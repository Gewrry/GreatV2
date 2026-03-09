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
            $table->decimal('area_sqm', 15, 2)->nullable()->after('geometry');
            $table->string('adj_north')->nullable()->after('area_sqm');
            $table->string('adj_south')->nullable()->after('adj_north');
            $table->string('adj_east')->nullable()->after('adj_south');
            $table->string('adj_west')->nullable()->after('adj_east');
            $table->decimal('gps_lat', 10, 8)->nullable()->after('adj_west');
            $table->decimal('gps_lng', 11, 8)->nullable()->after('gps_lat');
            $table->text('inspector_notes')->nullable()->after('gps_lng');
        });

        Schema::table('faas_attachments', function (Blueprint $table) {
            $table->string('attachment_type', 50)->nullable()->after('file_path')->comment('e.g., Land Title, Deed of Sale, ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_gen_rev_geometries', function (Blueprint $table) {
            $table->dropColumn(['area_sqm', 'adj_north', 'adj_south', 'adj_east', 'adj_west', 'gps_lat', 'gps_lng', 'inspector_notes']);
        });

        Schema::table('faas_attachments', function (Blueprint $table) {
            $table->dropColumn('attachment_type');
        });
    }
};
