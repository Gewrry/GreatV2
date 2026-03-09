<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Force drop the overly-restictive duplicate index
        try {
            DB::statement("ALTER TABLE tax_declarations DROP INDEX unique_td_per_year_status");
        } catch (\Exception $e) {
            // Might have finally dropped or not exists in some environments
        }

        Schema::table('tax_declarations', function (Blueprint $table) {
            // Add a more granular unique constraint that permits multiple components (Land, Bldg, Mach)
            // within the same FAAS record to have their own separate Tax Declarations.
            $table->unique(
                ['faas_property_id', 'faas_land_id', 'faas_building_id', 'faas_machinery_id', 'effectivity_year', 'status'],
                'unique_td_component_year_status'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->dropUnique('unique_td_component_year_status');
        });
        
        try {
            DB::statement("ALTER TABLE tax_declarations ADD UNIQUE INDEX unique_td_per_year_status(faas_property_id, effectivity_year, status)");
        } catch (\Exception $e) {}
    }
};
