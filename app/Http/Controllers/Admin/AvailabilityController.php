<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Campus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $campuses = Campus::where('is_active', true)->get();
        $selectedCampus = $request->campus_id ?? 'all';
        
        return view('admin.availability.index', compact('campuses', 'selectedCampus'));
    }
    
    public function getEvents(Request $request)
    {
        try {
            $query = Reservation::with(['establishment', 'campus', 'user'])
                ->where('status', 'approved');
            
            // Filter by campus
            if ($request->campus_id && $request->campus_id != 'all') {
                $query->where('campus_id', $request->campus_id);
            }
            
            // Filter by month/year
            if ($request->month && $request->year) {
                $year = (int)$request->year;
                $month = (int)$request->month;
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $query->whereBetween('event_date', [$startDate, $endDate]);
            }
            
            $reservations = $query->orderBy('event_date')->orderBy('start_time')->get();
            
            $events = [];
            foreach ($reservations as $res) {
                // Get multiple dates from remarks
                $remarks = json_decode($res->remarks, true);
                $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
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
                            'campus_id' => $res->campus_id,
                            'requestor' => $res->user->name,
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
                'count' => $reservations->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Admin Availability Error: ' . $e->getMessage());
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
            $date = $request->date;
            
            $query = Reservation::with(['establishment', 'campus', 'user'])
                ->where('status', 'approved')
                ->where('event_date', $date);
            
            if ($request->campus_id && $request->campus_id != 'all') {
                $query->where('campus_id', $request->campus_id);
            }
            
            $reservations = $query->orderBy('start_time')->get();
            
            $events = [];
            foreach ($reservations as $res) {
                $remarks = json_decode($res->remarks, true);
                $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
                $isMultiDate = count($multipleDates) > 1;
                
                $events[] = [
                    'id' => $res->id,
                    'title' => $res->event_name,
                    'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                    'venue' => $res->establishment->name,
                    'campus' => $res->campus->name,
                    'requestor' => $res->user->name,
                    'start_time' => $res->start_time,
                    'end_time' => $res->end_time,
                    'is_multi_date' => $isMultiDate,
                    'multiple_dates' => $multipleDates
                ];
            }
            
            return response()->json([
                'success' => true,
                'events' => $events,
                'date' => Carbon::parse($date)->format('F d, Y')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Admin Day Events Error: ' . $e->getMessage());
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
            $query = Reservation::with(['establishment', 'campus', 'user'])
                ->where('status', 'approved')
                ->where('event_date', '>=', Carbon::today());
            
            if ($request->campus_id && $request->campus_id != 'all') {
                $query->where('campus_id', $request->campus_id);
            }
            
            $reservations = $query->orderBy('event_date')->orderBy('start_time')->limit(10)->get();
            
            $events = [];
            foreach ($reservations as $res) {
                $remarks = json_decode($res->remarks, true);
                $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
                $isMultiDate = count($multipleDates) > 1;
                
                $events[] = [
                    'id' => $res->id,
                    'title' => $res->event_name,
                    'date' => Carbon::parse($res->event_date)->format('M d, Y'),
                    'day' => Carbon::parse($res->event_date)->format('l'),
                    'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                    'venue' => $res->establishment->name,
                    'campus' => $res->campus->name,
                    'requestor' => $res->user->name,
                    'is_multi_date' => $isMultiDate,
                    'multiple_dates' => $multipleDates
                ];
            }
            
            return response()->json([
                'success' => true,
                'events' => $events
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Admin Upcoming Events Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => []
            ], 500);
        }
    }
}