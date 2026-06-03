<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'handled_by_admin_id',
        'is_active',
        'ended_at',
        'closing_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by_admin_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public static function getActiveSession($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    public static function createNewSession($userId)
    {
        // Close any existing active session
        self::where('user_id', $userId)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);

        return self::create([
            'user_id' => $userId,
            'is_active' => true,
        ]);
    }
}