<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function checkRequired($table) {
    echo "REQUIRED FIELDS FOR $table:\n";
    $columns = DB::select("SHOW COLUMNS FROM $table");
    foreach ($columns as $col) {
        if ($col->Null == 'NO' && $col->Default === null && $col->Extra != 'auto_increment') {
            echo "{$col->Field} | {$col->Type}\n";
        }
    }
    echo "\n";
}

checkRequired('rpt_billings');
checkRequired('rpt_payments');
checkRequired('faas_lands');
