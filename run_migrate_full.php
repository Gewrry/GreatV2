<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

try {
    echo "Running artisan migrate...\n";
    $status = Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    file_put_contents('migration_error.log', "Exit Code: $status\nOutput:\n" . $output);
    echo "Done. Check migration_error.log\n";
} catch (\Exception $e) {
    $msg = "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    file_put_contents('migration_error.log', $msg);
    echo "Exception occurred. Check migration_error.log\n";
}
