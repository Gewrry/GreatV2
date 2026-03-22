<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $entry = \App\Models\BusinessEntry::whereHas('bplsApplication')
        ->with(['bplsApplication', 'bplsApplication.orAssignments'])
        ->first();
    if ($entry) {
        file_put_contents('debug.log', json_encode($entry->toArray(), JSON_PRETTY_PRINT));
    } else {
        file_put_contents('debug.log', "No linked entry found");
    }
} catch (\Throwable $e) {
    file_put_contents('debug.log', (string)$e);
}
