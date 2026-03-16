<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\RPT\TaxDeclaration;
use App\Models\Barangay;
use App\Models\User;

try {
    $barangay = Barangay::first();
    $user = User::first();
    $actualUse = \App\Models\RPT\RptaActualUse::first();

    echo "Step 1: Creating Property...\n";
    $property = FaasProperty::create([
        'property_type' => 'L',
        'owner_name' => 'Debug Owner ' . rand(1, 100),
        'owner_address' => 'Debug Address',
        'barangay_id' => $barangay->id,
        'status' => 'approved',
        'arp_no' => 'ARP-DEBUG-' . rand(10000, 99999),
        'pin' => 'PIN-DEBUG-' . rand(10000, 99999),
    ]);

    echo "Step 2: Creating Land...\n";
    $land = FaasLand::create([
        'faas_property_id' => $property->id,
        'survey_no' => 'SUR-D-' . rand(1000, 9999),
        'area_sqm' => 1000,
        'rpta_actual_use_id' => $actualUse->id,
        'unit_value' => 1500,
        'base_market_value' => 1500000,
        'market_value' => 1500000,
        'assessment_level' => 0.20,
        'assessed_value' => 300000,
    ]);

    echo "Step 3: Creating TD...\n";
    $td = TaxDeclaration::create([
        'faas_property_id' => $property->id,
        'faas_land_id' => $land->id,
        'td_no' => 'TD-DEBUG-' . rand(10000, 99999),
        'revision_year_id' => 1,
        'total_market_value' => 1500000,
        'total_assessed_value' => 300000,
        'property_type' => 'L',
        'property_kind' => 'land',
        'is_taxable' => true,
        'tax_rate' => 0.02,
        'status' => 'forwarded',
        'effectivity_year' => 2023,
        'created_by' => $user->id,
    ]);

    echo "All steps successful!\n";

} catch (\Exception $e) {
    file_put_contents('c:\xampp\htdocs\GreatV2\tmp\fatal_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "ERROR CAPTURED IN tmp/fatal_error.txt\n";
}
