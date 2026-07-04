<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\VehicleReservationStatusMail;
use App\Models\Campus;
use App\Models\Notification;
use App\Models\User;
use App\Models\VehicleReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VehicleReservationController extends Controller
{
    public function index()
    {
        $campuses = Campus::getActiveCampuses();

        $myReservations = VehicleReservation::with(['originCampus', 'destinationCampus'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('user.vehicle-reservation', compact('campuses', 'myReservations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'requester_type' => 'required|in:student,professor',
            'origin_campus_id' => 'required|exists:campuses,id',
            'purpose' => 'required|in:transporting,delivery,other',
            'other_purpose' => 'required_if:purpose,other|nullable|string|max:255',
            'destination_type' => 'required|in:campus,outside',
            'destination_campus_id' => 'required_if:destination_type,campus|nullable|exists:campuses,id',
            'destination_location' => 'required_if:destination_type,outside|nullable|string|max:255',
            'trip_date' => 'required|date|after_or_equal:today',
            'trip_dates' => 'nullable|array',
            'trip_dates.*' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required',
            'notes' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15360',
        ]);

        try {
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
                'user_id' => Auth::id(),
                'requester_type' => $request->requester_type,
                'origin_campus_id' => $request->origin_campus_id,
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
                'status' => 'pending',
            ]);

            $this->notifyAdmins($reservation);

            try {
                Mail::to($reservation->user->email)->send(new VehicleReservationStatusMail($reservation->fresh(['user', 'originCampus', 'destinationCampus']), 'pending'));
            } catch (\Exception $e) {
                Log::error('Failed to send vehicle reservation pending email: ' . $e->getMessage());
            }

            return redirect()->route('user.vehicle-reservations')
                ->with('success', 'Pickup vehicle reservation submitted! Please wait for Admin/Super Admin approval.');
        } catch (\Exception $e) {
            Log::error('Vehicle reservation creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while submitting your request. Please try again.');
        }
    }

    private function notifyAdmins(VehicleReservation $reservation)
    {
        try {
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

            if ($admins->isEmpty()) {
                return;
            }

            $userName = $reservation->user ? $reservation->user->name : 'Unknown User';
            $campusName = $reservation->originCampus ? $reservation->originCampus->name : 'Unknown Campus';
            $dateDisplay = $reservation->trip_date->format('M d, Y');

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'vehicle_reservation_id' => $reservation->id,
                    'title' => '🚐 New Pickup Vehicle Request',
                    'message' => "{$userName} requested a pickup vehicle from {$campusName} on {$dateDisplay}",
                    'type' => 'vehicle_reservation',
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create vehicle reservation notifications: ' . $e->getMessage());
        }
    }
}