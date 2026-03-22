<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $entry = \App\Models\BusinessEntry::whereNull('deleted_at')->first();
    if ($entry) {
        file_put_contents('debug_entry.log', json_encode($entry->getAttributes(), JSON_PRETTY_PRINT));
    } else {
        file_put_contents('debug_entry.log', "No entry found");
    }
} catch (\Throwable $e) {
    file_put_contents('debug_entry.log', (string)$e);
}
