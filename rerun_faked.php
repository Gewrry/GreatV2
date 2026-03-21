<?php
/**
 * Remove the faked migration records (the ones we inserted with the fix script)
 * so they can be properly re-run by `php artisan migrate`.
 *
 * We identify them as being in the highest batch number (the one our fix script used).
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$maxBatch = DB::table('migrations')->max('batch');
echo "Max batch: $maxBatch\n";

$faked = DB::table('migrations')->where('batch', $maxBatch)->pluck('migration');
echo "Found " . $faked->count() . " faked migrations in batch $maxBatch:\n";
foreach ($faked as $m) {
    echo "  - $m\n";
}

echo "\nDeleting these records so they can be re-run...\n";
DB::table('migrations')->where('batch', $maxBatch)->delete();
echo "Done! Now run: php artisan migrate\n";
