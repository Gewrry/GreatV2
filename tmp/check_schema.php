<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$table = 'rpt_properties';
if (Schema::hasTable($table)) {
    echo "$table columns:\n";
    foreach (Schema::getColumnListing($table) as $col) {
        echo "- $col\n";
    }
} else { echo "Table $table not found.\n"; }

$table = 'rpt_lands';
if (Schema::hasTable($table)) {
    echo "\n$table columns:\n";
    foreach (Schema::getColumnListing($table) as $col) {
        echo "- $col\n";
    }
} else { echo "Table $table not found.\n"; }

