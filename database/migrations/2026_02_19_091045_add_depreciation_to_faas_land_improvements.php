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
        Schema::table('faas_land_improvements', function (Blueprint $table) {
            $table->decimal('depreciation_rate', 5, 2)->default(0)->after('total_value');
            $table->decimal('remaining_value_percent', 5, 2)->default(100)->after('depreciation_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_land_improvements', function (Blueprint $table) {
            $table->dropColumn(['depreciation_rate', 'remaining_value_percent']);
        });
    }
};
