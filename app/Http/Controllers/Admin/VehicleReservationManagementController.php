<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VehicleReservationStatusMail;
use App\Models\AdminActionLog;
use App\Models\Campus;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class VehicleReservationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleReservation::with(['user', 'originCampus', 'destinationCampus', 'vehicle']);

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->campus_id && $request->campus_id != 'all') {
            $query->where('origin_campus_id', $request->campus_id);
        }

        if ($request->purpose && $request->purpose != 'all') {
            $query->where('purpose', $request->purpose);
        }

        if ($request->search) {
            $query->whereHas('user', function ($user) use ($request) {
                $user->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('trip_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('trip_date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('trip_date', '<=', $request->end_date);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 10)->withQueryString();
        $campuses = Campus::all();

        return view('admin.vehicle-reservations.index', compact('reservations', 'campuses'));
    }

    public function show($id)
    {
        $reservation = VehicleReservation::with(['user', 'originCampus', 'destinationCampus', 'approver', 'vehicle'])->findOrFail($id);

        return view('admin.vehicle-reservations.show', compact('reservation'));
    }

    public function edit($id)
    {
        $reservation = VehicleReservation::with(['user', 'originCampus', 'destinationCampus', 'vehicle'])->findOrFail($id);
        $campuses = Campus::where('is_active', true)->orderBy('display_order')->get();

        return view('admin.vehicle-reservations.edit', compact('reservation', 'campuses'));
    }

    public function update(Request $request, $id)
    {
        $reservation = VehicleReservation::with(['originCampus', 'destinationCampus', 'vehicle'])->findOrFail($id);

        $request->validate([
            'requester_type' => 'required|in:student,professor,admin',
            'origin_campus_id' => 'required|exists:campuses,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'purpose' => 'required|in:transporting,delivery,other',
            'other_purpose' => 'required_if:purpose,other|nullable|string|max:255',
            'destination_type' => 'required|in:campus,outside',
            'destination_campus_id' => 'required_if:destination_type,campus|nullable|exists:campuses,id',
            'destination_location' => 'required_if:destination_type,outside|nullable|string|max:255',
            'trip_date' => 'required|date',
            'trip_dates' => 'nullable|array',
            'trip_dates.*' => 'required|date',
            'pickup_time' => 'required',
            'notes' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15360',
            'remove_attachments' => 'nullable|array',
        ]);

        $tripDates = VehicleReservation::normalizeTripDates(
            $request->input('trip_dates', []),
            $request->trip_date
        );

        // Conflict check against OTHER approved reservations only — never
        // block the reservation from keeping the dates it already has.
        $approvedReservations = VehicleReservation::where('status', 'approved')
            ->where('id', '!=', $reservation->id)
            ->get();
        $conflictingDates = VehicleReservation::getConflictingTripDates($tripDates, $approvedReservations);

        if (!empty($conflictingDates)) {
            return back()->withInput()->withErrors([
                'trip_dates' => 'The following dates are already reserved for pickup vehicle: ' . implode(', ', $conflictingDates),
            ]);
        }

        $attachmentPaths = $reservation->attachments ?? [];

        // Remove any attachments the admin unchecked
        if ($request->filled('remove_attachments')) {
            foreach ($request->input('remove_attachments') as $toRemove) {
                Storage::disk('public')->delete($toRemove);
            }
            $attachmentPaths = array_values(array_diff($attachmentPaths, $request->input('remove_attachments')));
        }

        // Append any newly uploaded attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('vehicle-reservations/' . date('Y/m'), 'public');
                $attachmentPaths[] = $path;
            }
        }

        // ── Build a before/after diff so the report can show a "REVISED"
        // version, the same way event reservations already do. This is
        // computed BEFORE $reservation->update() runs, while its attributes
        // still hold the old values.
        $requesterTypeLabel = fn ($type) => match ($type) {
            'student' => 'Student',
            'professor' => 'Professor',
            'admin' => 'Administrator',
            default => ucfirst((string) $type),
        };

        $newOriginCampus = Campus::find($request->origin_campus_id);
        $newVehicle = $request->vehicle_id ? Vehicle::find($request->vehicle_id) : null;
        $newDestinationCampus = $request->destination_type === 'campus' && $request->destination_campus_id
            ? Campus::find($request->destination_campus_id)
            : null;

        $newPurposeLabel = $request->purpose === 'other'
            ? ($request->other_purpose ?: 'Other')
            : ($request->purpose === 'transporting' ? 'Transporting' : 'Items Delivery');

        $newDestinationLabel = $request->destination_type === 'campus'
            ? ($newDestinationCampus->name ?? 'N/A')
            : ($request->destination_location ?: 'N/A');

        $formatDates = fn ($dates) => implode(', ', array_map(function ($d) {
            return \Carbon\Carbon::parse($d)->format('F d, Y');
        }, $dates));

        $fieldsToCompare = [
            'requester_type' => [
                'label' => 'Requester Type',
                'old' => $requesterTypeLabel($reservation->requester_type),
                'new' => $requesterTypeLabel($request->requester_type),
            ],
            'origin_campus' => [
                'label' => 'Origin Campus',
                'old' => $reservation->originCampus->name ?? 'N/A',
                'new' => $newOriginCampus->name ?? 'N/A',
            ],
            'vehicle' => [
                'label' => 'Assigned Vehicle',
                'old' => $reservation->vehicle_label,
                'new' => $newVehicle?->label ?? 'Not yet assigned',
            ],
            'purpose' => [
                'label' => 'Purpose',
                'old' => $reservation->purpose_label,
                'new' => $newPurposeLabel,
            ],
            'destination' => [
                'label' => 'Destination',
                'old' => $reservation->destination_label,
                'new' => $newDestinationLabel,
            ],
            'trip_dates' => [
                'label' => 'Trip Date(s)',
                'old' => $formatDates($reservation->trip_dates),
                'new' => $formatDates($tripDates),
            ],
            'pickup_time' => [
                'label' => 'Pickup Time',
                'old' => \Carbon\Carbon::parse($reservation->pickup_time)->format('g:i A'),
                'new' => \Carbon\Carbon::parse($request->pickup_time)->format('g:i A'),
            ],
            'notes' => [
                'label' => 'Additional Details',
                'old' => $reservation->notes ?: 'None',
                'new' => $request->notes ?: 'None',
            ],
        ];

        $updatedFields = [];
        foreach ($fieldsToCompare as $key => $field) {
            if (trim((string) $field['old']) !== trim((string) $field['new'])) {
                $updatedFields[$key] = $field;
            }
        }

        $updateData = [
            'requester_type' => $request->requester_type,
            'origin_campus_id' => $request->origin_campus_id,
            'vehicle_id' => $request->vehicle_id,
            'purpose' => $request->purpose,
            'other_purpose' => $request->purpose === 'other' ? $request->other_purpose : null,
            'destination_type' => $request->destination_type,
            'destination_campus_id' => $request->destination_type === 'campus' ? $request->destination_campus_id : null,
            'destination_location' => $request->destination_type === 'outside' ? $request->destination_location : null,
            'trip_date' => $tripDates[0],
            'trip_dates' => $tripDates,
            'pickup_time' => $request->pickup_time,
            'notes' => $request->notes,
            'attachments' => $attachmentPaths,
        ];

        if (!empty($updatedFields)) {
            $updateData['revision_info'] = [
                'updated_fields' => $updatedFields,
                'last_revision_by' => Auth::user()->name,
                'last_revision_at' => now()->format('F d, Y h:i A'),
            ];
        }

        $reservation->update($updateData);

        return redirect()->route('admin.vehicle-reservations.show', $reservation->id)
            ->with('success', 'Pickup vehicle reservation updated successfully.');
    }

    public function create()
    {
        $campuses = Campus::where('is_active', true)->orderBy('display_order')->get();
        $users = User::where('account_status', 'approved')->orderBy('name')->get();

        return view('admin.vehicle-reservations.create', compact('campuses', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'requester_type' => 'required|in:student,professor,admin',
            'origin_campus_id' => 'required|exists:campuses,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'purpose' => 'required|in:transporting,delivery,other',
            'other_purpose' => 'required_if:purpose,other|nullable|string|max:255',
            'destination_type' => 'required|in:campus,outside',
            'destination_campus_id' => 'required_if:destination_type,campus|nullable|exists:campuses,id',
            'destination_location' => 'required_if:destination_type,outside|nullable|string|max:255',
            'trip_date' => 'required|date',
            'trip_dates' => 'nullable|array',
            'trip_dates.*' => 'required|date',
            'pickup_time' => 'required',
            'notes' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15360',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('vehicle-reservations/' . date('Y/m'), 'public');
                $attachmentPaths[] = $path;
            }
        }

        $tripDates = VehicleReservation::normalizeTripDates(
            $request->input('trip_dates', []),
            $request->trip_date
        );

        $approvedReservations = VehicleReservation::where('status', 'approved')->get();
        $conflictingDates = VehicleReservation::getConflictingTripDates($tripDates, $approvedReservations);

        if (!empty($conflictingDates)) {
            return back()->withInput()->withErrors([
                'trip_dates' => 'The following dates are already reserved for pickup vehicle: ' . implode(', ', $conflictingDates),
            ]);
        }

        $reservation = VehicleReservation::create([
            'user_id' => $request->user_id,
            'requester_type' => $request->requester_type,
            'origin_campus_id' => $request->origin_campus_id,
            'vehicle_id' => $request->vehicle_id,
            'purpose' => $request->purpose,
            'other_purpose' => $request->purpose === 'other' ? $request->other_purpose : null,
            'destination_type' => $request->destination_type,
            'destination_campus_id' => $request->destination_type === 'campus' ? $request->destination_campus_id : null,
            'destination_location' => $request->destination_type === 'outside' ? $request->destination_location : null,
            'trip_date' => $tripDates[0],
            'trip_dates' => $tripDates,
            'pickup_time' => $request->pickup_time,
            'notes' => $request->notes,
            'attachments' => $attachmentPaths,
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        try {
            Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus', 'approver', 'vehicle']), 'approved'));
        } catch (\Exception $e) {
            Log::error('Failed to send vehicle reservation approved email: ' . $e->getMessage());
        }

        return redirect()->route('admin.vehicle-reservations.show', $reservation->id)
            ->with('success', 'Pickup vehicle reservation created and approved successfully.');
    }

    public function approve($id)
    {
        $reservation = VehicleReservation::findOrFail($id);

        // Note: trip_dates is intentionally never touched here. Approving a
        // reservation must never be able to alter the dates that were
        // originally requested.
        $updateData = [
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ];

        $adminNotes = request('admin_notes');
        if (!empty($adminNotes)) {
            $updateData['remarks'] = $adminNotes;
        }

        $reservation->update($updateData);

        try {
            Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus', 'approver', 'vehicle']), 'approved'));
        } catch (\Exception $e) {
            Log::error('Failed to send vehicle reservation approved email: ' . $e->getMessage());
        }

        return redirect()->route('admin.vehicle-reservations.index')
            ->with('success', 'Pickup vehicle reservation approved successfully. Email sent to the requester.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $reservation = VehicleReservation::findOrFail($id);

        // Note: trip_dates is intentionally never touched here either.
        $reservation->update([
            'status' => 'rejected',
            'remarks' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        try {
            Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus', 'vehicle']), 'rejected', $request->rejection_reason));
        } catch (\Exception $e) {
            Log::error('Failed to send vehicle reservation rejected email: ' . $e->getMessage());
        }

        try {
            AdminActionLog::create([
                'admin_id' => Auth::id(),
                'target_user_id' => $reservation->user_id,
                'action' => 'reject_vehicle_reservation',
                'details' => "Rejected pickup vehicle reservation #{$reservation->id}. Reason: {$request->rejection_reason}",
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log vehicle reservation rejection: ' . $e->getMessage());
        }

        return redirect()->route('admin.vehicle-reservations.index')
            ->with('success', 'Pickup vehicle reservation rejected successfully. Email sent to the requester.');
    }

    public function destroy($id)
    {
        $reservation = VehicleReservation::findOrFail($id);
        $reservation->delete();

        return redirect()->route('admin.vehicle-reservations.index')
            ->with('success', 'Pickup vehicle reservation deleted successfully.');
    }
}