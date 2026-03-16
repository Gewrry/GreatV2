<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptBilling;
use App\Models\Barangay;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RptDelinquentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::listen(function($query) {
            $this->command->info("SQL: " . $query->sql);
        });

        $barangay = Barangay::first();
        if (!$barangay) {
            $this->command->error('No Barangay found. Please seed barangays first.');
            return;
        }

        $user = User::first();
        if (!$user) {
            $this->command->error('No User found. Please seed users first.');
            return;
        }

        // Cleanup existing test properties to allow re-seeding
        $testTdNos = ['TD-DELQ-001', 'TD-DELQ-002', 'TD-DELQ-003'];
        TaxDeclaration::withoutEvents(function() use ($testTdNos) {
            $existingTds = TaxDeclaration::whereIn('td_no', $testTdNos)->get();
            foreach ($existingTds as $eTd) {
                // Delete related billings and payments
                foreach ($eTd->billings as $billing) {
                    $billing->payments()->delete();
                    $billing->delete();
                }
                
                $propId = $eTd->faas_property_id;
                $landId = $eTd->faas_land_id;

                $eTd->forceDelete();
                
                if ($landId) {
                    FaasLand::where('id', $landId)->delete();
                }
                
                if ($propId) {
                    FaasProperty::where('id', $propId)->forceDelete();
                }
            }
        });

        $currentYear = 2026;

        // GeoJSON Polygons in Majayjay, Laguna area
        $poly1 = [
            "type" => "Feature", "geometry" => [ "type" => "Polygon", "coordinates" => [[ [121.511, 14.111], [121.513, 14.111], [121.513, 14.112], [121.511, 14.112], [121.511, 14.111] ]] ]
        ];
        $poly2 = [
            "type" => "Feature", "geometry" => [ "type" => "Polygon", "coordinates" => [[ [121.515, 14.113], [121.517, 14.113], [121.517, 14.115], [121.515, 14.115], [121.515, 14.113] ]] ]
        ];
        $poly3 = [
            "type" => "Feature", "geometry" => [ "type" => "Polygon", "coordinates" => [[ [121.510, 14.114], [121.512, 14.114], [121.512, 14.116], [121.510, 14.116], [121.510, 14.114] ]] ]
        ];

        try {
            // 1. Fully Delinquent Property (Unpaid for 3 years)
            $this->createDelinquentProperty(
                $barangay,
                $user,
                'Severino Reyes (Fully Delinquent)',
                'TD-DELQ-001',
                [$currentYear - 3, $currentYear - 2, $currentYear - 1],
                'unpaid',
                $poly1
            );

            // 2. Partially Paid Delinquent Property (Some quarters paid in history)
            $this->createDelinquentProperty(
                $barangay,
                $user,
                'Juan Luna (Partial Delinquent)',
                'TD-DELQ-002',
                [$currentYear - 2, $currentYear - 1],
                'mixed',
                $poly2
            );

            // 3. Current Year Partial (Paid Q1, but Q2-Q4 pending)
            $this->createDelinquentProperty(
                $barangay,
                $user,
                'Melchora Aquino (Current Delinquent)',
                'TD-DELQ-003',
                [$currentYear],
                'current_partial',
                $poly3
            );
        } catch (\Exception $e) {
            file_put_contents('c:\xampp\htdocs\GreatV2\tmp\seeder_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->command->error($e->getMessage());
        }

        $this->command->info('Delinquent RPT test properties seeded successfully!');
    }

    private function createDelinquentProperty($barangay, $user, $ownerName, $tdNo, $years, $type, $polygon)
    {
        $actualUse = \App\Models\RPT\RptaActualUse::where('name', 'like', '%Residential%')->first() 
                   ?? \App\Models\RPT\RptaActualUse::first();
        
        $revYear = \App\Models\RPT\RptaRevisionYear::where('year', 2026)->first()
                   ?? \App\Models\RPT\RptaRevisionYear::first();

        echo "Creating Property: $ownerName\n";
        $property = FaasProperty::create([
            'property_type' => 'L',
            'owner_name' => $ownerName,
            'owner_address' => 'Sample Address, ' . $barangay->name,
            'barangay_id' => $barangay->id,
            'status' => 'approved',
            'arp_no' => 'ARP-' . rand(10000, 99999),
            'pin' => 'PIN-' . rand(10000, 99999),
            'polygon_coordinates' => $polygon,
        ]);

        echo "Creating Land for $property->id\n";
        $land = FaasLand::create([
            'faas_property_id' => $property->id,
            'survey_no' => 'SUR-' . rand(1000, 9999),
            'area_sqm' => 1000,
            'rpta_actual_use_id' => $actualUse->id,
            'unit_value' => 1500,
            'base_market_value' => 1500000,
            'market_value' => 1500000,
            'assessment_level' => 0.20,
            'assessed_value' => 300000,
        ]);

        echo "Creating Tax Declaration for $land->id\n";
        $td = TaxDeclaration::create([
            'faas_property_id' => $property->id,
            'faas_land_id' => $land->id,
            'td_no' => $tdNo,
            'revision_year_id' => $revYear->id,
            'total_market_value' => 1500000,
            'total_assessed_value' => 300000,
            'property_type' => 'land',
            'property_kind' => 'land',
            'effectivity_quarter' => 1,
            'is_taxable' => true,
            'tax_rate' => 0.02,
            'status' => 'approved',
            'effectivity_year' => min($years),
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        echo "Creating Billings for $td->id\n";
        foreach ($years as $year) {
            for ($q = 1; $q <= 4; $q++) {
                $status = 'unpaid';
                $amtPaid = 0;

                if ($type === 'mixed') {
                    if ($year === min($years)) {
                        $status = 'paid';
                        $amtPaid = 1500; 
                    }
                } elseif ($type === 'current_partial') {
                    if ($q === 1) {
                        $status = 'paid';
                        $amtPaid = 1500;
                    }
                }

                $basic = 750; 
                $sef = 750;   
                $total = $basic + $sef;

                $billing = RptBilling::create([
                    'tax_declaration_id' => $td->id,
                    'tax_year' => $year,
                    'quarter' => $q,
                    'basic_tax' => $basic,
                    'sef_tax' => $sef,
                    'total_tax_due' => $total,
                    'discount_amount' => 0,
                    'penalty_amount' => 0,
                    'total_amount_due' => $total,
                    'amount_paid' => $amtPaid,
                    'balance' => $total - $amtPaid,
                    'status' => $status,
                    'due_date' => Carbon::create($year, $q * 3, 20)->toDateString(),
                ]);

                if ($amtPaid > 0) {
                    \App\Models\RPT\RptPayment::create([
                        'rpt_billing_id' => $billing->id,
                        'or_no' => 'OR-' . rand(100000, 999999),
                        'amount' => $amtPaid,
                        'payment_date' => Carbon::create($year, $q * 3, 5),
                        'payment_mode' => 'cash',
                        'status' => 'completed',
                        'collected_by' => $user->id,
                        'basic_tax' => $basic,
                        'sef_tax' => $sef,
                        'discount' => 0,
                        'penalty' => 0,
                    ]);
                }
            }
        }
    }
}
