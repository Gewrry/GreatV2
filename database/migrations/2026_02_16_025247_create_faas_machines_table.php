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
        Schema::create('faas_machines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faas_id');
            // Property Identification
            $table->string('td_no')->nullable();
            $table->string('pin')->nullable();
            
            // Machine Details
            $table->string('machine_name')->nullable();
            $table->string('brand_model')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('capacity')->nullable();
            $table->string('year_manufactured')->nullable();
            $table->string('year_installed')->nullable();
            
            // Valuation
            $table->decimal('acquisition_cost', 15, 2)->default(0);
            $table->decimal('freight_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0); // Computed: Acq + Freight + Insurance + Other
            
            $table->decimal('depreciation_rate', 5, 2)->default(0); // Store rate
            $table->decimal('residual_percent', 5, 2)->default(0); // Preferred method: Remaining Value %
            $table->decimal('market_value', 15, 2)->default(0); // Total Cost * Residual %
            
            $table->string('assmt_kind')->nullable();
            $table->string('actual_use')->nullable();
            $table->decimal('assessment_level', 5, 2)->default(0);
            $table->decimal('assessed_value', 15, 2)->default(0);
            
            // Admin
            $table->date('effectivity_date')->nullable();
            $table->string('status')->default('Active'); // Active/Disposed
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
        Schema::dropIfExists('faas_machines');
    }
};
