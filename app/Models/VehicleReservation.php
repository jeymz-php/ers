<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_type',
        'origin_campus_id',
        'purpose',
        'other_purpose',
        'destination_type',
        'destination_campus_id',
        'destination_location',
        'trip_date',
        'pickup_time',
        'notes',
        'attachments',
        'status',
        'remarks',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'attachments' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function originCampus()
    {
        return $this->belongsTo(Campus::class, 'origin_campus_id');
    }

    public function destinationCampus()
    {
        return $this->belongsTo(Campus::class, 'destination_campus_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getReservationCodeAttribute()
    {
        $number = $this->id ? str_pad($this->id, 5, '0', STR_PAD_LEFT) : strtoupper(substr(md5(now()->timestamp), 0, 6));
        return "VEH-$number";
    }

    public function getPurposeLabelAttribute()
    {
        if ($this->purpose === 'other') {
            return $this->other_purpose ?: 'Other';
        }

        return $this->purpose === 'transporting' ? 'Transporting' : 'Items Delivery';
    }

    public function getRequesterTypeLabelAttribute()
    {
        return match ($this->requester_type) {
            'student' => 'Student',
            'professor' => 'Professor',
            'admin' => 'Administrator',
            default => ucfirst($this->requester_type),
        };
    }

    public function getDestinationLabelAttribute()
    {
        if ($this->destination_type === 'campus') {
            return $this->destinationCampus?->name ?? 'N/A';
        }

        return $this->destination_location ?: 'N/A';
    }
}