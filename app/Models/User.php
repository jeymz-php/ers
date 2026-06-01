<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'campus_id',
        'is_password_generated',
        'role',
        'account_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_password_generated' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adminLogs()
    {
        return $this->hasMany(AdminActionLog::class, 'admin_id');
    }

    // Role Checks
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    // Account Status Checks
    public function isPending()
    {
        return $this->account_status === 'pending';
    }

    public function isApproved()
    {
        return $this->account_status === 'approved';
    }

    public function isRejected()
    {
        return $this->account_status === 'rejected';
    }

    // Scope for filtering
    public function scopePending($query)
    {
        return $query->where('account_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('account_status', 'approved');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activeChatSession()
    {
        return $this->hasOne(ChatSession::class, 'user_id')->where('is_active', true);
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class, 'user_id');
    }
}