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
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->string('boundary_north', 255)->nullable()->after('survey_no');
            $table->string('boundary_south', 255)->nullable()->after('boundary_north');
            $table->string('boundary_east', 255)->nullable()->after('boundary_south');
            $table->string('boundary_west', 255)->nullable()->after('boundary_east');
            
            // Also ensure administrator fields are consistent with registration
            // Note: administrator_name/address already exist in FaasProperty based on model, 
            // but let's check or add if missing.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn(['boundary_north', 'boundary_south', 'boundary_east', 'boundary_west']);
        });
    }
};
