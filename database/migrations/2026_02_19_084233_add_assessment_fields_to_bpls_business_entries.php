<?php
// database/migrations/2026_02_19_084233_add_assessment_fields_to_bpls_business_entries.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('bpls_business_entries', 'business_nature')) {
                $table->string('business_nature')->nullable()->after('type_of_business');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'business_scale')) {
                $table->string('business_scale')->nullable()->after('business_nature');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'capital_investment')) {
                $table->decimal('capital_investment', 15, 2)->nullable()->after('business_scale');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'mode_of_payment')) {
                $table->string('mode_of_payment')->nullable()->after('capital_investment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('bpls_business_entries', 'business_nature') ? 'business_nature' : null,
                Schema::hasColumn('bpls_business_entries', 'business_scale') ? 'business_scale' : null,
                Schema::hasColumn('bpls_business_entries', 'capital_investment') ? 'capital_investment' : null,
                Schema::hasColumn('bpls_business_entries', 'mode_of_payment') ? 'mode_of_payment' : null,
            ]));
        });
    }
};