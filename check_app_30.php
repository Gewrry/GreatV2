<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$application = \App\Models\onlineBPLS\BplsOnlineApplication::find(30);
if ($application) {
    echo "App 30 Year: " . $application->permit_year . "\n";
    echo "App 30 Status: " . $application->workflow_status . "\n";
    
    // If it is 2026, let's bump it to 2027 since it is for renewal payment
    if ($application->permit_year == 2026) {
        $application->update(['permit_year' => 2027]);
        echo "Updated to 2027\n";
    }
} else {
    echo "Not found\n";
}
