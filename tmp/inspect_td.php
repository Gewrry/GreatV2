<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT 
                       FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_NAME = 'tax_declarations'");

foreach ($columns as $col) {
    echo "Column: {$col->COLUMN_NAME} | Nullable: {$col->IS_NULLABLE} | Default: " . ($col->COLUMN_DEFAULT ?? 'NULL') . "\n";
}
