<?php
$rootDir = 'c:\\xampp\\htdocs\\GreatV2';
require $rootDir . '/vendor/autoload.php';
$app = require_once $rootDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = \App\Models\onlineBPLS\BplsOnlineApplication::where('application_number', 'APP-2026-00020')->first();
if ($a) {
    echo "ID: " . $a->id . "\n";
    echo "Assessment: " . $a->assessment_amount . "\n";
    echo "Workflow Status: " . $a->workflow_status . "\n";
    echo "ORs: " . $a->orAssignments->count() . "\n";
    foreach ($a->orAssignments as $or) {
        echo "  - OR Index: " . $or->id . " OR No: " . $or->or_number . " Status: " . $or->status . "\n";
    }
} else {
    echo "Not found\n";
}
