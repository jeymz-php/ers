<?php

namespace App\Http\Controllers;

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

        return view('public.availability', compact('campuses', 'selectedCampus'));
    }

    public function getEvents(Request $request)
    {
        try {
            $campusId = $request->campus_id;
            $month = (int) $request->month;
            $year = (int) $request->year;

            $query = Reservation::with(['establishment', 'campus', 'user'])
                ->where('status', 'approved');

            if ($campusId && $campusId !== 'all') {
                $query->where('campus_id', $campusId);
            }

            $reservations = $query->orderBy('event_date')->orderBy('start_time')->get();
            $events = [];

            foreach ($reservations as $res) {
                $remarks = json_decode($res->remarks, true) ?: [];
                $multipleDates = $remarks['multiple_dates'] ?? [];
                if (!is_array($multipleDates) || count(array_filter($multipleDates)) === 0) {
                    $multipleDates = [$res->event_date];
                }

                $isMultiDate = count($multipleDates) > 1;

                foreach ($multipleDates as $singleDate) {
                    $dateKey = Carbon::parse($singleDate)->format('Y-m-d');

                    if ($month && $year) {
                        $dateObj = Carbon::parse($singleDate);
                        if ($dateObj->month !== $month || $dateObj->year !== $year) {
                            continue;
                        }
                    }

                    if (!isset($events[$dateKey])) {
                        $events[$dateKey] = [];
                    }

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
                            'venue' => $res->establishment?->name ?? 'TBA',
                            'campus' => $res->campus?->name ?? 'Unknown Campus',
                            'campus_id' => $res->campus_id,
                            'requestor' => $res->user?->name ?? 'Guest',
                            'is_multi_date' => $isMultiDate,
                            'multiple_dates' => $multipleDates,
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            \Log::error('Public Availability Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => [],
            ], 500);
        }
    }

    public function getDayEvents(Request $request)
    {
        try {
            $date = $request->date;
            $campusId = $request->campus_id;

            $query = Reservation::with(['establishment', 'campus', 'user'])
                ->where('status', 'approved');

            if ($campusId && $campusId !== 'all') {
                $query->where('campus_id', $campusId);
            }

            $reservations = $query->orderBy('start_time')->get();
            $events = [];

            foreach ($reservations as $res) {
                $remarks = json_decode($res->remarks, true) ?: [];
                $multipleDates = $remarks['multiple_dates'] ?? [];
                if (!is_array($multipleDates) || count(array_filter($multipleDates)) === 0) {
                    $multipleDates = [$res->event_date];
                }

                if (!in_array($date, $multipleDates)) {
                    continue;
                }

                $events[] = [
                    'id' => $res->id,
                    'title' => $res->event_name,
                    'time' => Carbon::parse($res->start_time)->format('g:i A') . ' - ' . Carbon::parse($res->end_time)->format('g:i A'),
                    'venue' => $res->establishment?->name ?? 'TBA',
                    'campus' => $res->campus?->name ?? 'Unknown Campus',
                    'requestor' => $res->user?->name ?? 'Guest',
                    'is_multi_date' => count($multipleDates) > 1,
                    'multiple_dates' => $multipleDates,
                ];
            }

            return response()->json([
                'success' => true,
                'events' => $events,
                'date' => Carbon::parse($date)->format('F d, Y'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Public Day Events Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => [],
            ], 500);
        }
    }
}
