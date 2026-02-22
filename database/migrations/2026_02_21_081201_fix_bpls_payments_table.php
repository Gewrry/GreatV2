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
        Schema::table('bpls_payments', function (Blueprint $table) {
            // Add payment_year if it doesn't exist
            if (!Schema::hasColumn('bpls_payments', 'payment_year')) {
                $table->unsignedInteger('payment_year')->default(date('Y'))->after('id');
            }

            // Add renewal_cycle if it doesn't exist
            if (!Schema::hasColumn('bpls_payments', 'renewal_cycle')) {
                $table->unsignedInteger('renewal_cycle')->default(0)->after('payment_year');
            }

            // Add business_entry_id if it doesn't exist
            if (!Schema::hasColumn('bpls_payments', 'business_entry_id')) {
                $table->unsignedBigInteger('business_entry_id')->nullable()->after('renewal_cycle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpls_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_year', 'renewal_cycle', 'business_entry_id']);
        });
    }
};
