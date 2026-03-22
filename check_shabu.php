<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;

$entry = BusinessEntry::where('business_name', 'like', '%shabushabu231%')
    ->with(['bpls_application', 'online_payments', 'payments'])
    ->first();

if (!$entry) {
    echo "Business not found.\n";
    exit;
}

echo "Business: " . $entry->business_name . " (ID: " . $entry->id . ")\n";
echo "Active Total Due: " . $entry->active_total_due . "\n";
echo "Total Paid: " . $entry->total_paid . "\n";
echo "Outstanding Balance: " . $entry->outstanding_balance . "\n";

if ($entry->bpls_application) {
    echo "\nOnline Application:\n";
    echo "  Total Due: " . $entry->bpls_application->renewal_total_due . "\n";
    echo "  Discount Claimed: " . ($entry->bpls_application->discount_claimed ? 'Yes' : 'No') . "\n";
    echo "  Discount-aware Total: " . ($entry->bpls_application->renewal_total_due * 0.9) . "\n";
}

echo "\nOnline Payments:\n";
foreach ($entry->online_payments as $p) {
    echo "  Amount: " . $p->amount . " (Status: " . $p->payment_status . ")\n";
}

echo "\nMaster Payments:\n";
foreach ($entry->payments as $p) {
    echo "  Amount: " . $p->amount . "\n";
}
