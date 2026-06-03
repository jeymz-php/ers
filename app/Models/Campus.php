<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'address', 'is_active', 'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }

    public static function getActiveCampuses()
    {
        return self::where('is_active', true)
                   ->orderBy('display_order')
                   ->orderBy('name')
                   ->get();
    }
}