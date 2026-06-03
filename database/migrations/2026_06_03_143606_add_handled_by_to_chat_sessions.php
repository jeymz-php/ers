<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Drop the old non-nullable foreign key constraint and column
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('chat_sessions', function (Blueprint $table) {
            // Re-add admin_id as nullable (no Doctrine DBAL needed)
            $table->foreignId('admin_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            // Add the new column
            $table->foreignId('is_handled_by_admin_id')->nullable()->after('admin_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropForeign(['is_handled_by_admin_id']);
            $table->dropColumn('is_handled_by_admin_id');
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });

        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
        });
    }
};