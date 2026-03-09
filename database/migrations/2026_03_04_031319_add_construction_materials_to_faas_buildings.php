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
        Schema::table('faas_buildings', function (Blueprint $table) {
            $table->string('construction_materials')->nullable()->after('kind_of_building');
            $table->decimal('building_type_base_value', 18, 2)->nullable()->after('construction_materials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_buildings', function (Blueprint $table) {
            //
        });
    }
};
