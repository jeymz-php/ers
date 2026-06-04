<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Block admin/super admin from accessing user dashboard
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        $user = Auth::user();
        $currentMonth = date('F');
        $currentYear = date('Y');
        $userCampusId = $user->campus_id;
        $userCampusName = $user->campus->name ?? 'Your Campus';
        
        return view('user.dashboard', compact('currentMonth', 'currentYear', 'userCampusId', 'userCampusName'));
    }
    
    public function getEvents(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'events' => []
                ], 401);
            }
            
            $query = Reservation::with(['establishment', 'campus'])
                ->where('status', 'approved')
                ->where('campus_id', $user->campus_id)
                ->orderBy('event_date')
                ->orderBy('start_time');

            $reservations = $query->get();
            
            $events = [];
            foreach ($reservations as $res) {
                // Get multiple dates from remarks
                $remarks = json_decode($res->remarks, true) ?: [];
                $multipleDates = $remarks['multiple_dates'] ?? [];
                if (!is_array($multipleDates) || count(array_filter($multipleDates)) === 0) {
                    $multipleDates = [$res->event_date];
                }
                $isMultiDate = count($multipleDates) > 1;
                
                // For EACH date in multipleDates, add the event to that date
                foreach ($multipleDates as $singleDate) {
                    $dateKey = Carbon::parse($singleDate)->format('Y-m-d');
                    
                    // Check if this date is within the current month view
                    if ($request->month && $request->year) {
                        $dateObj = Carbon::parse($singleDate);
                        if ($dateObj->month != $request->month || $dateObj->year != $request->year) {
                            continue; // Skip dates outside current month view
                        }
                    }
                    
                    if (!isset($events[$dateKey])) {
                        $events[$dateKey] = [];
                    }
                    
                    // Check if this event already exists on this date (avoid duplicates)
                    $exists = false;
                    foreach ($events[$dateKey] as $existing) {
                        if ($existing['id'] === $res->id) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if (!$exists) {
                        $events[$dateKey][] = [
                            'id' => $res->id,
                            'title' => $res->event_name,
                            'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                            'venue' => $res->establishment->name,
                            'campus' => $res->campus->name,
                            'start_time' => $res->start_time,
                            'end_time' => $res->end_time,
                            'is_multi_date' => $isMultiDate,
                            'multiple_dates' => $multipleDates
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'events' => $events,
                'campus' => $user->campus->name ?? 'Your Campus',
                'count' => $reservations->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('User Dashboard Events Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => []
            ], 500);
        }
    }
    
    public function getUpcomingEvents(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'events' => []
                ], 401);
            }
            
            $reservations = Reservation::with(['establishment', 'campus'])
                ->where('status', 'approved')
                ->where('campus_id', $user->campus_id)
                ->where('event_date', '>=', Carbon::today())
                ->orderBy('event_date')
                ->orderBy('start_time')
                ->limit(10)
                ->get();
            
            $events = [];
            foreach ($reservations as $res) {
                $remarks = json_decode($res->remarks, true) ?: [];
                $multipleDates = $remarks['multiple_dates'] ?? [];
                if (!is_array($multipleDates) || count(array_filter($multipleDates)) === 0) {
                    $multipleDates = [$res->event_date];
                }
                $isMultiDate = count($multipleDates) > 1;
                
                $events[] = [
                    'id' => $res->id,
                    'title' => $res->event_name,
                    'date' => Carbon::parse($res->event_date)->format('M d, Y'),
                    'day' => Carbon::parse($res->event_date)->format('l'),
                    'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                    'venue' => $res->establishment->name,
                    'campus' => $res->campus->name,
                    'is_multi_date' => $isMultiDate,
                    'multiple_dates' => $multipleDates
                ];
            }
            
            return response()->json([
                'success' => true,
                'events' => $events,
                'campus' => $user->campus->name ?? 'Your Campus'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('User Upcoming Events Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => []
            ], 500);
        }
    }
    
    public function getDayEvents(Request $request)
    {
        try {
            $user = Auth::user();
            $date = $request->date;
            
            $reservations = Reservation::with(['establishment', 'campus'])
                ->where('status', 'approved')
                ->where('campus_id', $user->campus_id)
                ->orderBy('start_time')
                ->get();
            
            $events = [];
            foreach ($reservations as $res) {
                $remarks = json_decode($res->remarks, true) ?: [];
                $multipleDates = $remarks['multiple_dates'] ?? [];
                if (!is_array($multipleDates) || count(array_filter($multipleDates)) === 0) {
                    $multipleDates = [$res->event_date];
                }
                $isMultiDate = count($multipleDates) > 1;
                
                if (!in_array($date, $multipleDates)) {
                    continue;
                }

                $events[] = [
                    'id' => $res->id,
                    'title' => $res->event_name,
                    'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                    'venue' => $res->establishment->name,
                    'campus' => $res->campus->name,
                    'start_time' => $res->start_time,
                    'end_time' => $res->end_time,
                    'is_multi_date' => $isMultiDate,
                    'multiple_dates' => $multipleDates
                ];
            }
            
            return response()->json([
                'success' => true,
                'events' => $events,
                'date' => Carbon::parse($date)->format('F d, Y'),
                'campus' => $user->campus->name ?? 'Your Campus'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('User Day Events Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => []
            ], 500);
        }
    }
}