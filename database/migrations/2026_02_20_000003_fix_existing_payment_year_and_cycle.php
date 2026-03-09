<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Check if columns exist before trying to update them
        if (
            Schema::hasColumn('bpls_payments', 'payment_year') &&
            Schema::hasColumn('bpls_payments', 'renewal_cycle')
        ) {

            // Fix all existing payment rows:
            DB::statement("
                UPDATE bpls_payments
                SET
                    payment_year  = YEAR(payment_date),
                    renewal_cycle = 0
                WHERE payment_date IS NOT NULL
            ");

            // Safety net: any row where payment_date is NULL gets current year, cycle 0
            DB::statement("
                UPDATE bpls_payments
                SET
                    payment_year  = YEAR(NOW()),
                    renewal_cycle = 0
                WHERE payment_date IS NULL
            ");
        }

        // Check if columns exist for business_entries
        if (
            Schema::hasColumn('bpls_business_entries', 'permit_year') &&
            Schema::hasColumn('bpls_business_entries', 'renewal_cycle')
        ) {

            DB::statement("
                UPDATE bpls_business_entries
                SET
                    permit_year   = YEAR(COALESCE(approved_at, created_at)),
                    renewal_cycle = 0
                WHERE permit_year IS NULL
                   OR renewal_cycle IS NULL
            ");
        }
    }

    public function down(): void
    {
        // Non-destructive — no rollback needed for a data fix
    }
};