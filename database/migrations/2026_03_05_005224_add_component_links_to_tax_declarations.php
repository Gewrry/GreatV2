<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add per-component links to tax_declarations so each TD is bound
     * to exactly one Land, Building, or Machinery record (MRPAAO compliance).
     */
    public function up(): void
    {
        Schema::table('tax_declarations', function (Blueprint $table) {
            // Which specific appraisal component this TD covers
            $table->unsignedBigInteger('faas_land_id')->nullable()->after('faas_property_id');
            $table->unsignedBigInteger('faas_building_id')->nullable()->after('faas_land_id');
            $table->unsignedBigInteger('faas_machinery_id')->nullable()->after('faas_building_id');

            // Denormalised property kind for fast queries / display
            // Values: 'land' | 'building' | 'machinery'
            $table->string('property_kind', 20)->nullable()->after('property_type');

            $table->foreign('faas_land_id')->references('id')->on('faas_lands')->nullOnDelete();
            $table->foreign('faas_building_id')->references('id')->on('faas_buildings')->nullOnDelete();
            $table->foreign('faas_machinery_id')->references('id')->on('faas_machineries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->dropForeign(['faas_land_id']);
            $table->dropForeign(['faas_building_id']);
            $table->dropForeign(['faas_machinery_id']);
            $table->dropColumn(['faas_land_id', 'faas_building_id', 'faas_machinery_id', 'property_kind']);
        });
    }
};
