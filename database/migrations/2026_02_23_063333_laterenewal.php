<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Add late_renewal flag and rename status values
 *
 * STATUS CHANGES (old → new):
 *   for_renewal         → completed
 *   for_renewal_payment → for_renewal_payment  (unchanged, already exists)
 *   approved            → pending (if no payments exist) or completed
 *
 * NEW COLUMN:
 *   late_renewal (tinyint 0/1) — set when a business is moved to 'completed'
 *   after January 20. The payment controller reads this to auto-apply the
 *   RA 7160 Sec. 168 25% surcharge on the first installment of the new cycle.
 *
 * IMPORTANT: Run this migration ONCE on your existing database.
 * After migrating, update your code to use the new status values.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            // late_renewal: 1 if the business was renewed after Jan 20 (overdue penalty applies)
            $table->tinyInteger('late_renewal')->default(0)->after('renewal_total_due')
                ->comment('1 = renewal happened after Jan 20, 25% surcharge applies per RA 7160 Sec. 168');
        });

        // ── Migrate existing status values ─────────────────────────────────
        // Old 'for_renewal' → new 'completed' (same meaning: cycle done, ready to renew)
        DB::table('bpls_business_entries')
            ->where('status', 'for_renewal')
            ->update(['status' => 'completed']);

        // Old 'approved' (if any remain) → 'pending'
        // Note: In the old system 'approved' was sometimes used loosely.
        // Only migrate if there are no payments (truly just assessed, not paid).
        // If you want to keep them as-is, comment this out.
        DB::statement("
            UPDATE bpls_business_entries be
            SET status = 'pending'
            WHERE status = 'approved'
            AND NOT EXISTS (
                SELECT 1 FROM bpls_payments bp
                WHERE bp.business_entry_id = be.id
            )
        ");
    }

    public function down(): void
    {
        // Revert status values
        DB::table('bpls_business_entries')
            ->where('status', 'completed')
            ->update(['status' => 'for_renewal']);

        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $table->dropColumn('late_renewal');
        });
    }
};