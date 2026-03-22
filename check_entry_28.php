<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\BusinessEntry;

$id = 28;
$b = BusinessEntry::find($id);

if ($b) {
    echo "--- Business Entry ID $id ---\n";
    echo "Name: " . $b->business_name . "\n";
    echo "Status: " . $b->status . "\n";
    echo "Retirement Date: " . ($b->retirement_date ?? 'NULL') . "\n";
    echo "Retirement Reason: " . ($b->retirement_reason ?? 'NULL') . "\n";
    echo "Retirement Remarks: " . ($b->retirement_remarks ?? 'NULL') . "\n";
    
    $online = $b->bplsApplication;
    if ($online) {
        echo "\n--- Linked Online App ID " . $online->id . " ---\n";
        echo "Status: " . $online->workflow_status . "\n";
        echo "Retirement Date: " . ($online->retirement_date ?? 'NULL') . "\n";
        echo "Retirement Reason: " . ($online->retirement_reason ?? 'NULL') . "\n";
        echo "Retirement Remarks: " . ($online->retirement_remarks ?? 'NULL') . "\n";
    } else {
        echo "\nNo linked online application found.\n";
    }
} else {
    echo "BusinessEntry $id not found.\n";
}
