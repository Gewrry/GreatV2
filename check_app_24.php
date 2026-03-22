<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\BusinessEntry;

$id = 24;
$b = BusinessEntry::find($id);

if ($b) {
    echo "--- Business Entry ID $id ---\n";
    echo "Name: " . $b->business_name . "\n";
    echo "Due: " . ($b->renewal_cycle > 0 ? $b->renewal_total_due : $b->total_due) . "\n";
    echo "Paid: " . $b->total_paid . "\n";
    echo "D10: " . ($b->discount_10 ? 'Yes' : 'No') . "\n";
    echo "D5: " . ($b->discount_5 ? 'Yes' : 'No') . "\n";
    echo "Outstanding Balance (Model): " . $b->outstanding_balance . "\n";
    
    // Check Controller Logic
    $controller = new \App\Http\Controllers\BusinessListController();
    $reflector = new ReflectionClass($controller);
    $method = $reflector->getMethod('computeOutstandingBalance');
    $method->setAccessible(true);
    $balanceInfo = $method->invoke($controller, $b);
    
    echo "\n--- Controller computeOutstandingBalance ---\n";
    echo "Total Due (calculated): " . $balanceInfo['total_due'] . "\n";
    echo "Total Paid (calculated): " . $balanceInfo['total_paid'] . "\n";
    echo "Unpaid Balance: " . $balanceInfo['unpaid_balance'] . "\n";
    echo "Can Retire: " . ($balanceInfo['can_retire'] ? 'Yes' : 'No') . "\n";
} else {
    echo "BusinessEntry $id not found.\n";
}
