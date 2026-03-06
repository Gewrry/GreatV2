<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax_declarations', function (Blueprint $table) {
            // Unique TD per FAAS per year (only one active)
            $table->unique(['faas_property_id', 'effectivity_year', 'status'], 'unique_td_per_year_status');
        });

        Schema::table('rpt_payments', function (Blueprint $table) {
            $table->unique('or_no');
        });
    }

    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropUnique(['arp_no']);
        });

        Schema::table('tax_declarations', function (Blueprint $table) {
            $table->dropUnique('unique_td_per_year_status');
        });

        Schema::table('rpt_payments', function (Blueprint $table) {
            $table->dropUnique(['or_no']);
        });
    }
};
