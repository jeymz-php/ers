<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
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
        
        $data = [
            'reservation' => $reservation,
            'equipment' => $equipment,
            'userType' => $userType,
            'department' => $department,
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
        ];
        
        $pdf = Pdf::loadView('reports.single-reservation', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('reservation_' . $reservation->id . '_' . date('Y-m-d') . '.pdf');
    }
    
    public function generateAllReport(Request $request)
    {
        $query = Reservation::with(['user', 'establishment', 'campus']);
        
        // Apply filters
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
        
        return $pdf->download('all_reservations_' . date('Y-m-d') . '.pdf');
    }
}