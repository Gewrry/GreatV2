<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bpls_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('bpls_applications', 'signatory_id')) {
                $table->foreignId('signatory_id')->nullable()->after('permit_notes')
                    ->constrained('bpls_permit_signatories')->nullOnDelete();
            }
            if (!Schema::hasColumn('bpls_applications', 'signatory_name')) {
                $table->string('signatory_name')->nullable()->after('signatory_id');
            }
            if (!Schema::hasColumn('bpls_applications', 'signatory_position')) {
                $table->string('signatory_position')->nullable()->after('signatory_name');
            }
            if (!Schema::hasColumn('bpls_applications', 'permit_valid_from')) {
                $table->date('permit_valid_from')->nullable()->after('signatory_position');
            }
            if (!Schema::hasColumn('bpls_applications', 'permit_valid_until')) {
                $table->date('permit_valid_until')->nullable()->after('permit_valid_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('signatory_id');
            $table->dropColumn(['signatory_name', 'signatory_position', 'permit_valid_from', 'permit_valid_until']);
        });
    }
};
