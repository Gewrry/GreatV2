<?php
// database/migrations/xxxx_add_discount_to_bpls_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bpls_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('bpls_payments', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0)->after('backtaxes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_payments', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};