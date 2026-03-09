<?php
// database/migrations/2026_03_03_000002_backfill_business_id_for_existing_entries.php
//
// The column `business_id` already exists on `bpls_business_entries`.
// This migration backfills it for any entries that were approved before
// the generation logic was deployed (i.e. business_id IS NULL but
// approved_at IS NOT NULL).
//
// Run:  php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\BplsSetting;

return new class extends Migration {
    public function up(): void
    {
        // ----------------------------------------------------------------
        // 1. Ensure business_id column exists on bpls_business_entries
        // ----------------------------------------------------------------
        if (!Schema::hasColumn('bpls_business_entries', 'business_id')) {
            Schema::table('bpls_business_entries', function (Blueprint $table) {
                $table->string('business_id', 50)
                    ->nullable()
                    ->unique()
                    ->after('permit_year')
                    ->comment('Generated on Approve to Payment. Format: {MUNI}-{YEAR}-{ID}');
            });
        }

        // ----------------------------------------------------------------
        // 2. Backfill business_id for existing approved entries
        // ----------------------------------------------------------------
        $format = BplsSetting::where('key', 'business_id_format')->value('value') ?? 'BUS-{year}-{id}';

        $entries = DB::table('bpls_business_entries')
            ->whereNull('business_id')
            ->whereNotNull('approved_at')
            ->get(['id', 'permit_year', 'business_barangay', 'business_municipality']);

        foreach ($entries as $entry) {
            $year        = $entry->permit_year ?? now()->year;
            $barangayCode = strtoupper(substr(preg_replace('/\s+/', '', $entry->business_barangay ?? 'BRG'), 0, 4));
            $muniCode    = strtoupper(substr(preg_replace('/\s+/', '', $entry->business_municipality ?? 'MUN'), 0, 4));
            $paddedId    = str_pad($entry->id, 6, '0', STR_PAD_LEFT);

            $businessId = str_replace(
                ['{year}', '{id}', '{barangay_code}', '{muni}'],
                [$year, $paddedId, $barangayCode, $muniCode],
                $format
            );

            DB::table('bpls_business_entries')
                ->where('id', $entry->id)
                ->update(['business_id' => $businessId]);
        }

        // ----------------------------------------------------------------
        // 3. Backfill walk_in_business_id on clients
        //    Skip entirely if the column does not exist yet — it will be
        //    handled by the migration that adds the column.
        // ----------------------------------------------------------------
        if (!Schema::hasColumn('clients', 'walk_in_business_id')) {
            return;
        }

        $unlinkedClients = DB::table('clients')
            ->whereNull('walk_in_business_id')
            ->whereNull('deleted_at')
            ->get(['id', 'email']);

        foreach ($unlinkedClients as $client) {
            // Find the most recently approved walk-in entry for this email
            // (walk-in = no bpls_application link)
            $entry = DB::table('bpls_business_entries as be')
                ->leftJoin('bpls_applications as ba', 'ba.business_entry_id', '=', 'be.id')
                ->whereNull('ba.id')
                ->where('be.email', $client->email)
                ->whereNotNull('be.approved_at')
                ->whereNull('be.deleted_at')
                ->orderByDesc('be.approved_at')
                ->first(['be.id']);

            if ($entry) {
                DB::table('clients')
                    ->where('id', $client->id)
                    ->update(['walk_in_business_id' => $entry->id]);
            }
        }
    }

    public function down(): void
    {
        // Non-destructive — do not drop the column or clear business_id values
    }
};