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
        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->decimal('total_market_value_snapshot', 16, 2)->nullable()->after('total_market_value');
            $table->decimal('total_assessed_value_snapshot', 16, 2)->nullable()->after('total_assessed_value');
            $table->decimal('tax_rate_snapshot', 10, 5)->nullable()->after('tax_rate');
            $table->decimal('basic_tax_snapshot', 16, 2)->nullable()->after('tax_rate_snapshot');
        });

        Schema::table('faas_properties', function (Blueprint $table) {
            $table->timestamp('inactive_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->dropColumn(['total_market_value_snapshot', 'total_assessed_value_snapshot', 'tax_rate_snapshot', 'basic_tax_snapshot']);
        });

        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropColumn('inactive_at');
        });
    }
};
