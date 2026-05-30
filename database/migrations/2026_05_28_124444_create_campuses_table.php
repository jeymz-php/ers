<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Insert default campuses
        DB::table('campuses')->insert([
            ['name' => 'Main Campus', 'code' => 'MC', 'address' => 'Caloocan City', 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Congressional Extension Campus', 'code' => 'CEC', 'address' => 'Barangay 171, Caloocan City', 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Camarin Extension Campus', 'code' => 'CAM', 'address' => 'Barangay 178, Caloocan City', 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bagong Silang Campus', 'code' => 'BS', 'address' => 'Barangay 176, Caloocan City', 'display_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('campuses');
    }
};