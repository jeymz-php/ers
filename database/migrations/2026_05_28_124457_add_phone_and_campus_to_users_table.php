<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('email');
            $table->foreignId('campus_id')->nullable()->after('phone_number')
                  ->constrained('campuses')
                  ->nullOnDelete();
            $table->boolean('is_password_generated')->default(false)->after('password');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
            $table->dropColumn(['phone_number', 'campus_id', 'is_password_generated']);
        });
    }
};