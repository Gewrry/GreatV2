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
            if (!Schema::hasColumn('rpt_property_registrations', 'administrator_tin')) {
                $table->string('administrator_tin')->nullable()->after('administrator_name');
            }
            if (!Schema::hasColumn('rpt_property_registrations', 'administrator_contact')) {
                $table->string('administrator_contact')->nullable()->after('administrator_address');
            }
            if (!Schema::hasColumn('rpt_property_registrations', 'district')) {
                $table->string('district')->nullable()->after('barangay_id');
            }
            if (!Schema::hasColumn('rpt_property_registrations', 'boundary_north')) {
                $table->string('boundary_north')->nullable()->after('survey_no');
                $table->string('boundary_south')->nullable()->after('boundary_north');
                $table->string('boundary_east')->nullable()->after('boundary_south');
                $table->string('boundary_west')->nullable()->after('boundary_east');
            }
            if (!Schema::hasColumn('rpt_property_registrations', 'is_taxable')) {
                $table->boolean('is_taxable')->default(true)->after('property_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $cols = [
                'administrator_tin',
                'administrator_contact',
                'district',
                'boundary_north',
                'boundary_south',
                'boundary_east',
                'boundary_west',
                'is_taxable'
            ];
            foreach($cols as $col) {
                if(Schema::hasColumn('rpt_property_registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
