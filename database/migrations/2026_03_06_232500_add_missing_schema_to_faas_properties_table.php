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
        Schema::table('faas_properties', function (Blueprint $table) {
            // Missing core fields
            if (!Schema::hasColumn('faas_properties', 'property_type')) {
                $table->string('property_type')->after('property_registration_id')->comment('land, building, machinery, mixed');
            }
            if (!Schema::hasColumn('faas_properties', 'effectivity_date')) {
                $table->date('effectivity_date')->nullable()->after('property_type');
            }
            if (!Schema::hasColumn('faas_properties', 'revision_type')) {
                $table->string('revision_type')->nullable()->after('effectivity_date')->comment('New Discovery, Reassessment, etc.');
            }

            // Missing property identification fields (mirrored from registration)
            if (!Schema::hasColumn('faas_properties', 'title_no')) {
                $table->string('title_no')->nullable()->after('province');
            }
            if (!Schema::hasColumn('faas_properties', 'lot_no')) {
                $table->string('lot_no')->nullable()->after('title_no');
            }
            if (!Schema::hasColumn('faas_properties', 'blk_no')) {
                $table->string('blk_no')->nullable()->after('lot_no');
            }
            if (!Schema::hasColumn('faas_properties', 'survey_no')) {
                $table->string('survey_no')->nullable()->after('blk_no');
            }

            // Revision Year link (missing from manual migration but used in controller)
            if (!Schema::hasColumn('faas_properties', 'revision_year_id')) {
                $table->foreignId('revision_year_id')->nullable()->after('status')->constrained('rpta_revision_years')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faas_properties', function (Blueprint $table) {
            $table->dropForeign(['revision_year_id']);
            $table->dropColumn([
                'property_type',
                'effectivity_date',
                'revision_type',
                'title_no',
                'lot_no',
                'blk_no',
                'survey_no',
                'revision_year_id'
            ]);
        });
    }
};
