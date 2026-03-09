<?php
// database/migrations/xxxx_xx_xx_add_retirement_fields_to_business_entries_table.php
// FIXED: Table name corrected from 'business_entries' to 'bpls_business_entries'

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('bpls_business_entries', 'retirement_reason')) {
                $table->text('retirement_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retirement_date')) {
                $table->date('retirement_date')->nullable()->after('retirement_reason');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retirement_remarks')) {
                $table->text('retirement_remarks')->nullable()->after('retirement_date');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retired_at')) {
                $table->timestamp('retired_at')->nullable()->after('retirement_remarks');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'retired_by')) {
                $table->unsignedBigInteger('retired_by')->nullable()->after('retired_at');
            }
            if (!Schema::hasColumn('bpls_business_entries', 'remarks')) {
                $table->text('remarks')->nullable()->after('retired_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_business_entries', function (Blueprint $table) {
            $cols = [
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