<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$app_id = 24; // APP-2026-00023 which is record 28

$master = \App\Models\BplsPayment::where('bpls_application_id', $app_id)->get();
$online = \App\Models\onlineBPLS\BplsOnlinePayment::where('bpls_application_id', $app_id)->where('status', 'paid')->get();

ob_start();
echo "Master Payments:\n";
foreach ($master as $m) {
    echo "  ID: " . $m->id . " OR: [" . $m->or_number . "] Amount: " . $m->amount_paid . "\n";
}

echo "\nOnline Payments:\n";
foreach ($online as $o) {
    echo "  ID: " . $o->id . " OR: [" . $o->or_number . "] Amount: " . $o->amount_paid . "\n";
}

$out = ob_get_clean();
file_put_contents('debug_payments_28.log', $out);
