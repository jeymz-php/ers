<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop foreign key constraints first, then re-add as nullable
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropColumn(['sender_id', 'receiver_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->foreignId('receiver_id')->nullable()->after('sender_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropColumn(['sender_id', 'receiver_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('sender_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->after('sender_id')->constrained('users')->onDelete('cascade');
        });
    }
};
