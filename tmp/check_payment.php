<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = \App\Models\RPT\RptPayment::find(30);
if ($p) {
    echo "ID: " . $p->id . "\n";
    echo "Mode: " . $p->payment_mode . "\n";
    echo "Bank: " . $p->bank_name . "\n";
    echo "Check: " . $p->check_no . "\n";
} else {
    echo "Payment not found\n";
}
