<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VehicleReservationStatusMail;
use App\Models\AdminActionLog;
use App\Models\Campus;
use App\Models\User;
use App\Models\VehicleReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VehicleReservationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleReservation::with(['user', 'originCampus', 'destinationCampus']);

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
        $reservation = VehicleReservation::with(['user', 'originCampus', 'destinationCampus', 'approver'])->findOrFail($id);

        return view('admin.vehicle-reservations.show', compact('reservation'));
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
            'purpose' => 'required|in:transporting,delivery,other',
            'other_purpose' => 'required_if:purpose,other|nullable|string|max:255',
            'destination_type' => 'required|in:campus,outside',
            'destination_campus_id' => 'required_if:destination_type,campus|nullable|exists:campuses,id',
            'destination_location' => 'required_if:destination_type,outside|nullable|string|max:255',
            'trip_date' => 'required|date',
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

        $reservation = VehicleReservation::create([
            'user_id' => $request->user_id,
            'requester_type' => $request->requester_type,
            'origin_campus_id' => $request->origin_campus_id,
            'purpose' => $request->purpose,
            'other_purpose' => $request->purpose === 'other' ? $request->other_purpose : null,
            'destination_type' => $request->destination_type,
            'destination_campus_id' => $request->destination_type === 'campus' ? $request->destination_campus_id : null,
            'destination_location' => $request->destination_type === 'outside' ? $request->destination_location : null,
            'trip_date' => $request->trip_date,
            'pickup_time' => $request->pickup_time,
            'notes' => $request->notes,
            'attachments' => $attachmentPaths,
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        try {
            Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus', 'approver']), 'approved'));
        } catch (\Exception $e) {
            Log::error('Failed to send vehicle reservation approved email: ' . $e->getMessage());
        }

        return redirect()->route('admin.vehicle-reservations.show', $reservation->id)
            ->with('success', 'Pickup vehicle reservation created and approved successfully.');
    }

    public function approve($id)
    {
        $reservation = VehicleReservation::findOrFail($id);

        $reservation->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'remarks' => request('admin_notes'),
        ]);

        try {
            Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus', 'approver']), 'approved'));
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

        $reservation->update([
            'status' => 'rejected',
            'remarks' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        try {
            Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus']), 'rejected', $request->rejection_reason));
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