<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE tax_declarations DROP INDEX unique_td_per_year_status");
        } catch (\Exception $e) {
            // Index might not exist or already dropped by raw tinker commands earlier
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE tax_declarations ADD UNIQUE INDEX unique_td_per_year_status(faas_property_id, effectivity_year, status)");
        } catch (\Exception $e) {}
    }
};
