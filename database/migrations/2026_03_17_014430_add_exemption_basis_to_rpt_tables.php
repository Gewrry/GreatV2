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
        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('rpt_property_registrations', 'exemption_basis')) {
                $table->text('exemption_basis')->nullable()->after('is_taxable');
            }
        });

        Schema::table('faas_properties', function (Blueprint $table) {
            if (!Schema::hasColumn('faas_properties', 'exemption_basis')) {
                $table->text('exemption_basis')->nullable()->after('is_taxable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('rpt_property_registrations', 'exemption_basis')) {
                $table->dropColumn('exemption_basis');
            }
        });

        Schema::table('faas_properties', function (Blueprint $table) {
            if (Schema::hasColumn('faas_properties', 'exemption_basis')) {
                $table->dropColumn('exemption_basis');
            }
        });
    }
};
