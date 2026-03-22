<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;
use App\Models\onlineBPLS\BplsOnlineApplication;

$entries = BusinessEntry::whereNull('deleted_at')->get();
foreach ($entries as $entry) {
    $app = BplsOnlineApplication::where('bpls_business_id', $entry->id)->first();
    if ($app) {
        echo "Entry ID " . $entry->id . " matches App ID " . $app->id . " (bpls_business_id=" . $app->bpls_business_id . ")\n";
    }
}
