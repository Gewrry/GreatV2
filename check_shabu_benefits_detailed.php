<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;

$entry = BusinessEntry::where('business_name', 'like', '%shabushabu231%')
    ->with('benefits')
    ->first();

if (!$entry) {
    echo "Business not found.\n";
    exit;
}

echo "Business: " . $entry->business_name . " (ID: " . $entry->id . ")\n";
echo "Owner ID: " . $entry->bpls_owner_id . "\n";
echo "Benefits from relationship:\n";
foreach ($entry->benefits as $b) {
    echo "  - " . $b->name . " (" . $b->discount_percent . "%)\n";
}

// Check pivot table directly if relationship failed in script
$benefits = \DB::table('bpls_owner_benefits')
    ->join('bpls_benefits', 'bpls_owner_benefits.benefit_id', '=', 'bpls_benefits.id')
    ->where('bpls_owner_benefits.owner_id', $entry->bpls_owner_id)
    ->get();

echo "\nBenefits from pivot table:\n";
foreach ($benefits as $b) {
    echo "  - " . $b->name . " (" . $b->discount_percent . "%)\n";
}

$totalDue = 2030;
$discountAmount = 0;
foreach ($benefits as $b) {
    $discountAmount += $totalDue * ((float)$b->discount_percent / 100);
}
echo "\nCalculated Discount: " . $discountAmount . "\n";
echo "Expected Paid (2030 - discount): " . (2030 - $discountAmount) . "\n";
