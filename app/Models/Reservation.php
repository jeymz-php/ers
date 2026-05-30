<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'establishment_id', 'campus_id', 'event_name', 'description',
        'event_date', 'start_time', 'end_time', 'status', 'remarks',
        'approved_at', 'approved_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}