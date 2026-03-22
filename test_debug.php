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
        echo json_encode($entry->toArray(), JSON_PRETTY_PRINT);
    } else {
        echo "No linked entry found";
    }
} catch (\Throwable $e) {
    echo (string)$e;
}
