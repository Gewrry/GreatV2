<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BplsFinancialSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            // Advance Payment Discount Settings
            [
                'key' => 'advance_discount_enabled',
                'value' => '1',
                'label' => 'Enable Advance Payment Discount',
                'group' => 'discounts',
            ],
            [
                'key' => 'advance_discount_quarterly',
                'value' => '10.0',
                'label' => 'Quarterly Advance Discount Rate (%)',
                'group' => 'discounts',
            ],
            [
                'key' => 'advance_discount_semi_annual',
                'value' => '15.0',
                'label' => 'Semi-Annual Advance Discount Rate (%)',
                'group' => 'discounts',
            ],
            [
                'key' => 'advance_discount_annual',
                'value' => '20.0',
                'label' => 'Annual Advance Discount Rate (%)',
                'group' => 'discounts',
            ],
            [
                'key' => 'advance_discount_days_before',
                'value' => '30',
                'label' => 'Days Before Due Date to Qualify for Discount',
                'group' => 'discounts',
            ],

            // Surcharge / Penalty Settings
            [
                'key' => 'monthly_surcharge_rate',
                'value' => '2.0',
                'label' => 'Monthly Surcharge Rate (%)',
                'group' => 'penalties',
            ],
            [
                'key' => 'max_surcharge_rate',
                'value' => '72.0',
                'label' => 'Maximum Total Surcharge (%)',
                'group' => 'penalties',
            ],

            // Beneficiary Discount Settings
            [
                'key' => 'beneficiary_discount_enabled',
                'value' => '1',
                'label' => 'Enable Beneficiary Discounts (Senior/PWD/etc)',
                'group' => 'discounts',
            ],
        ];

        foreach ($settings as $item) {
            DB::table('bpls_settings')->updateOrInsert(
                ['key' => $item['key']],
                array_merge($item, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }

        $this->command->info('BPLS Financial Settings seeded successfully!');
    }
}
