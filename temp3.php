<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$application = \App\Models\onlineBPLS\BplsOnlineApplication::find(30);
if ($application && $application->business) {
    echo "Business ID: " . $application->business->id . "\n";
    echo "Business Cycle: " . $application->business->renewal_cycle . "\n";
    
    // Force set it to 1 just to be super sure!
    $application->business->renewal_cycle = 1;
    $application->business->save();
    echo "Saved to 1!\n";
} else {
    echo "Not found!\n";
}
