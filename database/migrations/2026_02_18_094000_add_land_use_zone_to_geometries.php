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
        Schema::table('faas_gen_rev_geometries', function (Blueprint $table) {
            $table->string('land_use_zone')->nullable()->after('area_sqm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_gen_rev_geometries', function (Blueprint $table) {
            $table->dropColumn('land_use_zone');
        });
    }
};
