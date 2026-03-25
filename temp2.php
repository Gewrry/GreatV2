<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pc = app(\App\Http\Controllers\BplsPaymentController::class);
// Let's use reflection to call private resolveUnifiedEntry
$reflector = new ReflectionObject($pc);
$method = $reflector->getMethod('resolveUnifiedEntry');
$method->setAccessible(true);
$entry = $method->invokeArgs($pc, ['online_30']);

echo "Entry ID: " . $entry->id . "\n";
echo "Entry Permit Year: " . $entry->permit_year . "\n";
echo "Entry Renewal Cycle: " . $entry->renewal_cycle . "\n";
echo "Entry Active Total Due: " . $entry->active_total_due . "\n";

$column = !empty($entry->is_online) ? 'bpls_application_id' : 'business_entry_id';
$activePayments = \App\Models\BplsPayment::where($column, $entry->id)
    ->where('payment_year', $entry->permit_year ?? now()->year)
    ->where('renewal_cycle', $entry->renewal_cycle ?? 0)
    ->orderBy('payment_date', 'desc')->get();

echo "Active Payments count: " . $activePayments->count() . "\n";
foreach($activePayments as $p) {
    echo "  -> Payment ID: " . $p->id . " Cycle: " . $p->renewal_cycle . "\n";
}
