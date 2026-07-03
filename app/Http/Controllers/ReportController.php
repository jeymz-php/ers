<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VehicleReservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function generateSingleReport($id)
    {
        $reservation = Reservation::with(['user', 'establishment', 'campus'])
            ->findOrFail($id);
        
        $remarks = json_decode($reservation->remarks, true);
        $equipment = $remarks['equipment'] ?? [];
        $userType = $remarks['user_type'] ?? 'N/A';
        $department = $remarks['department'] ?? 'N/A';
        $multipleDates = $remarks['multiple_dates'] ?? [$reservation->event_date];
        
        $data = [
            'reservation' => $reservation,
            'equipment' => $equipment,
            'userType' => $userType,
            'department' => $department,
            'multipleDates' => $multipleDates,
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
        ];
        
        $pdf = Pdf::loadView('reports.single-reservation', $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Use stream() to display in browser, not download
        return $pdf->stream('reservation_' . $reservation->id . '_' . date('Y-m-d') . '.pdf');
    }

    public function generateSingleVehicleReport($id)
    {
        $reservation = VehicleReservation::with(['user', 'originCampus', 'destinationCampus', 'approver'])
            ->findOrFail($id);

        $user = Auth::user();

        // Users may only view the report of their own pickup vehicle reservation.
        // Admin/Super Admin may view any.
        if (!$user->isAdmin() && $reservation->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this report.');
        }

        $data = [
            'reservation' => $reservation,
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.single-vehicle-reservation', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('vehicle_reservation_' . $reservation->id . '_' . date('Y-m-d') . '.pdf');
    }
    
    public function generateAllReport(Request $request)
    {
        $query = Reservation::with(['user', 'establishment', 'campus']);
        
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->campus_id && $request->campus_id != 'all') {
            $query->where('campus_id', $request->campus_id);
        }
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('event_date', [$request->start_date, $request->end_date]);
        }
        
        $reservations = $query->orderBy('created_at', 'desc')->get();
        
        $data = [
            'reservations' => $reservations,
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
            'filters' => [
                'status' => $request->status ?? 'all',
                'campus' => $request->campus_id ?? 'all',
                'date_range' => ($request->start_date && $request->end_date) 
                    ? $request->start_date . ' to ' . $request->end_date 
                    : 'All dates'
            ]
        ];
        
        $pdf = Pdf::loadView('reports.all-reservations', $data);
        $pdf->setPaper('A4', 'landscape');
        
        // Return PDF inline (opens in browser)
        return $pdf->stream('all_reservations_' . date('Y-m-d') . '.pdf');
    }
}