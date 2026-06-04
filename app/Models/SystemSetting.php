<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isDown(): bool
    {
        return static::getValue('system_status', 'up') === 'down';
    }

    public static function isUp(): bool
    {
        return static::getValue('system_status', 'up') === 'up';
    }
}
