<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicle_reservations', function (Blueprint $table) {
            // A dedicated column for revision tracking — kept separate from
            // "remarks" (admin/rejection notes) and "trip_dates" (the actual
            // dates) so editing a reservation can never clobber either of
            // those, the same reasoning that led trip_dates to get its own
            // column earlier.
            $table->json('revision_info')->nullable()->after('remarks');
        });
    }

    public function down()
    {
        Schema::table('vehicle_reservations', function (Blueprint $table) {
            $table->dropColumn('revision_info');
        });
    }
};