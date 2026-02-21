<?php
// app/Helpers/BplsSettingsHelper.php

namespace App\Helpers;

use App\Models\BplsSetting;
use Illuminate\Support\Facades\Log;

class BplsSettingsHelper
{
    /**
     * Get discount setting with fallback
     */
    public static function getDiscountSetting(string $key, $default = null)
    {
        try {
            // Common typos mapping
            $typoMap = [
                'advance_discount_quarterly' => [
                    'advance_discouznt_quarterly',
                    'advance_discount_quarterly',
                ],
                'advance_discount_semi_annual' => [
                    'advance_discouznt_semi_annual',
                    'advance_discount_semi_annual',
                ],
                'advance_discount_annual' => [
                    'advance_discouznt_annual',
                    'advance_discount_annual',
                ],
                'advance_discount_days_before' => [
                    'advance_discouznt_days_before',
                    'advance_discount_days_before',
                ],
                'advance_discount_enabled' => [
                    'advance_discouznt_enabled',
                    'advance_discount_enabled',
                ],
            ];

            // If this key has known typos, try them all
            if (isset($typoMap[$key])) {
                foreach ($typoMap[$key] as $possibleKey) {
                    $setting = BplsSetting::where('key', $possibleKey)->first();
                    if ($setting) {
                        return $setting->value;
                    }
                }
            }

            // Try the original key
            $setting = BplsSetting::where('key', $key)->first();
            if ($setting) {
                return $setting->value;
            }

            // Return default
            return $default;

        } catch (\Exception $e) {
            Log::error('Error getting discount setting: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Get all discount settings with proper defaults
     */
    public static function getDiscountSettings(): array
    {
        try {
            return [
                'enabled' => (bool) self::getDiscountSetting('advance_discount_enabled', '1'),
                'quarterly_rate' => (float) self::getDiscountSetting('advance_discount_quarterly', '5'),
                'semi_annual_rate' => (float) self::getDiscountSetting('advance_discount_semi_annual', '10'),
                'annual_rate' => (float) self::getDiscountSetting('advance_discount_annual', '20'),
                'days_before' => (int) self::getDiscountSetting('advance_discount_days_before', '10'),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting discount settings: ' . $e->getMessage());
            return [
                'enabled' => true,
                'quarterly_rate' => 5,
                'semi_annual_rate' => 10,
                'annual_rate' => 20,
                'days_before' => 10,
            ];
        }
    }

    /**
     * Update a discount setting
     */
    public static function updateDiscountSetting(string $key, $value, ?string $label = null): bool
    {
        try {
            $setting = BplsSetting::where('key', $key)->first();

            if ($setting) {
                $setting->value = $value;
                if ($label) {
                    $setting->label = $label;
                }
                $setting->save();
            } else {
                BplsSetting::create([
                    'key' => $key,
                    'value' => $value,
                    'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
                    'group' => 'advance_discount',
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating discount setting: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all discount settings as array
     */
    public static function getAllDiscountSettings(): array
    {
        try {
            $settings = BplsSetting::where('group', 'advance_discount')
                ->orWhere('key', 'like', 'advance_discount%')
                ->get()
                ->keyBy('key')
                ->toArray();

            return $settings;
        } catch (\Exception $e) {
            Log::error('Error getting all discount settings: ' . $e->getMessage());
            return [];
        }
    }
}