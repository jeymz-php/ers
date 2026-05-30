<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Latest Reservations (all reservations sorted by created_at desc)
        $latestReservations = Reservation::where('user_id', $user->id)
            ->with(['establishment', 'campus'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'latest_page')
            ->withQueryString();
        
        // Active Reservations (approved and future dates)
        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('event_date', '>=', Carbon::today())
            ->with(['establishment', 'campus'])
            ->orderBy('event_date', 'asc')
            ->paginate(5, ['*'], 'active_page')
            ->withQueryString();
        
        // Reservation History (past events or rejected/cancelled)
        $historyReservations = Reservation::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('event_date', '<', Carbon::today())
                      ->orWhere('status', 'rejected')
                      ->orWhere('status', 'cancelled');
            })
            ->with(['establishment', 'campus'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'history_page')
            ->withQueryString();
        
        return view('user.summary', compact('latestReservations', 'activeReservations', 'historyReservations'));
    }
    
    public function getDetails($id)
    {
        $reservation = Reservation::with(['establishment', 'campus'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        // Format times properly
        $startTimeFormatted = \Carbon\Carbon::parse($reservation->start_time)->format('g:i A');
        $endTimeFormatted = \Carbon\Carbon::parse($reservation->end_time)->format('g:i A');
        
        return response()->json([
            'success' => true,
            'reservation' => [
                'id' => $reservation->id,
                'event_name' => $reservation->event_name,
                'description' => $reservation->description,
                'event_date' => $reservation->event_date,
                'start_time' => $startTimeFormatted,
                'end_time' => $endTimeFormatted,
                'status' => $reservation->status,
                'approved_at' => $reservation->approved_at,
                'created_at' => $reservation->created_at,
                'remarks' => $reservation->remarks,
                'establishment' => [
                    'name' => $reservation->establishment->name,
                    'capacity' => $reservation->establishment->capacity,
                    'type' => $reservation->establishment->type,
                ],
                'campus' => [
                    'name' => $reservation->campus->name,
                ],
            ]
        ]);
    }
}