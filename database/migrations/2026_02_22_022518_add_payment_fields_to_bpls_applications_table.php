<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_applications', function (Blueprint $table) {
            // Add assessment + payment fields after 'assessed_by' (which already exists)
            if (!Schema::hasColumn('bpls_applications', 'assessment_amount')) {
                $table->decimal('assessment_amount', 12, 2)->nullable()->after('assessed_by');
            }
            if (!Schema::hasColumn('bpls_applications', 'assessment_notes')) {
                $table->text('assessment_notes')->nullable()->after('assessment_amount');
            }

            // mode_of_payment goes after assessment_notes (which now exists)
            if (!Schema::hasColumn('bpls_applications', 'mode_of_payment')) {
                $table->string('mode_of_payment')->nullable()->after('assessment_notes')
                    ->comment('quarterly | semi_annual | annual');
            }

            if (!Schema::hasColumn('bpls_applications', 'or_number')) {
                $table->string('or_number', 100)->nullable()->after('mode_of_payment');
            }
            if (!Schema::hasColumn('bpls_applications', 'permit_notes')) {
                $table->text('permit_notes')->nullable()->after('or_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_applications', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('bpls_applications', 'permit_notes') ? 'permit_notes' : null,
                Schema::hasColumn('bpls_applications', 'or_number') ? 'or_number' : null,
                Schema::hasColumn('bpls_applications', 'mode_of_payment') ? 'mode_of_payment' : null,
                Schema::hasColumn('bpls_applications', 'assessment_notes') ? 'assessment_notes' : null,
                Schema::hasColumn('bpls_applications', 'assessment_amount') ? 'assessment_amount' : null,
            ]));
        });
    }
};