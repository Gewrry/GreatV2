<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bpls_online_payments', function (Blueprint $table) {
            // Add or_number if missing
            if (!Schema::hasColumn('bpls_online_payments', 'or_number')) {
                $table->string('or_number')->nullable()->after('paid_at');
            }
        });

        // Add 'card' to the enum
        DB::statement("ALTER TABLE bpls_online_payments MODIFY COLUMN payment_method ENUM('gcash','maya','landbank','over_the_counter','card') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('bpls_online_payments', function (Blueprint $table) {
            if (Schema::hasColumn('bpls_online_payments', 'or_number')) {
                $table->dropColumn('or_number');
            }
        });

        DB::statement("ALTER TABLE bpls_online_payments MODIFY COLUMN payment_method ENUM('gcash','maya','landbank','over_the_counter') NOT NULL");
    }
};