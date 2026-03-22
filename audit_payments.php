<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\onlineBPLS\BplsOnlineApplication;
use App\Models\onlineBPLS\BplsOnlinePayment;

$entryID = 18;
$appID = 10;

$entry = BusinessEntry::find($entryID);
if ($entry) {
    echo "--- Business Entry $entryID ---\n";
    echo "Name: " . $entry->business_name . "\n";
    echo "Due: " . ($entry->renewal_cycle > 0 ? $entry->renewal_total_due : $entry->total_due) . "\n";
    echo "D10: " . ($entry->discount_10 ? 'Yes' : 'No') . "\n";
    echo "D5: " . ($entry->discount_5 ? 'Yes' : 'No') . "\n";
    echo "Balance (Model): " . $entry->outstanding_balance . "\n";
}
$masterPayments = BplsPayment::where('business_entry_id', $entryID)->get();
foreach ($masterPayments as $p) {
    echo "ID: " . $p->id . " | Amount: " . $p->amount_paid . " | OR: " . ($p->or_number ?? 'N/A') . " | AppID: " . ($p->bpls_application_id ?? 'N/A') . "\n";
}
echo "Total Master: " . $masterPayments->sum('amount_paid') . "\n\n";

echo "--- BplsOnlinePayment (Online) for App $appID ---\n";
$onlinePayments = BplsOnlinePayment::where('bpls_application_id', $appID)->where('status', 'paid')->get();
foreach ($onlinePayments as $p) {
    echo "ID: " . $p->id . " | Amount: " . $p->amount . " | OR: " . ($p->or_number ?? 'N/A') . "\n";
}
echo "Total Online: " . $onlinePayments->sum('amount') . "\n\n";

$a = BplsOnlineApplication::find($appID);
if ($a) {
    echo "App Total Paid (Model Method): " . $a->total_paid . "\n";
    echo "App Assessment: " . $a->assessment_amount . "\n";
    echo "App Balance: " . $a->outstanding_balance . "\n";
}
