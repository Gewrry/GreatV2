<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;

class RptaSetting extends Model
{
    protected $table = 'rpta_settings';
    protected $fillable = ['setting_key', 'setting_value'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value)
    {
        return self::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
    }
}
