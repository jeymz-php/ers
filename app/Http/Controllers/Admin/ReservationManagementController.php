<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Establishment;
use App\Models\Campus;
use App\Mail\ReservationStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReservationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'establishment', 'campus']);
        
        // Filter by status
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by campus
        if ($request->campus_id && $request->campus_id != 'all') {
            $query->where('campus_id', $request->campus_id);
        }
        
        // Search by client name or event name
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', function($user) use ($request) {
                    $user->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('event_name', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by date range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('event_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('event_date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('event_date', '<=', $request->end_date);
        }
        
        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);
        $campuses = Campus::all();
        
        return view('admin.reservations.index', compact('reservations', 'campuses'));
    }
    
    public function show($id)
    {
        $reservation = Reservation::with(['user', 'establishment', 'campus', 'approver'])->findOrFail($id);
        $remarks = json_decode($reservation->remarks, true);
        
        return view('admin.reservations.show', compact('reservation', 'remarks'));
    }

    public function edit($id)
    {
        $reservation = Reservation::with(['user', 'establishment', 'campus'])->findOrFail($id);
        $remarks = json_decode($reservation->remarks, true);
        $campuses = Campus::where('is_active', true)->with(['establishments' => function ($query) {
            $query->where('is_active', true)->orderBy('name');
        }])->orderBy('display_order')->get();

        return view('admin.reservations.edit', compact('reservation', 'remarks', 'campuses'));
    }

    public function create()
    {
        $campuses = Campus::where('is_active', true)->with(['establishments' => function ($query) {
            $query->where('is_active', true)->orderBy('name');
        }])->orderBy('display_order')->get();

        $users = User::whereIn('role', ['admin', 'super_admin'])
            ->where('account_status', 'approved')
            ->orderBy('name')
            ->get();

        return view('admin.reservations.create', compact('campuses', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'event_name' => 'required|string|max:200',
            'event_objectives' => 'nullable|string',
            'campus_id' => 'required|exists:campuses,id',
            'establishment_id' => 'required|exists:establishments,id',
            'event_dates' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'user_type' => 'required|in:student,professor,admin',
            'department' => 'nullable|string',
            'equipment' => 'nullable|string',
        ]);

        $establishment = Establishment::findOrFail($request->establishment_id);
        if ($establishment->campus_id != $request->campus_id) {
            return redirect()->back()->withErrors(['establishment_id' => 'The selected venue does not belong to the selected campus.'])->withInput();
        }

        $eventDates = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->event_dates)));
        if (empty($eventDates)) {
            return redirect()->back()->withErrors(['event_dates' => 'Please provide at least one event date.'])->withInput();
        }

        try {
            $normalizedDates = [];
            foreach ($eventDates as $date) {
                $normalizedDates[] = Carbon::parse($date)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['event_dates' => 'One or more dates are invalid. Use YYYY-MM-DD format.'])->withInput();
        }

        $equipment = [];
        if ($request->equipment) {
            $equipment = array_filter(array_map('trim', explode(',', $request->equipment)));
        }

        $conflicts = [];
        foreach ($normalizedDates as $eventDate) {
            $conflict = Reservation::where('establishment_id', $establishment->id)
                ->whereIn('status', ['approved', 'pending'])
                ->where('event_date', $eventDate)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($subQ) use ($request) {
                            $subQ->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })
                ->exists();

            if ($conflict) {
                $conflicts[] = $eventDate;
            }
        }

        if (!empty($conflicts)) {
            return redirect()->back()->withErrors(['event_dates' => 'The following dates are already booked for this venue: ' . implode(', ', $conflicts)])->withInput();
        }

        sort($normalizedDates);

        $reservation = Reservation::create([
            'user_id' => $request->user_id,
            'establishment_id' => $establishment->id,
            'campus_id' => $establishment->campus_id,
            'event_name' => $request->event_name,
            'description' => $request->event_objectives,
            'event_date' => $normalizedDates[0],
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'remarks' => json_encode([
                'user_type' => $request->user_type,
                'department' => $request->department,
                'equipment' => $equipment,
                'multiple_dates' => $normalizedDates,
                'is_multi_date' => count($normalizedDates) > 1,
            ]),
        ]);

        try {
            Mail::to($reservation->user->email)->send(new ReservationStatusMail($reservation, 'approved'));
        } catch (\Exception $e) {
            \Log::error('Failed to send reservation email after admin-created reservation: ' . $e->getMessage());
        }

        return redirect()->route('admin.reservations.show', $reservation->id)
            ->with('success', 'Reservation created and approved successfully. The user has been notified by email.');
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::with(['user', 'establishment', 'campus'])->findOrFail($id);
        $existingRemarks = json_decode($reservation->remarks, true) ?? [];

        $request->validate([
            'event_name' => 'required|string|max:200',
            'event_objectives' => 'nullable|string',
            'campus_id' => 'required|exists:campuses,id',
            'establishment_id' => 'required|exists:establishments,id',
            'event_dates' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'user_type' => 'required|in:student,professor',
            'department' => 'nullable|string',
            'equipment' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15360',
            'remove_attachments' => 'nullable|array',
        ]);

        $establishment = Establishment::findOrFail($request->establishment_id);
        if ($establishment->campus_id != $request->campus_id) {
            return redirect()->back()->withErrors(['establishment_id' => 'The selected venue does not belong to the selected campus.'])->withInput();
        }

        $eventDates = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->event_dates)));
        if (empty($eventDates)) {
            return redirect()->back()->withErrors(['event_dates' => 'Please provide at least one event date.'])->withInput();
        }

        try {
            $normalizedDates = [];
            foreach ($eventDates as $date) {
                $normalizedDates[] = \Carbon\Carbon::parse($date)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['event_dates' => 'One or more dates are invalid. Use YYYY-MM-DD format.'])->withInput();
        }

        $equipment = [];
        if ($request->equipment) {
            $equipment = array_filter(array_map('trim', explode(',', $request->equipment)));
        }

        // Check for time conflicts with other approved reservations for the same venue.
        $conflicts = [];
        foreach ($normalizedDates as $eventDate) {
            $conflict = Reservation::where('establishment_id', $establishment->id)
                ->where('status', 'approved')
                ->where('id', '!=', $reservation->id)
                ->where('event_date', $eventDate)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($subQ) use ($request) {
                            $subQ->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })
                ->exists();

            if ($conflict) {
                $conflicts[] = $eventDate;
            }
        }

        if (!empty($conflicts)) {
            return redirect()->back()->withErrors(['event_dates' => 'The following dates are already booked for this venue: ' . implode(', ', $conflicts)])->withInput();
        }

        $originalDates = $existingRemarks['multiple_dates'] ?? [$reservation->event_date];
        $originalEquipment = is_array($existingRemarks['equipment'] ?? null)
            ? $existingRemarks['equipment']
            : array_filter(array_map('trim', explode(',', $existingRemarks['equipment'] ?? '')));

        $normalizedOriginalDates = array_map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        }, $originalDates);
        sort($normalizedOriginalDates);
        sort($normalizedDates);

        // ── Attachments: fetch existing, remove any unchecked, append any
        // newly uploaded files.
        $existingAttachments = $existingRemarks['attachments'] ?? [];
        $finalAttachments = $existingAttachments;

        if ($request->filled('remove_attachments')) {
            foreach ($request->input('remove_attachments') as $toRemove) {
                Storage::disk('public')->delete($toRemove);
            }
            $finalAttachments = array_values(array_diff($finalAttachments, $request->input('remove_attachments')));
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('reservations/' . date('Y/m'), 'public');
                $finalAttachments[] = $path;
            }
        }

        $updatedFields = [];

        $fieldsToCompare = [
            'event_name' => ['label' => 'Event Name', 'old' => $reservation->event_name, 'new' => $request->event_name],
            'description' => ['label' => 'Objectives', 'old' => $reservation->description ?? '', 'new' => $request->event_objectives ?? ''],
            'campus' => ['label' => 'Campus', 'old' => $reservation->campus->name ?? '', 'new' => $establishment->campus->name ?? ''],
            'establishment' => ['label' => 'Venue', 'old' => $reservation->establishment->name ?? '', 'new' => $establishment->name],
            'event_dates' => [
                'label' => 'Event Dates',
                'old' => implode(', ', array_map(function ($date) { return \Carbon\Carbon::parse($date)->format('F d, Y'); }, $normalizedOriginalDates)),
                'new' => implode(', ', array_map(function ($date) { return \Carbon\Carbon::parse($date)->format('F d, Y'); }, $normalizedDates)),
            ],
            'start_time' => ['label' => 'Start Time', 'old' => \Carbon\Carbon::parse($reservation->start_time)->format('g:i A'), 'new' => \Carbon\Carbon::parse($request->start_time)->format('g:i A')],
            'end_time' => ['label' => 'End Time', 'old' => \Carbon\Carbon::parse($reservation->end_time)->format('g:i A'), 'new' => \Carbon\Carbon::parse($request->end_time)->format('g:i A')],
            'user_type' => ['label' => 'User Type', 'old' => $existingRemarks['user_type'] ?? 'N/A', 'new' => $request->user_type],
            'department' => ['label' => 'Department', 'old' => $existingRemarks['department'] ?? '', 'new' => $request->department ?? ''],
            'equipment' => ['label' => 'Equipment', 'old' => implode(', ', $originalEquipment), 'new' => implode(', ', $equipment)],
            'attachments' => [
                'label' => 'Attachments',
                'old' => count($existingAttachments) . ' file(s): ' . implode(', ', array_map('basename', $existingAttachments)),
                'new' => count($finalAttachments) . ' file(s): ' . implode(', ', array_map('basename', $finalAttachments)),
            ],
        ];

        foreach ($fieldsToCompare as $key => $field) {
            if (trim($field['old']) !== trim($field['new'])) {
                $updatedFields[$key] = [
                    'label' => $field['label'],
                    'old' => $field['old'] === '' ? 'None' : $field['old'],
                    'new' => $field['new'] === '' ? 'None' : $field['new'],
                ];
            }
        }

        $remarks = array_merge($existingRemarks, [
            'user_type' => $request->user_type,
            'department' => $request->department,
            'equipment' => $equipment,
            'multiple_dates' => $normalizedDates,
            'is_multi_date' => count($normalizedDates) > 1,
            'attachments' => $finalAttachments,
            'updated_fields' => $updatedFields,
            'last_revision_by' => auth()->user()->name,
            'last_revision_at' => now()->format('F d, Y h:i A'),
            'admin_notes' => 'Reservation details revised by ' . auth()->user()->name . ' on ' . now()->format('F d, Y h:i A'),
        ]);

        $reservation->update([
            'event_name' => $request->event_name,
            'description' => $request->event_objectives,
            'establishment_id' => $establishment->id,
            'campus_id' => $establishment->campus_id,
            'event_date' => $normalizedDates[0],
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'remarks' => json_encode($remarks),
        ]);

        // Send updated reservation email with revised report
        try {
            Mail::to($reservation->user->email)->send(new ReservationStatusMail($reservation->fresh(), 'updated'));
        } catch (\Exception $e) {
            \Log::error('Failed to send reservation update email: ' . $e->getMessage());
        }

        return redirect()->route('admin.reservations.show', $reservation->id)
            ->with('success', 'Reservation details updated successfully. The user has been notified by email.');
    }
    
    public function approve($id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $reservation->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'remarks' => json_encode(array_merge(
                json_decode($reservation->remarks, true) ?? [],
                ['admin_notes' => request('admin_notes')]
            ))
        ]);
        
        // Send email to user
        Mail::to($reservation->user->email)->send(new ReservationStatusMail($reservation, 'approved'));
        
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation approved successfully! Email sent to the user.');
    }
    
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);
        
        $reservation = Reservation::findOrFail($id);
        
        // Store rejection reason in remarks
        $remarks = json_decode($reservation->remarks, true) ?? [];
        $remarks['rejection_reason'] = $request->rejection_reason;
        $remarks['rejected_at'] = now()->toDateTimeString();
        $remarks['rejected_by'] = auth()->user()->name;
        
        $reservation->update([
            'status' => 'rejected',
            'remarks' => json_encode($remarks),
            'approved_at' => null,
            'approved_by' => null,
        ]);
        
        // Send rejection email to user
        try {
            Mail::to($reservation->user->email)->send(new ReservationStatusMail($reservation, 'rejected', $request->rejection_reason));
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }
        
        // Log the action
        \App\Models\AdminActionLog::create([
            'admin_id' => auth()->id(),
            'target_user_id' => $reservation->user_id,
            'action' => 'reject_reservation',
            'details' => "Rejected reservation #{$reservation->id}: {$reservation->event_name}. Reason: {$request->rejection_reason}",
        ]);
        
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation rejected successfully. Email sent to the user.');
    }
    
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
    
    public function bulkApprove(Request $request)
    {
        $ids = explode(',', $request->ids);
        $count = 0;
        
        foreach ($ids as $id) {
            $reservation = Reservation::find($id);
            if ($reservation && $reservation->status === 'pending') {
                $reservation->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id()
                ]);
                
                Mail::to($reservation->user->email)->send(new ReservationStatusMail($reservation, 'approved'));
                $count++;
            }
        }
        
        return redirect()->route('admin.reservations.index')
            ->with('success', "$count reservation(s) approved successfully.");
    }
}