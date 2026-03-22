<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\onlineBPLS\BplsOnlineApplication;
use App\Models\BusinessEntry;

$appNum = 'APP-2026-00028';
$a = BplsOnlineApplication::where('application_number', $appNum)->first();

echo "--- Online Application ---\n";
if ($a) {
    echo "ID: " . $a->id . "\n";
    echo "Assessed: " . $a->assessment_amount . "\n";
    echo "Total Paid: " . $a->total_paid . "\n";
    echo "Discount Claimed: " . ($a->discount_claimed ? 'Yes' : 'No') . "\n";
    echo "Outstanding Balance (Model): " . $a->outstanding_balance . "\n";
    
    // Check linked master entry
    $entry = BusinessEntry::where('id', $a->bpls_business_id)->first();
    if ($entry) {
        echo "\n--- Business Entry ---\n";
        echo "ID: " . $entry->id . "\n";
        echo "Total Due: " . $entry->total_due . "\n";
        echo "Renewal Total Due: " . $entry->renewal_total_due . "\n";
        echo "Discount 10: " . ($entry->discount_10 ? 'Yes' : 'No') . "\n";
        echo "Discount 5: " . ($entry->discount_5 ? 'Yes' : 'No') . "\n";
        echo "Total Paid (Model): " . $entry->total_paid . "\n";
        echo "Outstanding Balance (Model): " . $entry->outstanding_balance . "\n";
        
        // Check Controller Logic
        $controller = new \App\Http\Controllers\BusinessListController();
        $reflector = new ReflectionClass($controller);
        $method = $reflector->getMethod('computeOutstandingBalance');
        $method->setAccessible(true);
        $balanceInfo = $method->invoke($controller, $entry);
        
        echo "\n--- Controller computeOutstandingBalance ---\n";
        echo "Total Due (calculated): " . $balanceInfo['total_due'] . "\n";
        echo "Total Paid (calculated): " . $balanceInfo['total_paid'] . "\n";
        echo "Unpaid Balance: " . $balanceInfo['unpaid_balance'] . "\n";
        echo "Total Outstanding (inc surcharge): " . ($balanceInfo['total_outstanding'] ?? 'N/A') . "\n";
        echo "Can Retire: " . ($balanceInfo['can_retire'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "\nNo linked BusinessEntry found for this application.\n";
    }
} else {
    echo "Application $appNum not found.\n";
}
