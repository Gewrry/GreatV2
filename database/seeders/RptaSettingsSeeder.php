<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RPT\RptaClass;
use App\Models\RPT\RptaActualUse;
use App\Models\RPT\RptaAssessmentLevel;
use App\Models\RPT\RptaRevisionYear;

class RptaSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure we have a Current Revision Year
        $revYear = RptaRevisionYear::updateOrCreate(
            ['year' => 2024],
            ['is_current' => true]
        );

        // 2. Global LGU Settings for PIN Generation
        DB::table('rpta_settings')->updateOrInsert(['setting_key' => 'province_code'], ['setting_value' => '045']);
        DB::table('rpta_settings')->updateOrInsert(['setting_key' => 'municipality_code'], ['setting_value' => '02']);

        // 3. Standard Classifications
        $classData = [
            ['code' => 'RES', 'name' => 'Residential'],
            ['code' => 'AGR', 'name' => 'Agricultural'],
            ['code' => 'COM', 'name' => 'Commercial'],
            ['code' => 'IND', 'name' => 'Industrial'],
            ['code' => 'SPE', 'name' => 'Special'],
        ];

        foreach ($classData as $c) {
            $class = RptaClass::updateOrCreate(['code' => $c['code']], ['name' => $c['name']]);

            // 3. Common Actual Uses & Assessment Levels (Statutory standard approximations)
            switch ($c['code']) {
                case 'RES':
                    $this->createUseAndLevels($class, $revYear, 'Residential', 'RES-HOUSE', 0.20);
                    break;
                case 'AGR':
                    $this->createUseAndLevels($class, $revYear, 'Agricultural', 'AGR-LAND', 0.40);
                    break;
                case 'COM':
                    $this->createUseAndLevels($class, $revYear, 'Commercial', 'COM-BLDG', 0.50);
                    break;
                case 'IND':
                    $this->createUseAndLevels($class, $revYear, 'Industrial', 'IND-FACTORY', 0.50);
                    break;
                case 'SPE':
                    $this->createUseAndLevels($class, $revYear, 'Cultural/Scientific', 'SPE-CUL', 0.15);
                    $this->createUseAndLevels($class, $revYear, 'Hospital', 'SPE-HOSP', 0.15);
                    break;
            }
        }
    }

    private function createUseAndLevels($class, $revYear, $name, $code, $rate)
    {
        $use = RptaActualUse::updateOrCreate(
            ['code' => $code],
            ['rpta_class_id' => $class->id, 'name' => $name]
        );

        RptaAssessmentLevel::updateOrCreate(
            [
                'rpta_actual_use_id' => $use->id,
                'revision_year_id'   => $revYear->id,
                'min_value'          => 0,
            ],
            [
                'max_value' => null,
                'rate'      => $rate
            ]
        );
    }
}
