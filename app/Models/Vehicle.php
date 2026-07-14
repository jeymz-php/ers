<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'name',
        'plate_number',
        'type',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function reservations()
    {
        return $this->hasMany(VehicleReservation::class);
    }

    public static function getByCampus($campusId)
    {
        return self::where('campus_id', $campusId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getLabelAttribute(): string
    {
        $label = $this->name;

        if ($this->plate_number) {
            $label .= ' (' . $this->plate_number . ')';
        }

        return $label;
    }
}