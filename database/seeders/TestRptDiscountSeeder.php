<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptBilling;
use App\Models\Barangay;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TestRptDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $barangay = Barangay::first();
        if (!$barangay) {
            $this->command->error('No Barangay found. Please seed barangays first.');
            return;
        }

        // 1. Create a Property for Prompt Discount Testing (Current Year)
        $propertyPrompt = FaasProperty::create([
            'property_type' => 'L',
            'owner_name' => 'John Doe (Prompt Test)',
            'owner_address' => '123 Fake Street',
            'barangay_id' => $barangay->id,
            'status' => 'approved',
            'arp_no' => 'ARP-PROMPT-' . rand(1000, 9999),
            'pin' => 'PIN-PROMPT-' . rand(1000, 9999),
        ]);

        FaasLand::create([
            'faas_property_id' => $propertyPrompt->id,
            'survey_no' => 'SUR-' . rand(1000, 9999),
            'area_sqm' => 500,
            'rpta_actual_use_id' => \App\Models\RPT\RptaActualUse::first()->id ?? 1,
            'unit_value' => 1000,
            'base_market_value' => 500000,
            'market_value' => 500000,
            'assessment_level' => 0.20,
            'assessed_value' => 100000,
        ]);

        $tdPrompt = TaxDeclaration::create([
            'faas_property_id' => $propertyPrompt->id,
            'td_no' => 'TD-PROMPT-' . rand(1000, 9999),
            'total_assessed_value' => 100000,
            'status' => 'forwarded', // Must be forwarded to show in treasury
            'effectivity_year' => Carbon::now()->year,
        ]);

        // Create 2026 Quarter 1 and Quarter 2 billings
        $currentYear = Carbon::now()->year;
        $totalTax = 2000; // Example: 1% Basic (1000), 1% SEF (1000)
        
        RptBilling::create([
            'tax_declaration_id' => $tdPrompt->id,
            'tax_year' => $currentYear,
            'quarter' => 1,
            'basic_tax' => 250,
            'sef_tax' => 250,
            'total_tax_due' => 500,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount_due' => 500,
            'amount_paid' => 0,
            'balance' => 500,
            'status' => 'unpaid',
            'due_date' => Carbon::create($currentYear, 3, 31)->toDateString(),
        ]);
        
        RptBilling::create([
            'tax_declaration_id' => $tdPrompt->id,
            'tax_year' => $currentYear,
            'quarter' => 2,
            'basic_tax' => 250,
            'sef_tax' => 250,
            'total_tax_due' => 500,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount_due' => 500,
            'amount_paid' => 0,
            'balance' => 500,
            'status' => 'unpaid',
            'due_date' => Carbon::create($currentYear, 6, 30)->toDateString(),
        ]);


        // 2. Create a Property for Advance Discount Testing (Next Year)
        $propertyAdvance = FaasProperty::create([
            'property_type' => 'L',
            'owner_name' => 'Jane Smith (Advance Test)',
            'owner_address' => '456 Real Street',
            'barangay_id' => $barangay->id,
            'status' => 'approved',
            'arp_no' => 'ARP-ADVANCE-' . rand(1000, 9999),
            'pin' => 'PIN-ADVANCE-' . rand(1000, 9999),
        ]);

        FaasLand::create([
            'faas_property_id' => $propertyAdvance->id,
            'survey_no' => 'SUR-A-' . rand(1000, 9999),
            'area_sqm' => 800,
            'rpta_actual_use_id' => \App\Models\RPT\RptaActualUse::first()->id ?? 1,
            'unit_value' => 1000,
            'base_market_value' => 800000,
            'market_value' => 800000,
            'assessment_level' => 0.20,
            'assessed_value' => 160000,
        ]);

        $tdAdvance = TaxDeclaration::create([
            'faas_property_id' => $propertyAdvance->id,
            'td_no' => 'TD-ADVANCE-' . rand(1000, 9999),
            'total_assessed_value' => 160000,
            'status' => 'forwarded',
            'effectivity_year' => $currentYear,
        ]);

        // Create 2027 Quarter 1 billing (Advance)
        $nextYear = $currentYear + 1;
        
        RptBilling::create([
            'tax_declaration_id' => $tdAdvance->id,
            'tax_year' => $nextYear,
            'quarter' => 1,
            'basic_tax' => 400,
            'sef_tax' => 400,
            'total_tax_due' => 800,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount_due' => 800,
            'amount_paid' => 0,
            'balance' => 800,
            'status' => 'unpaid',
            'due_date' => Carbon::create($nextYear, 3, 31)->toDateString(),
        ]);
        
        RptBilling::create([
            'tax_declaration_id' => $tdAdvance->id,
            'tax_year' => $nextYear,
            'quarter' => 2,
            'basic_tax' => 400,
            'sef_tax' => 400,
            'total_tax_due' => 800,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount_due' => 800,
            'amount_paid' => 0,
            'balance' => 800,
            'status' => 'unpaid',
            'due_date' => Carbon::create($nextYear, 6, 30)->toDateString(),
        ]);

        // 3. Create a Property for Delinquent Discount Testing (Past Years - 2024)
        $propertyDelinquent = FaasProperty::create([
            'property_type' => 'L',
            'owner_name' => 'Bob Brown (Delinquent Test)',
            'owner_address' => '789 Late Street',
            'barangay_id' => $barangay->id,
            'status' => 'approved',
            'arp_no' => 'ARP-LATE-' . rand(1000, 9999),
            'pin' => 'PIN-LATE-' . rand(1000, 9999),
        ]);

        FaasLand::create([
            'faas_property_id' => $propertyDelinquent->id,
            'survey_no' => 'SUR-L-' . rand(1000, 9999),
            'area_sqm' => 600,
            'rpta_actual_use_id' => \App\Models\RPT\RptaActualUse::first()->id ?? 1,
            'unit_value' => 1000,
            'base_market_value' => 600000,
            'market_value' => 600000,
            'assessment_level' => 0.20,
            'assessed_value' => 120000,
        ]);

        $tdDelinquent = TaxDeclaration::create([
            'faas_property_id' => $propertyDelinquent->id,
            'td_no' => 'TD-LATE-' . rand(1000, 9999),
            'total_assessed_value' => 120000,
            'status' => 'forwarded',
            'effectivity_year' => 2023,
        ]);

        // Create 2024 Quarter 1 and Quarter 2 billings (Delinquent)
        $pastYear = 2024;
        
        RptBilling::create([
            'tax_declaration_id' => $tdDelinquent->id,
            'tax_year' => $pastYear,
            'quarter' => 1,
            'basic_tax' => 300,
            'sef_tax' => 300,
            'total_tax_due' => 600,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount_due' => 600,
            'amount_paid' => 0,
            'balance' => 600,
            'status' => 'unpaid',
            'due_date' => Carbon::create($pastYear, 3, 31)->toDateString(),
        ]);
        
        RptBilling::create([
            'tax_declaration_id' => $tdDelinquent->id,
            'tax_year' => $pastYear,
            'quarter' => 2,
            'basic_tax' => 300,
            'sef_tax' => 300,
            'total_tax_due' => 600,
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount_due' => 600,
            'amount_paid' => 0,
            'balance' => 600,
            'status' => 'unpaid',
            'due_date' => Carbon::create($pastYear, 6, 30)->toDateString(),
        ]);

        $this->command->info('Test properties for Prompt, Advance, and Delinquent payment scenarios seeded successfully!');
    }
}
