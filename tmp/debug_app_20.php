<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = \App\Models\onlineBPLS\BplsOnlineApplication::where('application_number', 'APP-2026-00020')->first();
if ($a) {
    echo "ID: " . $a->id . "\n";
    echo "Assessment: " . $a->assessment_amount . "\n";
    echo "Workflow Status: " . $a->workflow_status . "\n";
    echo "ORs: " . $a->orAssignments->count() . "\n";
    foreach ($a->orAssignments as $or) {
        echo "  OR: " . $or->or_number . " Status: " . $or->status . "\n";
    }
    
    $mp = \App\Models\BplsPayment::where('bpls_application_id', $a->id)->sum('amount_paid');
    echo "Master Paid: " . $mp . "\n";
    
    $op = \App\Models\onlineBPLS\BplsOnlinePayment::where('bpls_application_id', $a->id)->where('status', 'paid')->sum('amount_paid');
    echo "Online Paid: " . $op . "\n";
} else {
    echo "Not found\n";
}
