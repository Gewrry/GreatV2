<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\onlineBPLS\BplsOnlineApplication;
use App\Models\BplsPayment;
use App\Models\onlineBPLS\BplsOnlinePayment;

$app = BplsOnlineApplication::find(22);
if ($app) {
    $totalAssessed = (float)($app->assessment_amount ?? 0);
    
    $masterPayments = BplsPayment::where('bpls_application_id', $app->id)->get();
    $masterPaid = $masterPayments->sum('amount_paid');
    $masterOrs = $masterPayments->pluck('or_number')->filter()->toArray();

    $onlinePaid = BplsOnlinePayment::where('bpls_application_id', $app->id)
        ->where('status', 'paid')
        ->get()
        ->filter(function($p) use ($masterOrs) {
            return empty($p->or_number) || !in_array($p->or_number, $masterOrs);
        })
        ->sum('amount_paid');

    $totalPaid = $masterPaid + $onlinePaid;
    $bal = $totalAssessed - $totalPaid;
    
    printf("APP: %s\nAssessed: %.2f\nTotal Paid (Improved): %.2f\nBal: %.2f\n", 
        $app->application_number, $totalAssessed, $totalPaid, $bal);
}
?>
