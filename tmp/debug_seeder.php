<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptBilling;
use App\Models\Barangay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    $barangay = Barangay::first();
    $user = User::first();
    $currentYear = 2026;
    $poly = json_encode([
        "type" => "Feature", "geometry" => [ "type" => "Polygon", "coordinates" => [[ [121.511, 14.111], [121.513, 14.111], [121.513, 14.112], [121.511, 14.112], [121.511, 14.111] ]] ]
    ]);

    $ownerName = "Test Owner " . rand(1, 1000);
    $tdNo = "TD-TEST-" . rand(1000, 9999);

    $property = FaasProperty::create([
        'property_type' => 'L',
        'owner_name' => $ownerName,
        'owner_address' => 'Sample Address, ' . $barangay->name,
        'barangay_id' => $barangay->id,
        'status' => 'approved',
        'arp_no' => 'ARP-' . rand(10000, 99999),
        'pin' => 'PIN-' . rand(10000, 99999),
        'polygon_coordinates' => $poly,
    ]);
    echo "Property created: " . $property->id . "\n";

    $land = FaasLand::create([
        'faas_property_id' => $property->id,
        'survey_no' => 'SUR-' . rand(1000, 9999),
        'area_sqm' => 1000,
        'rpta_actual_use_id' => 1,
        'unit_value' => 1500,
        'base_market_value' => 1500000,
        'market_value' => 1500000,
        'assessment_level' => 0.20,
        'assessed_value' => 300000,
    ]);
    echo "Land created: " . $land->id . "\n";

    $td = TaxDeclaration::create([
        'faas_property_id' => $property->id,
        'faas_land_id' => $land->id,
        'td_no' => $tdNo,
        'revision_year_id' => 1,
        'total_market_value' => 1500000,
        'total_assessed_value' => 300000,
        'property_type' => 'land',
        'property_kind' => 'land',
        'effectivity_quarter' => 1,
        'is_taxable' => true,
        'tax_rate' => 0.02,
        'status' => 'approved',
        'effectivity_year' => $currentYear,
        'created_by' => $user->id,
        'approved_by' => $user->id,
        'approved_at' => now(),
    ]);
    echo "TD created: " . $td->id . "\n";

    DB::commit();
    echo "SUCCESS\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}
