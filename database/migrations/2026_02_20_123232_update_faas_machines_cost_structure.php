<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('faas_machines', function (Blueprint $table) {

            // ── Add td_no ──────────────────────────────────────────────────
            // Denormalized copy of the parent TD number for fast reporting/querying.
            // The error "Unknown column 'td_no'" confirms this was missing from the table.
            if (!Schema::hasColumn('faas_machines', 'td_no')) {
                $table->string('td_no')->nullable()->after('faas_id');
            }

            // ── Add pin ────────────────────────────────────────────────────
            // Denormalized copy of the parcel identification number from the parent TD.
            // Same issue as td_no — controller writes it but column didn't exist.
            if (!Schema::hasColumn('faas_machines', 'pin')) {
                $table->string('pin')->nullable()->after('td_no');
            }

            // ── Remove insurance_cost ──────────────────────────────────────
            // No longer part of the Base Value formula.
            // Formula: Base = Acquisition + Freight + Installation + Other Costs
            if (Schema::hasColumn('faas_machines', 'insurance_cost')) {
                $table->dropColumn('insurance_cost');
            }

            // ── Add other_cost ─────────────────────────────────────────────
            // Replaces insurance_cost as the catch-all "Other Costs" in Base Value.
            if (!Schema::hasColumn('faas_machines', 'other_cost')) {
                $table->decimal('other_cost', 15, 2)->default(0)->nullable()
                    ->after('installation_cost');
            }

            // ── Add year_acquired ──────────────────────────────────────────
            // Explicit year used as the primary source for Age = Current Year - Year Acquired.
            // date_acquired remains for storing the full optional exact date.
            if (!Schema::hasColumn('faas_machines', 'year_acquired')) {
                $table->unsignedSmallInteger('year_acquired')->nullable()
                    ->after('other_cost');
            }

            // ── Add age ────────────────────────────────────────────────────
            // Stores the computed Age (Current Year - Year Acquired) at time of saving.
            if (!Schema::hasColumn('faas_machines', 'age')) {
                $table->unsignedSmallInteger('age')->nullable()
                    ->after('year_acquired');
            }

            // ── Ensure depreciation_rate exists ───────────────────────────
            // Stores computed DepRate = Age / UsefulLife as a percentage (e.g. 25.00)
            if (!Schema::hasColumn('faas_machines', 'depreciation_rate')) {
                $table->decimal('depreciation_rate', 8, 2)->nullable()
                    ->after('age');
            }

            // ── Ensure residual_percent exists ─────────────────────────────
            // Stores the final clamped Remaining% = max(1 - DepRate, ResidualMinimum)
            if (!Schema::hasColumn('faas_machines', 'residual_percent')) {
                $table->decimal('residual_percent', 8, 2)->nullable()
                    ->after('depreciation_rate');
            }

            // ── Ensure total_cost exists ───────────────────────────────────
            // Stores Base Value = Acquisition + Freight + Installation + Other
            if (!Schema::hasColumn('faas_machines', 'total_cost')) {
                $table->decimal('total_cost', 15, 2)->nullable()
                    ->after('residual_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faas_machines', function (Blueprint $table) {

            // Drop columns added in up()
            $columnsToDrop = ['td_no', 'pin', 'other_cost', 'year_acquired', 'age'];

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('faas_machines', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Re-add insurance_cost on rollback
            if (!Schema::hasColumn('faas_machines', 'insurance_cost')) {
                $table->decimal('insurance_cost', 15, 2)->default(0)->nullable()
                    ->after('freight_cost');
            }
        });
    }
};