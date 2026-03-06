<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── SETTINGS TABLES ───────────────────────────────────────────────────

        // Property Classifications (Residential, Commercial, Agricultural, etc.)
        Schema::create('rpta_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Actual Use (Specific sub-type of a classification)
        Schema::create('rpta_actual_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpta_class_id')->constrained('rpta_classes')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Assessment Levels (Rate applied to Market Value to get Assessed Value, per classification)
        Schema::create('rpta_assessment_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpta_actual_use_id')->constrained('rpta_actual_uses')->cascadeOnDelete();
            $table->decimal('min_value', 18, 2)->default(0);
            $table->decimal('max_value', 18, 2)->nullable()->comment('null = unlimited');
            $table->decimal('rate', 5, 4)->comment('e.g. 0.2000 = 20%');
            $table->timestamps();
        });

        // Land Schedules / Unit Values (Base Market Value per sq.m per actual use)
        Schema::create('rpta_unit_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpta_actual_use_id')->constrained('rpta_actual_uses')->cascadeOnDelete();
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->decimal('value_per_sqm', 18, 2);
            $table->year('effectivity_year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Building Type (Type of structure for depreciation computation)
        Schema::create('rpta_bldg_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->decimal('base_construction_cost', 18, 2)->default(0)->comment('Cost per sq.m');
            $table->decimal('useful_life', 5, 2)->default(50);
            $table->decimal('residual_value_rate', 5, 4)->default(0.2)->comment('e.g. 0.20 = 20%');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // General Revision Year
        Schema::create('rpta_revision_years', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // RPTA Signatories (for printable TD and other docs)
        Schema::create('rpta_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('role')->comment('Assessor, Provincial Assessor, etc.');
            $table->string('name');
            $table->string('designation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpta_signatories');
        Schema::dropIfExists('rpta_revision_years');
        Schema::dropIfExists('rpta_bldg_types');
        Schema::dropIfExists('rpta_unit_values');
        Schema::dropIfExists('rpta_assessment_levels');
        Schema::dropIfExists('rpta_actual_uses');
        Schema::dropIfExists('rpta_classes');
    }
};
