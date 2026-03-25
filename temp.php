<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payments = \App\Models\BplsPayment::where('bpls_application_id', 30)->get();
foreach($payments as $p) {
    echo "ID: " . $p->id . ", Year: " . $p->payment_year . ", Cycle: " . $p->renewal_cycle . ", Total: " . $p->total_collected . "\n";
}
