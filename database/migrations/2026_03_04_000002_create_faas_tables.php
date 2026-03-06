<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── FAAS PROPERTY REGISTRY ────────────────────────────────────────────

        // Core property profile — one record per ARP/PIN
        Schema::create('faas_properties', function (Blueprint $table) {
            $table->id();
            $table->string('arp_no')->unique()->nullable()->comment('Assessment Roll Number — generated on approval');
            $table->string('pin')->nullable()->comment('Property Identification Number');
            $table->string('owner_name');
            $table->string('owner_tin')->nullable();
            $table->string('owner_address');
            $table->string('owner_contact')->nullable();
            $table->string('administrator_name')->nullable();
            $table->string('administrator_address')->nullable();

            // Location
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->string('street')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();

            // Workflow
            $table->enum('status', ['draft', 'for_review', 'approved', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // FAAS — Land component
        Schema::create('faas_lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faas_property_id')->constrained('faas_properties')->cascadeOnDelete();
            $table->foreignId('rpta_actual_use_id')->nullable()->constrained('rpta_actual_uses')->nullOnDelete();

            // Survey & Area
            $table->string('survey_no')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('blk_no')->nullable();
            $table->decimal('area_sqm', 14, 4);

            // Valuation
            $table->decimal('unit_value', 18, 2)->default(0)->comment('Base Market Value per sq.m');
            $table->decimal('base_market_value', 18, 2)->default(0)->comment('area × unit_value');
            $table->decimal('market_value_adjustments', 18, 2)->default(0);
            $table->decimal('market_value', 18, 2)->default(0)->comment('final FMV');
            $table->decimal('assessment_level', 5, 4)->default(0);
            $table->decimal('assessed_value', 18, 2)->default(0);

            $table->timestamps();
        });

        // FAAS — Building component
        Schema::create('faas_buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faas_property_id')->constrained('faas_properties')->cascadeOnDelete();
            $table->foreignId('faas_land_id')->nullable()->constrained('faas_lands')->nullOnDelete()->comment('Land parcel building sits on');
            $table->foreignId('rpta_bldg_type_id')->nullable()->constrained('rpta_bldg_types')->nullOnDelete();
            $table->foreignId('rpta_actual_use_id')->nullable()->constrained('rpta_actual_uses')->nullOnDelete();

            $table->string('building_name')->nullable();
            $table->string('kind_of_building')->nullable();
            $table->integer('num_storeys')->default(1);
            $table->decimal('floor_area', 14, 4)->comment('sq.m');
            $table->year('year_constructed')->nullable();
            $table->year('year_appraised')->nullable();

            // Valuation
            $table->decimal('construction_cost_per_sqm', 18, 2)->default(0);
            $table->decimal('base_market_value', 18, 2)->default(0);
            $table->decimal('depreciation_rate', 5, 4)->default(0);
            $table->decimal('depreciation_amount', 18, 2)->default(0);
            $table->decimal('market_value', 18, 2)->default(0);
            $table->decimal('assessment_level', 5, 4)->default(0);
            $table->decimal('assessed_value', 18, 2)->default(0);
            $table->text('additional_items')->nullable()->comment('JSON: [{description, area, unit_cost}]');

            $table->timestamps();
        });

        // FAAS — Machinery / Equipment component
        Schema::create('faas_machineries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faas_property_id')->constrained('faas_properties')->cascadeOnDelete();
            $table->foreignId('rpta_actual_use_id')->nullable()->constrained('rpta_actual_uses')->nullOnDelete();

            $table->string('machine_name');
            $table->string('brand')->nullable();
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->year('year_acquired')->nullable();
            $table->decimal('original_cost', 18, 2)->default(0);
            $table->integer('useful_life')->default(10);
            $table->decimal('depreciation_rate', 5, 4)->default(0);
            $table->decimal('depreciation_amount', 18, 2)->default(0);
            $table->decimal('market_value', 18, 2)->default(0);
            $table->decimal('assessment_level', 5, 4)->default(0);
            $table->decimal('assessed_value', 18, 2)->default(0);

            $table->timestamps();
        });

        // FAAS Property Attachments (document uploads for the property file)
        Schema::create('faas_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faas_property_id')->constrained('faas_properties')->cascadeOnDelete();
            $table->string('type')->comment('title_deed, sketch_plan, tax_clearance, others');
            $table->string('file_path');
            $table->string('original_filename');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Audit log for FAAS changes
        Schema::create('faas_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faas_property_id')->constrained('faas_properties')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faas_activity_logs');
        Schema::dropIfExists('faas_attachments');
        Schema::dropIfExists('faas_machineries');
        Schema::dropIfExists('faas_buildings');
        Schema::dropIfExists('faas_lands');
        Schema::dropIfExists('faas_properties');
    }
};
