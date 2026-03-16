<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rpt_online_applications', function (Blueprint $table) {
            $table->string('administrator_name', 255)->nullable()->after('owner_email');
            $table->string('administrator_address', 500)->nullable()->after('administrator_name');
            $table->string('boundary_north', 255)->nullable()->after('property_description');
            $table->string('boundary_south', 255)->nullable()->after('boundary_north');
            $table->string('boundary_east', 255)->nullable()->after('boundary_south');
            $table->string('boundary_west', 255)->nullable()->after('boundary_east');
        });

        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $table->string('boundary_north', 255)->nullable()->after('survey_no');
            $table->string('boundary_south', 255)->nullable()->after('boundary_north');
            $table->string('boundary_east', 255)->nullable()->after('boundary_south');
            $table->string('boundary_west', 255)->nullable()->after('boundary_east');
        });
    }

    public function down(): void
    {
        Schema::table('rpt_online_applications', function (Blueprint $table) {
            $table->dropColumn(['administrator_name', 'administrator_address', 'boundary_north', 'boundary_south', 'boundary_east', 'boundary_west']);
        });

        Schema::table('rpt_property_registrations', function (Blueprint $table) {
            $table->dropColumn(['boundary_north', 'boundary_south', 'boundary_east', 'boundary_west']);
        });
    }
};
