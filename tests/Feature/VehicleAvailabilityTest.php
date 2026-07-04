<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\User;
use App\Models\VehicleReservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_vehicle_availability_endpoint_returns_reserved_dates(): void
    {
        $campus = Campus::create([
            'name' => 'Main Campus',
            'code' => 'MAIN',
            'address' => 'Test Address',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $user = User::factory()->create();

        $tripDate = Carbon::now()->startOfMonth()->addDays(5);

        VehicleReservation::create([
            'user_id' => $user->id,
            'requester_type' => 'student',
            'origin_campus_id' => $campus->id,
            'purpose' => 'transporting',
            'destination_type' => 'campus',
            'destination_campus_id' => $campus->id,
            'trip_date' => $tripDate->toDateString(),
            'pickup_time' => '08:00:00',
            'status' => 'approved',
        ]);

        $response = $this->getJson('/availability/vehicles?campus_id=' . $campus->id . '&month=' . $tripDate->month . '&year=' . $tripDate->year);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'dates.' . $tripDate->format('Y-m-d'));
    }
}
