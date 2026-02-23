<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the machinery_valuations audit table.
 *
 * Every time a machine record is created or updated, one row is written here.
 * This provides:
 *   - Full reassessment history
 *   - Reproducible valuation snapshots (age and dep_rate are stored HERE, not in main table)
 *   - Future-proof annual revaluation reports
 *   - Audit traceability for BIR / BLGF compliance
 *
 * age and dep_rate are intentionally NOT stored in faas_machines because:
 *   - Age changes every year and would become stale
 *   - dep_rate derived from a stale age produces wrong reassessments
 *   - This table captures both at the exact moment of valuation
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('machinery_valuations', function (Blueprint $table) {

            $table->id();

            // ── Reference ────────────────────────────────────────────────────────
            $table->unsignedBigInteger('machine_id')->index();
            $table->string('td_no', 50)->nullable();

            // ── Snapshot: inputs used at the time of valuation ───────────────────
            $table->decimal('acquisition_cost', 15, 2);
            $table->decimal('freight_cost', 15, 2)->default(0);
            $table->decimal('installation_cost', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('base_value', 15, 2);

            $table->date('acquisition_date')->nullable();
            $table->unsignedSmallInteger('useful_life')->nullable();
            $table->decimal('salvage_value_percent', 6, 2);

            // ── Snapshot: computed intermediaries ────────────────────────────────
            // These are the fields intentionally excluded from the main table.
            // They are correct AS OF computed_at and must not be mutated afterward.
            $table->unsignedSmallInteger('computed_age');           // current_year - acquisition_year
            $table->decimal('computed_dep_rate', 8, 4);            // age / useful_life (e.g. 0.3000 = 30%)

            // ── Snapshot: residual and valuation ─────────────────────────────────
            $table->enum('residual_mode', ['auto', 'manual']);
            $table->decimal('residual_used', 6, 2);                // residual_percent actually applied
            $table->decimal('assessment_level', 6, 2);
            $table->decimal('market_value', 15, 2);
            $table->decimal('assessed_value', 15, 2);              // FINAL value at this point in time

            // ── Audit ────────────────────────────────────────────────────────────
            $table->string('action', 20)->default('created');       // created | updated | reassessed
            $table->timestamp('computed_at');                        // exact moment of computation
            $table->unsignedBigInteger('created_by')->nullable();   // assessor user ID
            $table->string('created_by_name', 255)->nullable();     // denormalized for report readability

            $table->timestamps();

            // ── Foreign Key ──────────────────────────────────────────────────────
            $table->foreign('machine_id')
                ->references('id')
                ->on('faas_machines')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machinery_valuations');
    }
};