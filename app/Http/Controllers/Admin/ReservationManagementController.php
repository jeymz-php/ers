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