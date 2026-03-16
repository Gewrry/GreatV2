<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "REQUIRED FIELDS FOR tax_declarations:\n";
$columns = DB::select("SHOW COLUMNS FROM tax_declarations");
foreach ($columns as $col) {
    if ($col->Null == 'NO' && $col->Default === null && $col->Extra != 'auto_increment') {
        echo "{$col->Field} | {$col->Type}\n";
    }
}
