<?php
// database/migrations/2026_02_20_000001_add_total_due_to_bpls_business_entries.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('bpls_business_entries', 'total_due')) {
                $table->decimal('total_due', 15, 2)->nullable()->after('mode_of_payment');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('total_due');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('bpls_business_entries', 'total_due') ? 'total_due' : null,
                Schema::hasColumn('bpls_business_entries', 'approved_at') ? 'approved_at' : null,
            ]));
        });
    }
};