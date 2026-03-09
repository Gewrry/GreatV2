<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // We use raw SQL for maximum control and to ignore errors if constraints don't exist
        
        // 1. Drop foreign key if it's there
        try {
            DB::statement("ALTER TABLE rpta_assessment_levels DROP FOREIGN KEY rpta_assessment_levels_revision_year_id_foreign");
        } catch (\Exception $e) {}

        // 2. Drop unique index if it exists
        try {
            DB::statement("ALTER TABLE rpta_assessment_levels DROP INDEX unique_assessment_level_per_use_rev");
        } catch (\Exception $e) {}

        // 3. Drop another possible unique index name
        try {
            DB::statement("ALTER TABLE rpta_assessment_levels DROP INDEX rpta_assessment_levels_rpta_actual_use_id_revision_year_id_unique");
        } catch (\Exception $e) {}

        // 4. Add the new expanded unique constraint
        try {
            DB::statement("ALTER TABLE rpta_assessment_levels ADD UNIQUE INDEX unique_assessment_lvl_range(rpta_actual_use_id, revision_year_id, min_value, max_value)");
        } catch (\Exception $e) {}

        // 5. Re-add the foreign key
        try {
            DB::statement("ALTER TABLE rpta_assessment_levels ADD CONSTRAINT rpta_assessment_levels_revision_year_id_foreign FOREIGN KEY (revision_year_id) REFERENCES rpta_revision_years(id) ON DELETE CASCADE");
        } catch (\Exception $e) {}
    }

    public function down()
    {
        try {
            DB::statement("ALTER TABLE rpta_assessment_levels DROP INDEX unique_assessment_lvl_range");
            DB::statement("ALTER TABLE rpta_assessment_levels ADD UNIQUE INDEX unique_assessment_level_per_use_rev(rpta_actual_use_id, revision_year_id)");
        } catch (\Exception $e) {}
    }
};
