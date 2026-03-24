<?php

use App\Models\RPT\FaasProperty;
use App\Models\RPT\TaxDeclaration;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mismatches = TaxDeclaration::whereIn('status', ['approved', 'forwarded'])
    ->whereHas('property', function($q) {
        $q->where('status', 'inactive');
    })->get();

echo "Found " . $mismatches->count() . " mismatching Tax Declarations.\n";

foreach ($mismatches as $td) {
    echo "Updating TD ID: {$td->id} (No: {$td->td_no}) from [{$td->status}] to [inactive]...\n";
    $td->update([
        'status'      => 'inactive',
        'inactive_at' => $td->property->inactive_at ?? now(),
        'remarks'     => $td->remarks . " | [AUTO-SYNC] Set to inactive because Mother FAAS is inactive."
    ]);
}

echo "Cleanup completed.\n";
