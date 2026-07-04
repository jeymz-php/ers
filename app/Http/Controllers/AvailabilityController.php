<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Campus;
use App\Models\VehicleReservation;
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
                $multipleDates = $this->normalizeDates($remarks['multiple_dates'] ?? [], $res->event_date);
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
                $multipleDates = $this->normalizeDates($remarks['multiple_dates'] ?? [], $res->event_date);

                if (!in_array($date, $multipleDates, true)) {
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

    public function getVehicleAvailability(Request $request)
    {
        try {
            $campusId = $request->campus_id;
            $month = (int) $request->month;
            $year = (int) $request->year;

            $query = VehicleReservation::with(['originCampus', 'destinationCampus', 'user'])
                ->where('status', 'approved');

            if ($campusId && $campusId !== 'all') {
                $query->where('origin_campus_id', $campusId);
            }

            $reservations = $query->orderBy('trip_date')->get();
            $dates = [];

            foreach ($reservations as $reservation) {
                $dateKey = Carbon::parse($reservation->trip_date)->format('Y-m-d');

                if ($month && $year) {
                    $dateObj = Carbon::parse($reservation->trip_date);
                    if ($dateObj->month !== $month || $dateObj->year !== $year) {
                        continue;
                    }
                }

                if (!isset($dates[$dateKey])) {
                    $dates[$dateKey] = [];
                }

                $dates[$dateKey][] = [
                    'id' => $reservation->id,
                    'code' => $reservation->reservation_code,
                    'purpose' => $reservation->purpose_label,
                    'time' => Carbon::parse($reservation->pickup_time)->format('g:i A'),
                    'origin_campus' => $reservation->originCampus?->name ?? 'Unknown Campus',
                    'destination' => $reservation->destination_label,
                    'requestor' => $reservation->user?->name ?? 'Guest',
                ];
            }

            return response()->json([
                'success' => true,
                'dates' => $dates,
            ]);
        } catch (\Exception $e) {
            \Log::error('Public Vehicle Availability Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'dates' => [],
            ], 500);
        }
    }

    public function getVehicleDayAvailability(Request $request)
    {
        try {
            $date = $request->date;
            $campusId = $request->campus_id;

            $query = VehicleReservation::with(['originCampus', 'destinationCampus', 'user'])
                ->where('status', 'approved');

            if ($campusId && $campusId !== 'all') {
                $query->where('origin_campus_id', $campusId);
            }

            $reservations = $query->whereDate('trip_date', $date)->orderBy('pickup_time')->get();
            $items = [];

            foreach ($reservations as $reservation) {
                $items[] = [
                    'id' => $reservation->id,
                    'code' => $reservation->reservation_code,
                    'purpose' => $reservation->purpose_label,
                    'time' => Carbon::parse($reservation->pickup_time)->format('g:i A'),
                    'origin_campus' => $reservation->originCampus?->name ?? 'Unknown Campus',
                    'destination' => $reservation->destination_label,
                    'requestor' => $reservation->user?->name ?? 'Guest',
                ];
            }

            return response()->json([
                'success' => true,
                'reservations' => $items,
                'date' => Carbon::parse($date)->format('F d, Y'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Public Vehicle Day Availability Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'reservations' => [],
            ], 500);
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
