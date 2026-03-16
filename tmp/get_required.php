<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['faas_properties', 'faas_lands', 'tax_declarations', 'rpt_billings', 'rpt_payments'];

foreach ($tables as $table) {
    echo "--- $table ---\n";
    $columns = DB::select("SELECT COLUMN_NAME 
                           FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_NAME = '$table' 
                           AND IS_NULLABLE = 'NO' 
                           AND COLUMN_DEFAULT IS NULL 
                           AND COLUMN_NAME NOT IN ('id', 'created_at', 'updated_at')");
    foreach ($columns as $col) {
        echo $col->COLUMN_NAME . "\n";
    }
}
