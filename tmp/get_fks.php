<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$fks = DB::select("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'faas_lands' AND REFERENCED_TABLE_NAME IS NOT NULL");
foreach ($fks as $fk) {
    echo "Column: $fk->COLUMN_NAME -> $fk->REFERENCED_TABLE_NAME($fk->REFERENCED_COLUMN_NAME)\n";
}
