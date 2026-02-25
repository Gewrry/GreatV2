<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Redesigns faas_machines to match the approved eRPTA machinery valuation schema.
 *
 * Key changes from old schema:
 *  - total_cost          → base_value          (clearer name, same formula)
 *  - estimated_life      → useful_life          (BLGF-aligned terminology)
 *  - year_acquired       → dropped              (acquisition_date is primary)
 *  - date_acquired       → acquisition_date     (primary depreciation basis)
 *  - other_cost          → kept (permits / foundation / etc.)
 *  - age                 → dropped              (computed on-the-fly; stored in audit table only)
 *  - depreciation_rate   → dropped              (same reason)
 *  - residual_minimum    → salvage_value_percent (pulled from classification table)
 *  - residual_mode       → added (manual | auto)
 *  - residual_percent    → kept (single source of truth into market_value formula)
 *  - date_installed      → added (audit / records only, no formula role)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('faas_machines', function (Blueprint $table) {

            $table->id();

            // ── Parent Reference ─────────────────────────────────────────────────
            $table->unsignedBigInteger('faas_id')->index();
            $table->string('td_no', 50)->nullable()->index();
            $table->string('pin', 50)->nullable();

            // ── Machinery Identification ─────────────────────────────────────────
            $table->string('machine_name', 255);
            $table->string('brand_model', 255)->nullable();
            $table->string('serial_no', 100)->nullable();
            $table->string('capacity', 100)->nullable();
            $table->string('supplier_vendor', 255)->nullable();

            // ── Timeline ─────────────────────────────────────────────────────────
            $table->unsignedSmallInteger('year_manufactured')->nullable();
            $table->date('date_installed')->nullable();   // audit / records only
            $table->date('acquisition_date')->nullable(); // PRIMARY depreciation basis

            // ── Physical Details ─────────────────────────────────────────────────
            $table->string('condition', 50)->nullable();    // New / Good / Fair / Poor
            $table->unsignedSmallInteger('useful_life')->nullable(); // years; denominator in dep_rate
            $table->unsignedSmallInteger('remaining_life')->nullable(); // optional audit field
            $table->string('invoice_no', 100)->nullable();
            $table->string('funding_source', 255)->nullable();

            // ── Cost Breakdown ───────────────────────────────────────────────────
            // base_value = acquisition_cost + freight_cost + installation_cost + other_cost
            $table->decimal('acquisition_cost', 15, 2)->default(0);
            $table->decimal('freight_cost', 15, 2)->default(0);
            $table->decimal('installation_cost', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);       // permits / foundation / etc.
            $table->decimal('base_value', 15, 2)->default(0);       // computed snapshot

            // ── Depreciation / Residual ──────────────────────────────────────────
            // salvage_value_percent comes from the classification/ordinance table.
            // It is the floor for residual_percent when mode = auto.
            // Fallback hierarchy: classification.default_salvage → LGU setting → 20
            $table->decimal('salvage_value_percent', 6, 2)->default(20.00);

            // residual_mode determines how residual_percent is derived:
            //   auto   → computed: max((1 - age/useful_life) × 100, salvage_value_percent)
            //   manual → assessor enters the value directly
            $table->enum('residual_mode', ['auto', 'manual'])->default('auto');

            // residual_percent is the SINGLE SOURCE OF TRUTH going into market_value.
            // Always stored regardless of mode, so market_value can always be recomputed.
            $table->decimal('residual_percent', 6, 2)->default(100.00);

            // ── Valuation ────────────────────────────────────────────────────────
            // market_value   = base_value × (residual_percent / 100)
            // assessed_value = market_value × (assessment_level / 100)
            $table->decimal('market_value', 15, 2)->default(0);
            $table->decimal('assessment_level', 6, 2)->default(0);  // from LGU ordinance table
            $table->decimal('assessed_value', 15, 2)->default(0);   // FINAL value

            // ── Classification ───────────────────────────────────────────────────
            $table->string('assmt_kind', 100)->nullable();
            $table->string('actual_use', 100)->nullable();
            $table->string('rev_year', 10)->nullable();

            // ── Record Details ───────────────────────────────────────────────────
            $table->date('effectivity_date')->nullable();
            $table->enum('status', ['ACTIVE', 'RETIRED'])->default('ACTIVE');
            $table->text('remarks')->nullable();
            $table->text('memoranda')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ── Foreign Key ──────────────────────────────────────────────────────
            $table->foreign('faas_id')
                ->references('id')
                ->on('faas_gen_rev')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faas_machines');
    }
};