<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'campus_id', 'capacity', 'type', 'is_active'
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
        return $this->hasMany(Reservation::class);
    }

    public static function getByCampus($campusId)
    {
        return self::where('campus_id', $campusId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}