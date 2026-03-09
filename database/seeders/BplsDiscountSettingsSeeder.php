<?php
// database/seeders/BplsDiscountSettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BplsDiscountSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'advance_discount_enabled',
                'value' => '1',
                'label' => 'Enable Advance Discount',
                'group' => 'advance_discount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'advance_discount_quarterly',
                'value' => '5',
                'label' => 'Quarterly Discount Rate (%)',
                'group' => 'advance_discount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'advance_discount_semi_annual',
                'value' => '10',
                'label' => 'Semi-Annual Discount Rate (%)',
                'group' => 'advance_discount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'advance_discount_annual',
                'value' => '20',
                'label' => 'Annual Discount Rate (%)',
                'group' => 'advance_discount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'advance_discount_days_before',
                'value' => '10',
                'label' => 'Days Before Due Date to Qualify',
                'group' => 'advance_discount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            // Check if record exists
            $exists = DB::table('bpls_settings')
                ->where('key', $setting['key'])
                ->exists();

            if ($exists) {
                // Update existing - remove 'key' from update array since it's used in where clause
                DB::table('bpls_settings')
                    ->where('key', $setting['key'])
                    ->update([
                        'value' => $setting['value'],
                        'label' => $setting['label'],
                        'group' => $setting['group'],
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert new
                DB::table('bpls_settings')->insert($setting);
            }
        }

        $this->command->info('Discount settings seeded successfully!');
    }
}