<?php

use App\Models\RPT\FaasProperty;
use App\Services\RPT\FaasValidationService;
use Illuminate\Validation\ValidationException;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$validator = app(FaasValidationService::class);

// Find a property with a balance (or create a scenario)
$p = FaasProperty::where('status', 'approved')->first();

if (!$p) {
    echo "No approved property found.\n";
    exit;
}

echo "Testing Consolidation Validation for ARP: {$p->arp_no} (Status: {$p->status})\n";

try {
    $validator->assertCanConsolidate([$p->id]);
    echo "PASS: Validation passed (Wait, did it have a balance?)\n";
} catch (ValidationException $e) {
    echo "FAIL: Validation caught error: " . collect($e->errors())->flatten()->first() . "\n";
}
