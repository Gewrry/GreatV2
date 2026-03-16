<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['tax_declarations', 'rpt_billings'] as $table) {
    echo "Table: $table\n";
    $cols = DB::select("DESCRIBE $table");
    foreach ($cols as $c) {
        if ($c->Null === 'NO' && $c->Default === null && $c->Extra !== 'auto_increment') {
            echo "$c->Field\n";
        }
    }
}
