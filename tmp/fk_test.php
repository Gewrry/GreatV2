<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;

DB::listen(function($query) {
    echo "SQL: " . $query->sql . " [" . implode(',', $query->bindings) . "]\n";
});

try {
    $barangay = Barangay::first();
    $p = FaasProperty::create([
        'property_type' => 'L',
        'owner_name' => 'FK TEST',
        'barangay_id' => $barangay->id,
        'status' => 'approved',
        'arp_no' => 'TEST-' . rand(100,999),
        'pin' => 'PIN-' . rand(100,999),
    ]);
    echo "P_ID: $p->id\n";

    $l = FaasLand::create([
        'faas_property_id' => $p->id,
        'rpta_actual_use_id' => 2,
        'area_sqm' => 100,
        'unit_value' => 100,
        'base_market_value' => 10000,
        'market_value' => 10000,
        'assessment_level' => 0.20,
        'assessed_value' => 2000,
    ]);
    echo "L_ID: $l->id\n";
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
