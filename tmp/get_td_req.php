<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("SELECT COLUMN_NAME 
                       FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_NAME = 'tax_declarations' 
                       AND IS_NULLABLE = 'NO' 
                       AND COLUMN_DEFAULT IS NULL 
                       AND COLUMN_NAME NOT IN ('id', 'created_at', 'updated_at')");
foreach ($columns as $col) {
    echo $col->COLUMN_NAME . "\n";
}
