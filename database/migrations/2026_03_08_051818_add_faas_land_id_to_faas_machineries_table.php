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
        Schema::table('faas_machineries', function (Blueprint $table) {
            $table->foreignId('faas_land_id')->nullable()->after('faas_property_id')->constrained('faas_lands')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_machineries', function (Blueprint $table) {
            $table->dropForeign(['faas_land_id']);
            $table->dropColumn('faas_land_id');
        });
    }
};
