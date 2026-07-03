<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    protected $fillable = ['version', 'updates', 'created_by'];

    protected $casts = [
        'updates' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the most recently published system update record.
     */
    public static function latestUpdate()
    {
        return static::orderByDesc('id')->first();
    }

    /**
     * Get the current system version string (e.g. "2.1").
     * Falls back to the base version if no updates have been published yet.
     */
    public static function currentVersion(string $fallback = '2.0 beta')
    {
        $latest = static::orderByDesc('id')->first();
        return $latest ? $latest->version : $fallback;
    }

    /**
     * System-generated next version number, based on the latest published version.
     * Increments the minor version (2.1 -> 2.2). After reaching .9 it rolls
     * over to the next major version (2.9 -> 3.0).
     */
    public static function nextVersion(): string
    {
        $latest = static::orderByDesc('id')->first();

        if (!$latest || !preg_match('/^(\d+)\.(\d+)/', $latest->version, $m)) {
            return '2.1';
        }

        $major = (int) $m[1];
        $minor = (int) $m[2];

        if ($minor >= 9) {
            $major++;
            $minor = 0;
        } else {
            $minor++;
        }

        return "{$major}.{$minor}";
    }
}