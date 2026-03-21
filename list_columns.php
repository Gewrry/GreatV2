<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['rpt_property_registrations', 'faas_properties', 'bpls_business_entries'];

foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    if (!Schema::hasTable($table)) {
        echo "Table does not exist!\n\n";
        continue;
    }
    
    $columns = Schema::getColumnListing($table);
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    echo "\n";
}
