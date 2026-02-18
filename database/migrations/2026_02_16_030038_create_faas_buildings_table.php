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
        Schema::create('faas_buildings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_id');
            
            // Property Identification
            $table->string('td_no')->nullable();
            $table->string('pin')->nullable();
            $table->string('land_td_no')->nullable(); // Link to Land
            
            // Building Description
            $table->string('building_type')->nullable(); // Residential, Commercial, etc.
            $table->string('structure_type')->nullable(); // Concrete, Wood, etc.
            $table->integer('storeys')->nullable();
            $table->string('year_constructed')->nullable();
            $table->string('year_occupied')->nullable();
            $table->string('permit_no')->nullable();
            
            // Structural Details (Optional breakdown can be added later if needed)
            
            // Floor Area & Valuation
            $table->decimal('floor_area', 15, 2)->default(0); // Total Floor Area
            $table->decimal('unit_value', 15, 2)->default(0); // Construction Cost per sqm
            $table->decimal('replacement_cost', 15, 2)->default(0); // Floor Area * Unit Value
            
            // Depreciation (Method A)
            $table->decimal('depreciation_rate', 5, 2)->default(0); // Depreciation %
            $table->decimal('depreciation_cost', 15, 2)->default(0); // Replacement Cost * Rate
            $table->decimal('residual_percent', 5, 2)->default(0); // Remaining Value %
            
            // Market & Assessed Value
            $table->decimal('market_value', 15, 2)->default(0); 
            $table->string('assmt_kind')->nullable();
            $table->string('actual_use')->nullable();
            $table->decimal('assessment_level', 5, 2)->default(0);
            $table->decimal('assessed_value', 15, 2)->default(0);
            
            // Administrative
            $table->date('effectivity_date')->nullable();
            $table->string('status')->default('Existing'); // Existing, Demolished, etc.
            $table->text('remarks')->nullable();
            $table->text('memoranda')->nullable();
            
            $table->timestamps();

            $table->foreign('faas_id')->references('id')->on('faas_gen_rev')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faas_buildings');
    }
};
