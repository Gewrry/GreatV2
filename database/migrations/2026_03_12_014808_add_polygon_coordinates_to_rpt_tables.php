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
        Schema::table('rpt_online_applications', function (Blueprint $table) {
            $table->json('polygon_coordinates')->nullable();
        });

        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $table->json('polygon_coordinates')->nullable();
        });

        Schema::table('faas_lands', function (Blueprint $table) {
            $table->json('polygon_coordinates')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpt_online_applications', function (Blueprint $table) {
            $table->dropColumn('polygon_coordinates');
        });

        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $table->dropColumn('polygon_coordinates');
        });

        Schema::table('faas_lands', function (Blueprint $table) {
            $table->dropColumn('polygon_coordinates');
        });
    }
};
