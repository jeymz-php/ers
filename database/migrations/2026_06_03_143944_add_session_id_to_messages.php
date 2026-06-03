<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('messages', 'session_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->foreignId('session_id')->nullable()->after('receiver_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('messages', 'session_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('session_id');
            });
        }
    }
};