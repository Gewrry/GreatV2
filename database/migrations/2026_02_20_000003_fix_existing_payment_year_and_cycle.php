<?php
// database/migrations/2026_02_20_000003_fix_existing_payment_year_and_cycle.php
//
// PURPOSE: The previous migration added payment_year + renewal_cycle columns
// but the backfill condition was wrong. Existing payment rows defaulted to
// payment_year=2026, renewal_cycle=0 — which collides with fresh renewal
// assessments. This migration corrects those rows properly.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Fix all existing payment rows:
        // - payment_year  = actual year from payment_date
        // - renewal_cycle = 0  (all existing payments are original-registration payments)
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

        // Also fix business entries: set permit_year from approved_at or created_at
        DB::statement("
            UPDATE bpls_business_entries
            SET
                permit_year   = YEAR(COALESCE(approved_at, created_at)),
                renewal_cycle = 0
            WHERE permit_year IS NULL
               OR renewal_cycle IS NULL
        ");
    }

    public function down(): void
    {
        // Non-destructive — no rollback needed for a data fix
    }
};