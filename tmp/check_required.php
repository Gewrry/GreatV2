<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['faas_properties', 'faas_lands', 'tax_declarations', 'rpt_billings', 'rpt_payments'];

foreach ($tables as $table) {
    echo "--- $table ---\n";
    $columns = DB::select("SHOW COLUMNS FROM $table");
    foreach ($columns as $col) {
        if ($col->Null == 'NO' && $col->Default === null && $col->Extra != 'auto_increment') {
            echo "REQUIRED NO DEFAULT: {$col->Field} | {$col->Type}\n";
        }
    }
}
