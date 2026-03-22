<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;

$entry = BusinessEntry::find(28);
if ($entry) {
    echo "Entry ID: " . $entry->id . "\n";
    echo "Business Name: " . $entry->business_name . "\n";
    echo "Outstanding Balance: " . $entry->outstanding_balance . "\n";
    echo "Total Paid: " . $entry->total_paid . "\n";
    echo "Active Total Due: " . $entry->active_total_due . "\n";
    
    if ($entry->bplsApplication) {
        $app = $entry->bplsApplication;
        echo "Application Number: " . $app->application_number . "\n";
        echo "App Assessment: " . $app->assessment_amount . "\n";
        echo "App Renewal Total: " . $app->renewal_total_due . "\n";
        echo "App Total Paid: " . $app->total_paid . "\n";
        
        echo "OR Assignments:\n";
        foreach ($app->orAssignments as $or) {
            echo "  OR: " . $or->or_number . " Status: " . $or->status . "\n";
        }
    }
} else {
    echo "Entry 28 not found\n";
}
