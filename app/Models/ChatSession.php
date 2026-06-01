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

    public static function isSessionActive($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }

    public static function getActiveSession($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    public static function startOrGetSession($userId, $adminId)
    {
        $session = self::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
        
        if (!$session) {
            $session = self::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'is_active' => true,
            ]);
        }
        
        return $session;
    }
    
    public static function getAvailableAdmins()
    {
        return User::whereIn('role', ['admin', 'super_admin'])->get();
    }
}