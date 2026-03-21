<?php
/**
 * Smart migration runner.
 * Tries to run migrations one by one. 
 * If a migration fails because a table/column already exists, it marks it as "run" (fakes it).
 * If it fails for other reasons, it stops.
 * This ensures that "add column" migrations run, while "create table" migrations that are already in the backup are skipped.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\QueryException;

$migrator = app('migrator');
$files = $migrator->getMigrationFiles(database_path('migrations'));
$ran = $migrator->getRepository()->getRan();
$pending = array_diff(array_keys($files), $ran);

if (empty($pending)) {
    echo "No pending migrations.\n";
    exit(0);
}

echo "Found " . count($pending) . " pending migrations. Processing one by one...\n\n";

$batch = DB::table('migrations')->max('batch') + 1;

foreach ($pending as $migration) {
    echo "Processing $migration...\n";
    
    try {
        // Try to run this specific migration file
        Artisan::call('migrate', [
            '--path' => 'database/migrations/' . $migration . '.php',
            '--force' => true
        ]);
        echo "  ✓ Successfully run.\n";
    } catch (\Throwable $e) {
        $message = $e->getMessage();
        
        // Check if the error is "Table already exists" or "Column already exists"
        $alreadyExists = (
            str_contains($message, 'already exists') || 
            str_contains($message, 'Duplicate column name') ||
            str_contains($message, '1050 Table') ||
            str_contains($message, '1061 Duplicate key') ||
            str_contains($message, '1060 Duplicate column')
        );

        if ($alreadyExists) {
            echo "  ! Already exists in database. Faking migration record...\n";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch
            ]);
            echo "  ✓ Faked.\n";
        } else {
            echo "\n  ✗ FAILED: $migration\n";
            echo "  Error: " . $message . "\n";
            exit(1);
        }
    }
}

echo "\nAll pending migrations processed! Logic: 'Run if missing, Fake if exists'.\n";
