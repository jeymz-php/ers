<?php

namespace Tests\Unit;

use App\Models\VehicleReservation;
use Carbon\Carbon;
use Tests\TestCase;

class VehicleReservationTest extends TestCase
{
    public function test_vehicle_reservation_exposes_multiple_trip_dates(): void
    {
        $reservation = new VehicleReservation();
        $reservation->trip_date = Carbon::parse('2026-07-04');
        $reservation->remarks = json_encode([
            'is_multi_date' => true,
            'multiple_dates' => ['2026-07-04', '2026-07-05', '2026-07-06'],
        ]);

        $this->assertSame(['2026-07-04', '2026-07-05', '2026-07-06'], $reservation->getTripDatesAttribute());
    }

    public function test_conflicting_trip_dates_are_detected(): void
    {
        $existingReservations = [
            (object) ['trip_dates' => ['2026-07-04', '2026-07-05']],
            (object) ['trip_dates' => ['2026-07-07']],
        ];

        $conflicts = VehicleReservation::getConflictingTripDates(['2026-07-03', '2026-07-05', '2026-07-08'], $existingReservations);

        $this->assertSame(['2026-07-05'], $conflicts);
    }

    public function test_multi_date_display_lists_each_selected_date(): void
    {
        $reservation = new VehicleReservation();
        $reservation->trip_date = Carbon::parse('2026-07-23');
        $reservation->remarks = json_encode([
            'is_multi_date' => true,
            'multiple_dates' => ['2026-07-23', '2026-07-24', '2026-07-25'],
        ]);

        $this->assertSame('July 23, 2026, July 24, 2026, July 25, 2026', $reservation->trip_dates_display);
    }

    public function test_merge_remarks_preserves_existing_multi_date_data(): void
    {
        $reservation = new VehicleReservation();
        $reservation->remarks = json_encode([
            'is_multi_date' => true,
            'multiple_dates' => ['2026-07-23', '2026-07-24', '2026-07-25'],
        ]);

        $remarks = $reservation->mergeRemarks(['admin_notes' => 'Approved by admin']);

        $this->assertSame(['2026-07-23', '2026-07-24', '2026-07-25'], $remarks['multiple_dates']);
        $this->assertSame('Approved by admin', $remarks['admin_notes']);
    }
}
