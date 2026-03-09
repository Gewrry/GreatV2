<?php
// database/migrations/2026_02_20_000002_add_renewal_and_year_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ── bpls_payments: add payment_year + renewal_cycle ───────────────
        // Only add if the table exists (it should)
        if (Schema::hasTable('bpls_payments')) {
            Schema::table('bpls_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('bpls_payments', 'payment_year')) {
                    // The year this payment belongs to (e.g. 2026, 2027)
                    $table->unsignedSmallInteger('payment_year')
                        ->after('business_entry_id')
                        ->default(2026)
                        ->comment('The fiscal/permit year this payment covers');
                }
                if (!Schema::hasColumn('bpls_payments', 'renewal_cycle')) {
                    // Increments each renewal: 0 = original, 1 = first renewal, etc.
                    $table->unsignedTinyInteger('renewal_cycle')
                        ->after('payment_year')
                        ->default(0)
                        ->comment('0 = original registration, 1 = first renewal, etc.');
                }
            });

            // Backfill existing rows: derive year from payment_date
            DB::statement("
                UPDATE bpls_payments
                SET payment_year = YEAR(payment_date),
                    renewal_cycle = 0
                WHERE payment_year = 2026
                  AND payment_date IS NOT NULL
            ");
        }

        // ── bpls_business_entries: add renewal tracking columns ───────────
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('bpls_business_entries', 'renewal_cycle')) {
                // Current renewal cycle of the entry (matches payments)
                $table->unsignedTinyInteger('renewal_cycle')
                    ->after('status')
                    ->default(0)
                    ->comment('0 = original, 1 = 1st renewal, etc.');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'permit_year')) {
                // The year the current permit/assessment is for
                $table->unsignedSmallInteger('permit_year')
                    ->after('renewal_cycle')
                    ->nullable()
                    ->comment('Year the current total_due assessment is for');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'last_renewed_at')) {
                $table->timestamp('last_renewed_at')
                    ->nullable()
                    ->after('permit_year');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'renewal_total_due')) {
                // Separate column so original total_due is never overwritten
                $table->decimal('renewal_total_due', 15, 2)
                    ->nullable()
                    ->after('total_due')
                    ->comment('Total due for the current renewal cycle');
            }
            // Retirement columns (from previous migration — safe to skip if exists)
            if (!Schema::hasColumn('bpls_business_entries', 'retirement_reason')) {
                $table->text('retirement_reason')->nullable();
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retirement_date')) {
                $table->date('retirement_date')->nullable();
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retirement_remarks')) {
                $table->text('retirement_remarks')->nullable();
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retired_at')) {
                $table->timestamp('retired_at')->nullable();
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retired_by')) {
                $table->unsignedBigInteger('retired_by')->nullable();
            }
            if (!Schema::hasColumn('bpls_business_entries', 'remarks')) {
                $table->text('remarks')->nullable();
            }
        });

        // Backfill permit_year for existing entries
        DB::statement("
            UPDATE bpls_business_entries
            SET permit_year = YEAR(COALESCE(approved_at, created_at)),
                renewal_cycle = 0
            WHERE permit_year IS NULL
        ");
    }

    public function down(): void
    {
        if (Schema::hasTable('bpls_payments')) {
            Schema::table('bpls_payments', function (Blueprint $table) {
                $table->dropColumn(['payment_year', 'renewal_cycle']);
            });
        }

        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $cols = [
                'renewal_cycle',
                'permit_year',
                'last_renewed_at',
                'renewal_total_due',
                'retirement_reason',
                'retirement_date',
                'retirement_remarks',
                'retired_at',
                'retired_by',
                'remarks',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bpls_business_entries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};