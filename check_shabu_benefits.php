<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;

$entry = BusinessEntry::where('business_name', 'like', '%shabushabu231%')->first();

if (!$entry) {
    echo "Business not found.\n";
    exit;
}

echo "Business: " . $entry->business_name . " (ID: " . $entry->id . ")\n";
echo "Benefits:\n";
foreach ($entry->benefits as $b) {
    echo "  - " . $b->name . " (" . $b->discount_percent . "%)\n";
}

echo "\nModel Balance: " . $entry->outstanding_balance . "\n";
echo "Model Total Paid: " . $entry->total_paid . "\n";
echo "Active Total Due: " . $entry->active_total_due . "\n";
