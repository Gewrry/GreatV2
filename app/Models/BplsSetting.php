<?php
// app/Models/BplsSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BplsSetting extends Model
{
    protected $table = 'bpls_settings';

    protected $fillable = ['key', 'value', 'label', 'group'];

    public $timestamps = true;

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $label = null, $group = 'advance_discount')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $label,
                'group' => $group
            ]
        );

        Cache::forget('bpls_settings_all');
        return $setting;
    }

    /**
     * Get all settings as array
     */
    public static function getAll()
    {
        return Cache::remember('bpls_settings_all', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}