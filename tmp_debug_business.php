<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\BusinessEntry;

$entry = BusinessEntry::where('business_name', 'shabushabu23')->first();

if ($entry) {
    echo "ID: " . $entry->id . "\n";
    echo "Status: " . $entry->status . "\n";
    echo "Total Due: " . $entry->active_total_due . "\n";
    echo "Total Paid: " . $entry->total_paid . "\n";
    echo "Balance: " . $entry->outstanding_balance . "\n";
    
    echo "\nBenefits:\n";
    foreach ($entry->benefits as $b) {
        echo "- " . $b->name . " (Key: " . $b->field_key . ")\n";
    }

    echo "\nPayments:\n";
    foreach ($entry->payments as $p) {
        echo "- Amount: " . $p->amount_paid . " | OR: " . $p->or_number . " | Status: " . $p->status . "\n";
    }
} else {
    echo "Not found\n";
}
