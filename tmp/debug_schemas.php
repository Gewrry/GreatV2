<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['rpt_billings', 'rpt_payments', 'faas_lands', 'tax_declarations', 'faas_properties'];

foreach ($tables as $table) {
    echo "Table: $table\n";
    if (Schema::hasTable($table)) {
        $columns = Schema::getColumnListing($table);
        echo implode(", ", $columns) . "\n\n";
    } else {
        echo "NOT FOUND\n\n";
    }
}
