<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicle_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('requester_type', ['student', 'professor', 'admin'])->default('student');

            // Origin
            $table->foreignId('origin_campus_id')->constrained('campuses')->onDelete('cascade');

            // Purpose
            $table->enum('purpose', ['transporting', 'delivery', 'other']);
            $table->string('other_purpose')->nullable();

            // Destination
            $table->enum('destination_type', ['campus', 'outside']);
            $table->foreignId('destination_campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->string('destination_location')->nullable();

            // Schedule
            $table->date('trip_date');
            $table->time('pickup_time');

            // Extra details
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();

            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_reservations');
    }
};