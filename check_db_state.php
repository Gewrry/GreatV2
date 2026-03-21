<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$table = 'users';

if (Schema::hasTable($table)) {
    echo "Table: $table EXISTS\n";
    $columns = DB::select("SHOW COLUMNS FROM $table");
    foreach ($columns as $column) {
        echo " - Field: " . $column->Field . " | Type: " . $column->Type . "\n";
    }
} else {
    echo "Table: $table DOES NOT EXIST\n";
}

$tables = DB::select('SHOW TABLES');
echo "\nAll Tables:\n";
foreach ($tables as $t) {
    echo " - " . array_values((array)$t)[0] . "\n";
}
