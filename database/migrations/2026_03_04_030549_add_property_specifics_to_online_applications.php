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
            // Building Specifics
            $table->decimal('building_floor_area', 14, 2)->nullable()->after('land_area');
            $table->string('building_type')->nullable()->after('building_floor_area');
            $table->string('building_materials')->nullable()->after('building_type');

            // Machinery Specifics
            $table->decimal('machinery_cost', 18, 2)->nullable()->after('building_materials');
            $table->integer('machinery_useful_life')->nullable()->after('machinery_cost');
            $table->date('machinery_acquisition_date')->nullable()->after('machinery_useful_life');
        });

        // Update document types to include Government ID
        Schema::table('rpt_application_documents', function (Blueprint $table) {
             DB::statement("ALTER TABLE rpt_application_documents MODIFY COLUMN type ENUM('title_deed', 'tax_clearance', 'deed_of_sale', 'sketch_plan', 'special_power_of_attorney', 'gov_id', 'others') DEFAULT 'others'");
        });
    }

    public function down(): void
    {
        Schema::table('rpt_online_applications', function (Blueprint $table) {
            $table->dropColumn([
                'building_floor_area', 'building_type', 'building_materials',
                'machinery_cost', 'machinery_useful_life', 'machinery_acquisition_date'
            ]);
        });
    }
};
