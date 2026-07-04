<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicle_reservations', function (Blueprint $table) {
            $table->json('trip_dates')->nullable()->after('trip_date');
        });

        // Previously, the selected multi-dates for a pickup vehicle reservation
        // were smuggled inside the shared "remarks" text column as JSON. Because
        // "remarks" was ALSO overwritten with plain admin/rejection notes during
        // approve/reject, saving a note (or even an empty one) could wipe out the
        // multi-date data — which is exactly the "3 days became 1 day after
        // approval" bug. This migration moves the dates into their own dedicated
        // column so they can never be touched by a remarks update again, and
        // restores "remarks" to plain text for existing rows.
        $rows = DB::table('vehicle_reservations')->get();

        foreach ($rows as $row) {
            $decoded = json_decode((string) $row->remarks, true);
            $tripDates = [];
            $cleanRemarks = null;

            if (is_array($decoded)) {
                if (!empty($decoded['multiple_dates']) && is_array($decoded['multiple_dates'])) {
                    foreach ($decoded['multiple_dates'] as $date) {
                        try {
                            $tripDates[] = Carbon::parse($date)->format('Y-m-d');
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                $cleanRemarks = $decoded['rejection_reason'] ?? $decoded['admin_notes'] ?? null;
            } elseif (!empty($row->remarks)) {
                // remarks was already plain text (not our JSON scheme) — keep it
                $cleanRemarks = $row->remarks;
            }

            if (empty($tripDates)) {
                $tripDates = $row->trip_date ? [Carbon::parse($row->trip_date)->format('Y-m-d')] : [];
            }

            $tripDates = array_values(array_unique($tripDates));
            sort($tripDates);

            DB::table('vehicle_reservations')
                ->where('id', $row->id)
                ->update([
                    'trip_dates' => json_encode($tripDates),
                    'remarks' => $cleanRemarks,
                ]);
        }
    }

    public function down()
    {
        Schema::table('vehicle_reservations', function (Blueprint $table) {
            $table->dropColumn('trip_dates');
        });
    }
};