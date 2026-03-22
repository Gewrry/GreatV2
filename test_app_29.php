<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\onlineBPLS\BplsOnlineApplication;

$app = BplsOnlineApplication::find(29);
if ($app) {
    echo "App ID: " . $app->id . "\n";
    echo "Assessment Gross: " . $app->assessment_amount . "\n";
    echo "Discount Claimed: " . ($app->discount_claimed ? 'YES' : 'NO') . "\n";
    echo "Total Paid: " . $app->total_paid . "\n";
    echo "Outstanding: " . $app->outstanding_balance . "\n";
}
