<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('vehicle_reservation_id')->nullable()->after('reservation_id')
                  ->constrained('vehicle_reservations')->onDelete('cascade');
        });

        // Allow event-reservation notifications and vehicle-reservation notifications
        // to share the same table: reservation_id must become optional.
        // Raw SQL is used here so this doesn't require doctrine/dbal.
        DB::statement('ALTER TABLE notifications MODIFY reservation_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['vehicle_reservation_id']);
            $table->dropColumn('vehicle_reservation_id');
        });

        DB::statement('ALTER TABLE notifications MODIFY reservation_id BIGINT UNSIGNED NOT NULL');
    }
};