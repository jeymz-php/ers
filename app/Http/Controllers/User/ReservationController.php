<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\Campus;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get only the user's campus
        $campuses = Campus::where('id', $user->campus_id)
            ->where('is_active', true)
            ->get();
        
        return view('user.reservations', compact('campuses'));
    }
    
    public function getEstablishmentsByCampus($campusId)
    {
        $user = Auth::user();
        
        if ($user->campus_id != $campusId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $campus = Campus::find($campusId);
        $establishments = Establishment::where('campus_id', $campusId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'campus' => $campus,
            'establishments' => $establishments
        ]);
    }
    
    public function showAvailability($id)
    {
        $establishment = Establishment::findOrFail($id);
        
        $reservations = Reservation::where('establishment_id', $id)
            ->where('status', 'approved')
            ->get(['event_date', 'start_time', 'end_time']);
        
        $bookedDates = [];
        foreach ($reservations as $res) {
            $date = $res->event_date;
            if (!isset($bookedDates[$date])) {
                $bookedDates[$date] = [];
            }
            $bookedDates[$date][] = [
                'start' => $res->start_time,
                'end' => $res->end_time
            ];
        }
        
        return response()->json([
            'success' => true,
            'establishment' => $establishment,
            'booked_dates' => $bookedDates
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'establishment_id' => 'required|exists:establishments,id',
                'event_name' => 'required|string|max:200',
                'event_objectives' => 'nullable|string',
                'event_dates' => 'required|json',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'user_type' => 'required|in:student,professor',
                'department' => 'nullable|string',
                'equipment' => 'nullable|json',
                'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15360'
            ]);

            $establishment = Establishment::findOrFail($request->establishment_id);
            $eventDates = json_decode($request->event_dates, true);
            $equipment = json_decode($request->equipment, true) ?? [];
            
            if ($establishment->campus_id != Auth::user()->campus_id) {
                return response()->json(['error' => 'You can only reserve establishments in your campus.'], 403);
            }

            // Check for conflicting reservations on ANY of the selected dates
            $conflicts = [];
            foreach ($eventDates as $eventDate) {
                $conflict = Reservation::where('establishment_id', $request->establishment_id)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($eventDate, $request) {
                        $query->where('event_date', $eventDate)
                            ->where(function ($q) use ($request) {
                                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                                    ->orWhere(function ($subQ) use ($request) {
                                        $subQ->where('start_time', '<=', $request->start_time)
                                            ->where('end_time', '>=', $request->end_time);
                                    });
                            });
                    })
                    ->exists();
                
                if ($conflict) {
                    $conflicts[] = $eventDate;
                }
            }
            
            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The following dates are already booked: ' . implode(', ', $conflicts)
                ], 400);
            }

            // Handle multiple file uploads
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('reservations/' . date('Y/m'), 'public');
                    $attachmentPaths[] = $path;
                }
            }

            // Sort dates for display
            sort($eventDates);
            
            // Create SINGLE reservation with multiple dates stored in remarks
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'establishment_id' => $request->establishment_id,
                'campus_id' => $establishment->campus_id,
                'event_name' => $request->event_name,
                'description' => $request->event_objectives,
                'event_date' => $eventDates[0], // Store first date as primary
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->is_approved ? 'approved' : 'pending',
                'remarks' => json_encode([
                    'user_type' => $request->user_type,
                    'department' => $request->department,
                    'equipment' => $equipment,
                    'attachments' => $attachmentPaths,
                    'multiple_dates' => $eventDates, // Store all dates here
                    'is_multi_date' => count($eventDates) > 1
                ])
            ]);
            
            if ($request->is_approved) {
                $reservation->update([
                    'approved_at' => now(),
                    'approved_by' => Auth::id()
                ]);
            }
            
            // Create SINGLE notification for admins
            $this->createNotification($reservation);
            
            $dateDisplay = count($eventDates) > 1 
                ? count($eventDates) . ' dates (' . date('M d', strtotime($eventDates[0])) . ' - ' . date('M d', strtotime(end($eventDates))) . ')'
                : date('M d, Y', strtotime($eventDates[0]));
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation submitted successfully! ' . $dateDisplay,
                'reservation' => $reservation
            ]);
            
        } catch (\Exception $e) {
            Log::error('Reservation creation error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function createNotification($reservation)
    {
        try {
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            
            if ($admins->isEmpty()) {
                return;
            }
            
            $userName = $reservation->user ? $reservation->user->name : 'Unknown User';
            $campusName = $reservation->campus ? $reservation->campus->name : 'Unknown Campus';
            
            $remarks = json_decode($reservation->remarks, true);
            $multipleDates = $remarks['multiple_dates'] ?? [$reservation->event_date];
            $isMultiDate = count($multipleDates) > 1;
            
            if ($isMultiDate) {
                $dateDisplay = count($multipleDates) . ' dates (' . date('M d', strtotime($multipleDates[0])) . ' - ' . date('M d', strtotime(end($multipleDates))) . ')';
                $message = "{$userName} requested '{$reservation->event_name}' at {$campusName} for {$dateDisplay}";
            } else {
                $dateDisplay = date('M d, Y', strtotime($reservation->event_date));
                $message = "{$userName} requested '{$reservation->event_name}' at {$campusName} on {$dateDisplay}";
            }
            
            $title = $isMultiDate ? '📅 New Multi-Date Reservation Request' : '📅 New Reservation Request';
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'reservation_id' => $reservation->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'reservation',
                    'is_read' => false,
                ]);
            }
            
            Log::info('Notification created for reservation ID: ' . $reservation->id);
            
        } catch (\Exception $e) {
            Log::error('Failed to create notifications: ' . $e->getMessage());
        }
    }
}