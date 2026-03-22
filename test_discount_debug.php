<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessEntry;
use App\Models\onlineBPLS\BplsOnlineApplication;

// Find "testing" business
$app = BplsOnlineApplication::where('application_number', 'LIKE', '%testing%')
    ->orWhereHas('business', function($q) {
        $q->where('business_name', 'LIKE', '%testing%');
    })
    ->first();

if (!$app) {
    // Try by amount from screenshot
    $app = BplsOnlineApplication::where('assessment_amount', 2580)->first();
}

if ($app) {
    echo "App ID: " . $app->id . " Num: " . $app->application_number . "\n";
    echo "Assessment: " . $app->assessment_amount . "\n";
    
    $entry = BusinessEntry::find($app->bpls_business_id);
    if ($entry) {
        echo "Entry Found. ID: " . $entry->id . "\n";
        echo "Discount 10: " . ($entry->discount_10 ? 'YES' : 'NO') . "\n";
        echo "Discount 5: " . ($entry->discount_5 ? 'YES' : 'NO') . "\n";
    } else {
        echo "No linked BusinessEntry for ID " . $app->bpls_business_id . "\n";
    }
} else {
    echo "Application matching criteria not found.\n";
}
