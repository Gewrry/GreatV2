<?php
// database/migrations/2026_xx_xx_000001_seed_beneficiary_discount_bpls_settings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Seed the default beneficiary-discount settings into bpls_settings.
     * Uses INSERT IGNORE / updateOrInsert so it's safe to re-run.
     */
    public function up(): void
    {
        $now = now();

        $seeds = [
            // Master toggle
            ['key' => 'beneficiary_discount_enabled', 'value' => '1', 'label' => 'Enable Beneficiary Discounts', 'group' => 'beneficiary_discount'],
            // Stacking rule
            ['key' => 'beneficiary_discount_stack', 'value' => 'highest_only', 'label' => 'Discount Stacking Rule', 'group' => 'beneficiary_discount'],

            // PWD
            ['key' => 'pwd_discount_rate', 'value' => '20', 'label' => 'PWD Discount Rate (%)', 'group' => 'beneficiary_discount'],
            ['key' => 'pwd_discount_apply_to', 'value' => 'total', 'label' => 'PWD Discount Apply To', 'group' => 'beneficiary_discount'],

            // Senior Citizen
            ['key' => 'senior_discount_rate', 'value' => '20', 'label' => 'Senior Citizen Discount Rate (%)', 'group' => 'beneficiary_discount'],
            ['key' => 'senior_discount_apply_to', 'value' => 'total', 'label' => 'Senior Citizen Discount Apply To', 'group' => 'beneficiary_discount'],

            // Solo Parent
            ['key' => 'solo_parent_discount_rate', 'value' => '10', 'label' => 'Solo Parent Discount Rate (%)', 'group' => 'beneficiary_discount'],
            ['key' => 'solo_parent_discount_apply_to', 'value' => 'total', 'label' => 'Solo Parent Discount Apply To', 'group' => 'beneficiary_discount'],

            // 4Ps
            ['key' => 'fourps_discount_rate', 'value' => '10', 'label' => '4Ps Discount Rate (%)', 'group' => 'beneficiary_discount'],
            ['key' => 'fourps_discount_apply_to', 'value' => 'total', 'label' => '4Ps Discount Apply To', 'group' => 'beneficiary_discount'],
        ];

        foreach ($seeds as $seed) {
            DB::table('bpls_settings')->updateOrInsert(
                ['key' => $seed['key']],
                array_merge($seed, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        $keys = [
            'beneficiary_discount_enabled',
            'beneficiary_discount_stack',
            'pwd_discount_rate',
            'pwd_discount_apply_to',
            'senior_discount_rate',
            'senior_discount_apply_to',
            'solo_parent_discount_rate',
            'solo_parent_discount_apply_to',
            'fourps_discount_rate',
            'fourps_discount_apply_to',
        ];

        DB::table('bpls_settings')->whereIn('key', $keys)->delete();
    }
};