<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->foreignId('campus_id')->constrained('campuses')->onDelete('cascade');
            $table->integer('capacity');
            $table->enum('type', ['Indoor', 'Outdoor']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('establishments');
    }
};