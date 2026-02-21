<?php
// database/migrations/2024_xx_xx_xxxxxx_fix_discount_settings_typo.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Fix the typo in existing records
        DB::table('bpls_settings')
            ->where('key', 'advance_discouznt_quarterly')
            ->update(['key' => 'advance_discount_quarterly']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discouznt_semi_annual')
            ->update(['key' => 'advance_discount_semi_annual']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discouznt_annual')
            ->update(['key' => 'advance_discount_annual']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discouznt_days_before')
            ->update(['key' => 'advance_discount_days_before']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discouznt_enabled')
            ->update(['key' => 'advance_discount_enabled']);
    }

    public function down()
    {
        // Revert back if needed
        DB::table('bpls_settings')
            ->where('key', 'advance_discount_quarterly')
            ->update(['key' => 'advance_discouznt_quarterly']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discount_semi_annual')
            ->update(['key' => 'advance_discouznt_semi_annual']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discount_annual')
            ->update(['key' => 'advance_discouznt_annual']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discount_days_before')
            ->update(['key' => 'advance_discouznt_days_before']);

        DB::table('bpls_settings')
            ->where('key', 'advance_discount_enabled')
            ->update(['key' => 'advance_discouznt_enabled']);
    }
};