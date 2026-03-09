<?php
// database/migrations/2026_03_03_000002_seed_business_id_format_setting.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Seed the default business_id_format into bpls_settings.
     *
     * Placeholders supported:
     *   {muni}  → first 3 uppercase letters of business_municipality
     *   {year}  → permit_year (4 digits)
     *   {id}    → bpls_business_entries.id, zero-padded to 6 digits
     *
     * Examples:
     *   "{muni}-{year}-{id}"  →  MJY-2026-000029
     *   "{year}-{id}"         →  2026-000029
     *   "BUS-{year}-{id}"     →  BUS-2026-000029
     */
    public function up(): void
    {
        DB::table('bpls_settings')->updateOrInsert(
            ['key' => 'business_id_format'],
            [
                'key' => 'business_id_format',
                'value' => '{muni}-{year}-{id}',
                'label' => 'Business ID Format',
                'group' => 'permit',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('bpls_settings')->where('key', 'business_id_format')->delete();
    }
};