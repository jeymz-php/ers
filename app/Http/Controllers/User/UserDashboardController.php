<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\VehicleReservation;
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
                $multipleDates = $this->normalizeDates($remarks['multiple_dates'] ?? [], $res->event_date);
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
                            'type' => 'event',
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

            $this->mergeVehicleEvents($events, $user, (int) $request->month, (int) $request->year);
            
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
                $multipleDates = $this->normalizeDates($remarks['multiple_dates'] ?? [], $res->event_date);
                $isMultiDate = count($multipleDates) > 1;
                
                $events[] = [
                    'type' => 'event',
                    'id' => $res->id,
                    'title' => $res->event_name,
                    'date' => Carbon::parse($res->event_date)->format('M d, Y'),
                    'day' => Carbon::parse($res->event_date)->format('l'),
                    'event_date' => Carbon::parse($res->event_date)->format('Y-m-d'),
                    'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                    'venue' => $res->establishment->name,
                    'campus' => $res->campus->name,
                    'is_multi_date' => $isMultiDate,
                    'multiple_dates' => $multipleDates
                ];
            }

            $vehicleReservations = VehicleReservation::with(['originCampus', 'vehicle'])
                ->where('status', 'approved')
                ->where('origin_campus_id', $user->campus_id)
                ->where('trip_date', '>=', Carbon::today())
                ->orderBy('trip_date')
                ->limit(10)
                ->get();

            foreach ($vehicleReservations as $res) {
                $events[] = [
                    'type' => 'vehicle',
                    'id' => $res->id,
                    'title' => '🚐 ' . $res->purpose_label,
                    'date' => $res->trip_date->format('M d, Y'),
                    'day' => $res->trip_date->format('l'),
                    'event_date' => $res->trip_date->format('Y-m-d'),
                    'time' => Carbon::parse($res->pickup_time)->format('g:i A'),
                    'venue' => $res->destination_label,
                    'campus' => $res->originCampus?->name ?? 'Your Campus',
                    'vehicle' => $res->vehicle_label,
                    'is_multi_date' => $res->is_multi_date,
                    'multiple_dates' => $res->trip_dates,
                ];
            }

            usort($events, function ($a, $b) {
                return strcmp($a['event_date'] ?? '', $b['event_date'] ?? '');
            });

            $events = array_slice($events, 0, 10);
            
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
                $multipleDates = $this->normalizeDates($remarks['multiple_dates'] ?? [], $res->event_date);
                $isMultiDate = count($multipleDates) > 1;
                
                if (!in_array($date, $multipleDates, true)) {
                    continue;
                }

                $events[] = [
                    'type' => 'event',
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

            $vehicleReservations = VehicleReservation::with(['originCampus', 'vehicle'])
                ->where('status', 'approved')
                ->where('origin_campus_id', $user->campus_id)
                ->get();

            foreach ($vehicleReservations as $res) {
                if (!in_array($date, $res->trip_dates, true)) {
                    continue;
                }

                $events[] = [
                    'type' => 'vehicle',
                    'id' => $res->id,
                    'title' => '🚐 ' . $res->purpose_label,
                    'time' => Carbon::parse($res->pickup_time)->format('g:i A'),
                    'venue' => $res->destination_label,
                    'campus' => $res->originCampus?->name ?? 'Your Campus',
                    'vehicle' => $res->vehicle_label,
                    'is_multi_date' => $res->is_multi_date,
                    'multiple_dates' => $res->trip_dates,
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

    /**
     * Merge approved pickup vehicle reservations (for the user's own campus)
     * into the same date-keyed events array used by the event calendar.
     */
    private function mergeVehicleEvents(array &$events, $user, int $month, int $year): void
    {
        $vehicleReservations = VehicleReservation::with(['originCampus', 'vehicle'])
            ->where('status', 'approved')
            ->where('origin_campus_id', $user->campus_id)
            ->get();

        foreach ($vehicleReservations as $res) {
            foreach ($res->trip_dates as $singleDate) {
                if ($month && $year) {
                    $dateObj = Carbon::parse($singleDate);
                    if ($dateObj->month != $month || $dateObj->year != $year) {
                        continue;
                    }
                }

                $dateKey = Carbon::parse($singleDate)->format('Y-m-d');

                if (!isset($events[$dateKey])) {
                    $events[$dateKey] = [];
                }

                $events[$dateKey][] = [
                    'type' => 'vehicle',
                    'id' => $res->id,
                    'title' => '🚐 ' . $res->purpose_label,
                    'time' => Carbon::parse($res->pickup_time)->format('g:i A'),
                    'venue' => $res->destination_label,
                    'campus' => $res->originCampus?->name ?? 'Your Campus',
                    'vehicle' => $res->vehicle_label,
                    'is_multi_date' => $res->is_multi_date,
                    'multiple_dates' => $res->trip_dates,
                ];
            }
        }
    }

    private function normalizeDates($dates, $defaultDate): array
    {
        $normalized = [];

        if (!is_array($dates)) {
            return [$defaultDate];
        }

        foreach ($dates as $date) {
            try {
                $parsed = Carbon::parse($date)->format('Y-m-d');
                $normalized[] = $parsed;
            } catch (\Exception $e) {
                continue;
            }
        }

        if (empty($normalized) && $defaultDate) {
            try {
                $normalized[] = Carbon::parse($defaultDate)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        return array_values(array_unique($normalized));
    }
}