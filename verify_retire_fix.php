<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;
use App\Http\Controllers\BusinessListController;

$entry = BusinessEntry::where('business_name', 'like', '%shabushabu231%')->first();

if (!$entry) {
    echo "Business not found.\n";
    exit;
}

$controller = new BusinessListController();
$reflection = new \ReflectionClass($controller);
$method = $reflection->getMethod('computeOutstandingBalance');
$method->setAccessible(true);

$result = $method->invoke($controller, $entry);

echo "Business: " . $entry->business_name . "\n";
echo "Total Due: " . $result['total_due'] . "\n";
echo "Total Paid: " . $result['total_paid'] . "\n";
echo "Unpaid Balance: " . $result['unpaid_balance'] . "\n";
echo "Can Retire: " . ($result['can_retire'] ? 'YES' : 'NO') . "\n";
if (!$result['can_retire']) {
    echo "Block Reason: " . $result['block_reason'] . "\n";
}
