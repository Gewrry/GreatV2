<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Division;
use App\Models\JobPosition;
use App\Models\SalaryGrade;
use App\Models\EmploymentType;
use App\Models\Plantilla;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HROrganizationSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('Seeding HR Organization Setup...');

        $this->seedEmploymentTypes();
        $this->seedSalaryGrades();
        $this->seedOffices();
        $this->seedDivisions();
        $this->seedJobPositions();
        $this->seedPlantilla();

        $this->command->info('HR Organization Setup seeded successfully!');
    }

    protected function seedEmploymentTypes(): void
    {
        $types = [
            [
                'type_name' => 'Permanent',
                'type_code' => 'PERM',
                'type_description' => 'Regular full-time employee with permanent appointment',
                'category' => 'Regular',
                'is_permanent' => true,
                'has_plantilla' => true,
                'leave_credits_per_year' => 15,
                'is_active' => true,
            ],
            [
                'type_name' => 'Casual',
                'type_code' => 'CAS',
                'type_description' => 'Casual employee with temporary appointment',
                'category' => 'Casual',
                'is_permanent' => false,
                'has_plantilla' => false,
                'leave_credits_per_year' => 5,
                'is_active' => true,
            ],
            [
                'type_name' => 'Contractual',
                'type_code' => 'CONT',
                'type_description' => 'Employee under contract of service',
                'category' => 'Contractual',
                'is_permanent' => false,
                'has_plantilla' => false,
                'leave_credits_per_year' => 0,
                'is_active' => true,
            ],
            [
                'type_name' => 'Job Order',
                'type_code' => 'JO',
                'type_description' => 'Job order worker paid from lump sum',
                'category' => 'Job Order',
                'is_permanent' => false,
                'has_plantilla' => false,
                'leave_credits_per_year' => 0,
                'is_active' => true,
            ],
            [
                'type_name' => 'Elective',
                'type_code' => 'ELEC',
                'type_description' => 'Elected officials',
                'category' => 'Elective',
                'is_permanent' => false,
                'has_plantilla' => true,
                'leave_credits_per_year' => 0,
                'is_active' => true,
            ],
            [
                'type_name' => 'Co-terminus',
                'type_code' => 'COTERM',
                'type_description' => 'Employee whose tenure is coterminous with that of the appointing authority',
                'category' => 'Co-terminus',
                'is_permanent' => false,
                'has_plantilla' => true,
                'leave_credits_per_year' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            EmploymentType::updateOrCreate(
                ['type_code' => $type['type_code']],
                $type
            );
        }

        $this->command->info('Employment types seeded.');
    }

    protected function seedSalaryGrades(): void
    {
        $grades = [
            ['grade_number' => 1, 'grade_name' => 'SG-1', 'step_1' => 16528, 'step_2' => 17094, 'step_3' => 17681, 'step_4' => 18290, 'step_5' => 18921, 'step_6' => 19574, 'step_7' => 20251, 'step_8' => 20950, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 2, 'grade_name' => 'SG-2', 'step_1' => 17526, 'step_2' => 18125, 'step_3' => 18746, 'step_4' => 19390, 'step_5' => 20058, 'step_6' => 20750, 'step_7' => 21466, 'step_8' => 22207, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 3, 'grade_name' => 'SG-3', 'step_1' => 18572, 'step_2' => 19222, 'step_3' => 19895, 'step_4' => 20593, 'step_5' => 21314, 'step_6' => 22060, 'step_7' => 22832, 'step_8' => 23629, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 4, 'grade_name' => 'SG-4', 'step_1' => 19676, 'step_2' => 20357, 'step_3' => 21060, 'step_4' => 21785, 'step_5' => 22534, 'step_6' => 23307, 'step_7' => 24104, 'step_8' => 24927, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 5, 'grade_name' => 'SG-5', 'step_1' => 20838, 'step_2' => 21557, 'step_3' => 22300, 'step_4' => 23067, 'step_5' => 23859, 'step_6' => 24676, 'step_7' => 25519, 'step_8' => 26389, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 6, 'grade_name' => 'SG-6', 'step_1' => 22061, 'step_2' => 22832, 'step_3' => 23628, 'step_4' => 24450, 'step_5' => 25298, 'step_6' => 26172, 'step_7' => 27073, 'step_8' => 28002, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 7, 'grade_name' => 'SG-7', 'step_1' => 23346, 'step_2' => 24163, 'step_3' => 25008, 'step_4' => 25881, 'step_5' => 26784, 'step_6' => 27716, 'step_7' => 28679, 'step_8' => 29673, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 8, 'grade_name' => 'SG-8', 'step_1' => 24698, 'step_2' => 25563, 'step_3' => 26456, 'step_4' => 27379, 'step_5' => 28332, 'step_6' => 29316, 'step_7' => 30332, 'step_8' => 31380, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 9, 'grade_name' => 'SG-9', 'step_1' => 26121, 'step_2' => 27037, 'step_3' => 27982, 'step_4' => 28958, 'step_5' => 29966, 'step_6' => 31006, 'step_7' => 32080, 'step_8' => 33188, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 10, 'grade_name' => 'SG-10', 'step_1' => 27620, 'step_2' => 28578, 'step_3' => 29566, 'step_4' => 30585, 'step_5' => 31635, 'step_6' => 32718, 'step_7' => 33834, 'step_8' => 34985, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 11, 'grade_name' => 'SG-11', 'step_1' => 29310, 'step_2' => 30329, 'step_3' => 31380, 'step_4' => 32464, 'step_5' => 33581, 'step_6' => 34733, 'step_7' => 35920, 'step_8' => 37143, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 12, 'grade_name' => 'SG-12', 'step_1' => 31097, 'step_2' => 32184, 'step_3' => 33304, 'step_4' => 34459, 'step_5' => 35648, 'step_6' => 36873, 'step_7' => 38134, 'step_8' => 39432, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 13, 'grade_name' => 'SG-13', 'step_1' => 32987, 'step_2' => 34141, 'step_3' => 35328, 'step_4' => 36551, 'step_5' => 37809, 'step_6' => 39104, 'step_7' => 40436, 'step_8' => 41806, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 14, 'grade_name' => 'SG-14', 'step_1' => 34985, 'step_2' => 36194, 'step_3' => 37439, 'step_4' => 38721, 'step_5' => 40041, 'step_6' => 41400, 'step_7' => 42800, 'step_8' => 44241, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 15, 'grade_name' => 'SG-15', 'step_1' => 37093, 'step_2' => 38392, 'step_3' => 39729, 'step_4' => 41106, 'step_5' => 42523, 'step_6' => 43982, 'step_7' => 45484, 'step_8' => 47029, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 16, 'grade_name' => 'SG-16', 'step_1' => 39316, 'step_2' => 40677, 'step_3' => 42079, 'step_4' => 43522, 'step_5' => 45009, 'step_6' => 46540, 'step_7' => 48116, 'step_8' => 49739, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 17, 'grade_name' => 'SG-17', 'step_1' => 41658, 'step_2' => 43111, 'step_3' => 44607, 'step_4' => 46147, 'step_5' => 47732, 'step_6' => 49364, 'step_7' => 51043, 'step_8' => 52772, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 18, 'grade_name' => 'SG-18', 'step_1' => 44121, 'step_2' => 45665, 'step_3' => 47252, 'step_4' => 48884, 'step_5' => 50562, 'step_6' => 52288, 'step_7' => 54062, 'step_8' => 55886, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 19, 'grade_name' => 'SG-19', 'step_1' => 46713, 'step_2' => 48350, 'step_3' => 50031, 'step_4' => 51758, 'step_5' => 53533, 'step_6' => 55358, 'step_7' => 57234, 'step_8' => 59163, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 20, 'grade_name' => 'SG-20', 'step_1' => 49440, 'step_2' => 51169, 'step_3' => 52943, 'step_4' => 54765, 'step_5' => 56636, 'step_6' => 58557, 'step_7' => 60531, 'step_8' => 62558, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 21, 'grade_name' => 'SG-21', 'step_1' => 52310, 'step_2' => 54131, 'step_3' => 55999, 'step_4' => 57915, 'step_5' => 59882, 'step_6' => 61901, 'step_7' => 63973, 'step_8' => 66101, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 22, 'grade_name' => 'SG-22', 'step_1' => 55328, 'step_2' => 57262, 'step_3' => 59245, 'step_4' => 61277, 'step_5' => 63361, 'step_6' => 65498, 'step_7' => 67690, 'step_8' => 69938, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 23, 'grade_name' => 'SG-23', 'step_1' => 58502, 'step_2' => 60550, 'step_3' => 62648, 'step_4' => 64799, 'step_5' => 67004, 'step_6' => 69264, 'step_7' => 71580, 'step_8' => 73954, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 24, 'grade_name' => 'SG-24', 'step_1' => 61840, 'step_2' => 64004, 'step_3' => 66220, 'step_4' => 68489, 'step_5' => 70813, 'step_6' => 73193, 'step_7' => 75631, 'step_8' => 78128, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 25, 'grade_name' => 'SG-25', 'step_1' => 65341, 'step_2' => 67628, 'step_3' => 69969, 'step_4' => 72365, 'step_5' => 74818, 'step_6' => 77330, 'step_7' => 79902, 'step_8' => 82538, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 26, 'grade_name' => 'SG-26', 'step_1' => 69014, 'step_2' => 71429, 'step_3' => 73900, 'step_4' => 76428, 'step_5' => 79015, 'step_6' => 81662, 'step_7' => 84371, 'step_8' => 87144, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 27, 'grade_name' => 'SG-27', 'step_1' => 72868, 'step_2' => 75419, 'step_3' => 78028, 'step_4' => 80697, 'step_5' => 83428, 'step_6' => 86222, 'step_7' => 89080, 'step_8' => 92005, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 28, 'grade_name' => 'SG-28', 'step_1' => 76911, 'step_2' => 79602, 'step_3' => 82352, 'step_4' => 85164, 'step_5' => 88039, 'step_6' => 90980, 'step_7' => 93988, 'step_8' => 97067, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 29, 'grade_name' => 'SG-29', 'step_1' => 81154, 'step_2' => 83996, 'step_3' => 86901, 'step_4' => 89870, 'step_5' => 92905, 'step_6' => 96008, 'step_7' => 99181, 'step_8' => 102428, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 30, 'grade_name' => 'SG-30', 'step_1' => 85606, 'step_2' => 88604, 'step_3' => 91668, 'step_4' => 94800, 'step_5' => 98001, 'step_6' => 101274, 'step_7' => 104621, 'step_8' => 108044, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 31, 'grade_name' => 'SG-31', 'step_1' => 90268, 'step_2' => 93428, 'step_3' => 96656, 'step_4' => 99953, 'step_5' => 103322, 'step_6' => 106765, 'step_7' => 110284, 'step_8' => 113882, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 32, 'grade_name' => 'SG-32', 'step_1' => 95148, 'step_2' => 98479, 'step_3' => 101882, 'step_4' => 105358, 'step_5' => 108910, 'step_6' => 112539, 'step_7' => 116249, 'step_8' => 120042, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
            ['grade_number' => 33, 'grade_name' => 'SG-33', 'step_1' => 100250, 'step_2' => 103759, 'step_3' => 107345, 'step_4' => 111010, 'step_5' => 114756, 'step_6' => 118585, 'step_7' => 122501, 'step_8' => 126506, 'salary_schedule' => '2024', 'effectivity_year' => 2024],
        ];

        foreach ($grades as $grade) {
            SalaryGrade::updateOrCreate(
                ['grade_number' => $grade['grade_number']],
                $grade
            );
        }

        $this->command->info('Salary grades seeded.');
    }

    protected function seedOffices(): void
    {
        $offices = [
            ['office_name' => 'Office of the Mayor', 'office_code' => 'MAYOR', 'office_short_name' => 'Mayor', 'office_description' => 'Office of the Municipal/City Mayor', 'order_sequence' => 1, 'is_active' => true],
            ['office_name' => 'Office of the Vice Mayor', 'office_code' => 'VICE-MAYOR', 'office_short_name' => 'Vice Mayor', 'office_description' => 'Office of the Municipal/City Vice Mayor', 'order_sequence' => 2, 'is_active' => true],
            ['office_name' => 'Sangguniang Bayan/Panlungsod', 'office_code' => 'SB', 'office_short_name' => 'SB', 'office_description' => 'Legislative Office', 'order_sequence' => 3, 'is_active' => true],
            ['office_name' => 'Municipal/City Administrator', 'office_code' => 'ADMIN', 'office_short_name' => 'Admin', 'office_description' => 'Office of the Municipal/City Administrator', 'order_sequence' => 4, 'is_active' => true],
            ['office_name' => 'Human Resource Management Office', 'office_code' => 'HRMO', 'office_short_name' => 'HRMO', 'office_description' => 'Human Resource Management Office', 'order_sequence' => 5, 'is_active' => true],
            ['office_name' => 'Municipal/City Treasurer\'s Office', 'office_code' => 'TREASURY', 'office_short_name' => 'Treasury', 'office_description' => 'Office of the Municipal/City Treasurer', 'order_sequence' => 6, 'is_active' => true],
            ['office_name' => 'Municipal/City Accountant\'s Office', 'office_code' => 'ACCOUNTANT', 'office_short_name' => 'Accountant', 'office_description' => 'Office of the Municipal/City Accountant', 'order_sequence' => 7, 'is_active' => true],
            ['office_name' => 'Municipal/City Engineer\'s Office', 'office_code' => 'ENGINEERING', 'office_short_name' => 'Engineering', 'office_description' => 'Office of the Municipal/City Engineer', 'order_sequence' => 8, 'is_active' => true],
            ['office_name' => 'Municipal/City Health Office', 'office_code' => 'MCHO', 'office_short_name' => 'MCHO', 'office_description' => 'Municipal/City Health Office', 'order_sequence' => 9, 'is_active' => true],
            ['office_name' => 'Municipal/City Social Welfare and Development Office', 'office_code' => 'MSWDO', 'office_short_name' => 'MSWDO', 'office_description' => 'Municipal/City Social Welfare and Development Office', 'order_sequence' => 10, 'is_active' => true],
            ['office_name' => 'Municipal/City Planning and Development Office', 'office_code' => 'MPDO', 'office_short_name' => 'MPDO', 'office_description' => 'Municipal/City Planning and Development Office', 'order_sequence' => 11, 'is_active' => true],
            ['office_name' => 'Municipal/City Civil Registrar\'s Office', 'office_code' => 'CR', 'office_short_name' => 'Civil Registrar', 'office_description' => 'Municipal/City Civil Registrar\'s Office', 'order_sequence' => 12, 'is_active' => true],
            ['office_name' => 'Municipal/City Budget Office', 'office_code' => 'BUDGET', 'office_short_name' => 'Budget', 'office_description' => 'Municipal/City Budget Office', 'order_sequence' => 13, 'is_active' => true],
            ['office_name' => 'Municipal/City Legal Office', 'office_code' => 'LEGAL', 'office_short_name' => 'Legal', 'office_description' => 'Municipal/City Legal Office', 'order_sequence' => 14, 'is_active' => true],
            ['office_name' => 'Municipal/City Assessor\'s Office', 'office_code' => 'ASSESSOR', 'office_short_name' => 'Assessor', 'office_description' => 'Municipal/City Assessor\'s Office', 'order_sequence' => 15, 'is_active' => true],
            ['office_name' => 'Business Permits and Licensing Office', 'office_code' => 'BPLS', 'office_short_name' => 'BPLS', 'office_description' => 'Business Permits and Licensing Office', 'order_sequence' => 16, 'is_active' => true],
            ['office_name' => 'Municipal/City Agriculture Office', 'office_code' => 'MAO', 'office_short_name' => 'MAO', 'office_description' => 'Municipal/City Agriculture Office', 'order_sequence' => 17, 'is_active' => true],
            ['office_name' => 'Municipal/City Veterinary Office', 'office_code' => 'VET', 'office_short_name' => 'Vet', 'office_description' => 'Municipal/City Veterinary Office', 'order_sequence' => 18, 'is_active' => true],
            ['office_name' => 'Municipal/City Environment and Natural Resources Office', 'office_code' => 'MENRO', 'office_short_name' => 'MENRO', 'office_description' => 'Municipal/City Environment and Natural Resources Office', 'order_sequence' => 19, 'is_active' => true],
            ['office_name' => 'Municipal/City Disaster Risk Reduction and Management Office', 'office_code' => 'DRRMO', 'office_short_name' => 'DRRMO', 'office_description' => 'Municipal/City Disaster Risk Reduction and Management Office', 'order_sequence' => 20, 'is_active' => true],
            ['office_name' => 'Municipal/City Tourism Office', 'office_code' => 'TOURISM', 'office_short_name' => 'Tourism', 'office_description' => 'Municipal/City Tourism Office', 'order_sequence' => 21, 'is_active' => true],
            ['office_name' => 'Municipal/City General Services Office', 'office_code' => 'GSO', 'office_short_name' => 'GSO', 'office_description' => 'Municipal/City General Services Office', 'order_sequence' => 22, 'is_active' => true],
            ['office_name' => 'Municipal/City Library', 'office_code' => 'LIBRARY', 'office_short_name' => 'Library', 'office_description' => 'Municipal/City Library', 'order_sequence' => 23, 'is_active' => true],
            ['office_name' => 'Municipal/City Sports and Youth Development Office', 'office_code' => 'SYDO', 'office_short_name' => 'SYDO', 'office_description' => 'Municipal/City Sports and Youth Development Office', 'order_sequence' => 24, 'is_active' => true],
        ];

        foreach ($offices as $office) {
            Office::updateOrCreate(
                ['office_code' => $office['office_code']],
                $office
            );
        }

        $this->command->info('Offices seeded.');
    }

    protected function seedDivisions(): void
    {
        $hrOffice = Office::where('office_code', 'HRMO')->first();
        $adminOffice = Office::where('office_code', 'ADMIN')->first();
        $treasuryOffice = Office::where('office_code', 'TREASURY')->first();
        $mchoOffice = Office::where('office_code', 'MCHO')->first();

        $divisions = [];

        if ($hrOffice) {
            $divisions[] = ['division_name' => 'HRD', 'division_code' => 'HRMO-HRD', 'office_id' => $hrOffice->id, 'order_sequence' => 1];
            $divisions[] = ['division_name' => 'Recruitment and Selection', 'division_code' => 'HRMO-RS', 'office_id' => $hrOffice->id, 'order_sequence' => 2];
            $divisions[] = ['division_name' => 'Benefits and Compensation', 'division_code' => 'HRMO-BC', 'office_id' => $hrOffice->id, 'order_sequence' => 3];
        }

        if ($adminOffice) {
            $divisions[] = ['division_name' => 'Administrative Services', 'division_code' => 'ADMIN-AS', 'office_id' => $adminOffice->id, 'order_sequence' => 1];
            $divisions[] = ['division_name' => 'Records Management', 'division_code' => 'ADMIN-RM', 'office_id' => $adminOffice->id, 'order_sequence' => 2];
        }

        if ($treasuryOffice) {
            $divisions[] = ['division_name' => 'Revenue Collection', 'division_code' => 'TREAS-RC', 'office_id' => $treasuryOffice->id, 'order_sequence' => 1];
            $divisions[] = ['division_name' => 'Disbursement', 'division_code' => 'TREAS-DB', 'office_id' => $treasuryOffice->id, 'order_sequence' => 2];
        }

        if ($mchoOffice) {
            $divisions[] = ['division_name' => 'Health Services', 'division_code' => 'MCHO-HS', 'office_id' => $mchoOffice->id, 'order_sequence' => 1];
            $divisions[] = ['division_name' => 'Sanitation', 'division_code' => 'MCHO-SAN', 'office_id' => $mchoOffice->id, 'order_sequence' => 2];
        }

        foreach ($divisions as $division) {
            Division::updateOrCreate(
                ['division_code' => $division['division_code']],
                $division
            );
        }

        $this->command->info('Divisions seeded.');
    }

    protected function seedJobPositions(): void
    {
        $hrOffice = Office::where('office_code', 'HRMO')->first();
        $mayorOffice = Office::where('office_code', 'MAYOR')->first();
        $adminOffice = Office::where('office_code', 'ADMIN')->first();

        $permType = EmploymentType::where('type_code', 'PERM')->first();
        $sg11 = SalaryGrade::where('grade_number', 11)->first();
        $sg18 = SalaryGrade::where('grade_number', 18)->first();
        $sg24 = SalaryGrade::where('grade_number', 24)->first();

        $positions = [];

        if ($hrOffice && $permType && $sg18) {
            $positions[] = [
                'position_name' => 'Human Resource Management Officer III',
                'position_code' => 'HRMO-HRM03',
                'office_id' => $hrOffice->id,
                'salary_grade_id' => $sg18->id,
                'employment_type_id' => $permType->id,
                'position_level' => 'Head',
                'item_number' => 1,
                'is_vacant' => true,
            ];
        }

        if ($hrOffice && $permType && $sg11) {
            $positions[] = [
                'position_name' => 'Administrative Officer II',
                'position_code' => 'HRMO-AO2',
                'office_id' => $hrOffice->id,
                'salary_grade_id' => $sg11->id,
                'employment_type_id' => $permType->id,
                'position_level' => 'Staff',
                'item_number' => 2,
                'is_vacant' => true,
            ];
        }

        if ($mayorOffice && $permType && $sg24) {
            $positions[] = [
                'position_name' => 'Municipal Mayor',
                'position_code' => 'MAYOR-MAYOR',
                'office_id' => $mayorOffice->id,
                'salary_grade_id' => $sg24->id,
                'employment_type_id' => $permType->id,
                'position_level' => 'Elective',
                'item_number' => 1,
                'is_vacant' => false,
            ];
        }

        if ($adminOffice && $permType && $sg18) {
            $positions[] = [
                'position_name' => 'Municipal Administrator',
                'position_code' => 'ADMIN-MADMIN',
                'office_id' => $adminOffice->id,
                'salary_grade_id' => $sg18->id,
                'employment_type_id' => $permType->id,
                'position_level' => 'Head',
                'item_number' => 1,
                'is_vacant' => true,
            ];
        }

        if ($adminOffice && $permType && $sg11) {
            $positions[] = [
                'position_name' => 'Administrative Officer I',
                'position_code' => 'ADMIN-AO1',
                'office_id' => $adminOffice->id,
                'salary_grade_id' => $sg11->id,
                'employment_type_id' => $permType->id,
                'position_level' => 'Staff',
                'item_number' => 2,
                'is_vacant' => true,
            ];
        }

        foreach ($positions as $position) {
            JobPosition::updateOrCreate(
                ['position_code' => $position['position_code']],
                $position
            );
        }

        $this->command->info('Job positions seeded.');
    }

    protected function seedPlantilla(): void
    {
        $permType = EmploymentType::where('type_code', 'PERM')->first();
        $casType = EmploymentType::where('type_code', 'CAS')->first();
        $sg1 = SalaryGrade::where('grade_number', 1)->first();
        $sg6 = SalaryGrade::where('grade_number', 6)->first();
        $sg9 = SalaryGrade::where('grade_number', 9)->first();
        $sg11 = SalaryGrade::where('grade_number', 11)->first();
        $sg12 = SalaryGrade::where('grade_number', 12)->first();
        $sg15 = SalaryGrade::where('grade_number', 15)->first();
        $sg18 = SalaryGrade::where('grade_number', 18)->first();
        $sg24 = SalaryGrade::where('grade_number', 24)->first();

        $hrOffice = Office::where('office_code', 'HRMO')->first();
        $mayorOffice = Office::where('office_code', 'MAYOR')->first();
        $adminOffice = Office::where('office_code', 'ADMIN')->first();
        $treasuryOffice = Office::where('office_code', 'TREASURY')->first();
        $mchoOffice = Office::where('office_code', 'MCHO')->first();
        $mswdoOffice = Office::where('office_code', 'MSWDO')->first();

        $plantilla = [
            // Mayor's Office
            [
                'item_number' => 'MAYOR-2024-001',
                'position_title' => 'Municipal Mayor',
                'office_id' => $mayorOffice?->id,
                'salary_grade_id' => $sg24?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Elective',
                'is_vacant' => false,
                'effectivity_date' => '2022-06-30',
            ],
            // HRMO
            [
                'item_number' => 'HRMO-2024-001',
                'position_title' => 'Human Resource Management Officer III',
                'office_id' => $hrOffice?->id,
                'salary_grade_id' => $sg18?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Head',
                'is_vacant' => true,
                'effectivity_date' => '2024-01-01',
            ],
            [
                'item_number' => 'HRMO-2024-002',
                'position_title' => 'Administrative Officer II',
                'office_id' => $hrOffice?->id,
                'salary_grade_id' => $sg11?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Staff',
                'is_vacant' => true,
                'effectivity_date' => '2024-01-01',
            ],
            // Admin Office
            [
                'item_number' => 'ADMIN-2024-001',
                'position_title' => 'Municipal Administrator',
                'office_id' => $adminOffice?->id,
                'salary_grade_id' => $sg18?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Head',
                'is_vacant' => true,
                'effectivity_date' => '2024-01-01',
            ],
            [
                'item_number' => 'ADMIN-2024-002',
                'position_title' => 'Administrative Officer I',
                'office_id' => $adminOffice?->id,
                'salary_grade_id' => $sg11?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Staff',
                'is_vacant' => false,
                'effectivity_date' => '2024-01-01',
            ],
            // Treasury Office
            [
                'item_number' => 'TREAS-2024-001',
                'position_title' => 'Municipal Treasurer',
                'office_id' => $treasuryOffice?->id,
                'salary_grade_id' => $sg18?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Head',
                'is_vacant' => false,
                'effectivity_date' => '2022-06-30',
            ],
            // MCHO
            [
                'item_number' => 'MCHO-2024-001',
                'position_title' => 'Municipal Health Officer',
                'office_id' => $mchoOffice?->id,
                'salary_grade_id' => $sg18?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Head',
                'is_vacant' => true,
                'effectivity_date' => '2024-01-01',
            ],
            // MSWDO
            [
                'item_number' => 'MSWDO-2024-001',
                'position_title' => 'Municipal Social Welfare and Development Officer',
                'office_id' => $mswdoOffice?->id,
                'salary_grade_id' => $sg15?->id,
                'salary_step' => 1,
                'employment_type_id' => $permType?->id,
                'position_level' => 'Head',
                'is_vacant' => true,
                'effectivity_date' => '2024-01-01',
            ],
        ];

        foreach ($plantilla as $item) {
            Plantilla::updateOrCreate(
                ['item_number' => $item['item_number']],
                $item
            );
        }

        $this->command->info('Plantilla positions seeded.');
    }
}
