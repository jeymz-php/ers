// Migration
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamp('ended_at')->nullable();
            $table->text('closing_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_sessions');
    }
};