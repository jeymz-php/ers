<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user')->after('campus_id');
            $table->enum('account_status', ['pending', 'approved', 'rejected'])->default('pending')->after('role');
            $table->text('rejection_reason')->nullable()->after('account_status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'account_status', 'rejection_reason', 'approved_at', 'approved_by']);
        });
    }
};