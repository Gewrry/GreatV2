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
        Schema::table('rpta_assessment_levels', function (Blueprint $table) {
            $table->foreignId('revision_year_id')->nullable()->after('rpta_actual_use_id')->constrained('rpta_revision_years')->nullOnDelete();
            // Unique per actual use + revision year + classification (inherited from actual use)
            $table->unique(['rpta_actual_use_id', 'revision_year_id'], 'unique_assessment_level_per_use_rev');
        });

        Schema::table('rpta_unit_values', function (Blueprint $table) {
            $table->foreignId('revision_year_id')->nullable()->after('rpta_actual_use_id')->constrained('rpta_revision_years')->nullOnDelete();
            // Unique per actual use + barangay + revision year
            $table->unique(['rpta_actual_use_id', 'barangay_id', 'revision_year_id'], 'unique_unit_value_per_loc_rev');
        });
    }

    public function down(): void
    {
        Schema::table('rpta_assessment_levels', function (Blueprint $table) {
            $table->dropUnique('unique_assessment_level_per_use_rev');
            $table->dropForeign(['revision_year_id']);
            $table->dropColumn('revision_year_id');
        });

        Schema::table('rpta_unit_values', function (Blueprint $table) {
            $table->dropUnique('unique_unit_value_per_loc_rev');
            $table->dropForeign(['revision_year_id']);
            $table->dropColumn('revision_year_id');
        });
    }
};
