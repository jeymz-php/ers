<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicle_reservations', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->after('origin_campus_id')
                  ->constrained('vehicles')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('vehicle_reservations', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
    }
};