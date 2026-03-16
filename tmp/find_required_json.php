<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$results = [];
foreach (['faas_lands', 'tax_declarations', 'rpt_billings'] as $table) {
    $cols = DB::select("DESCRIBE $table");
    foreach ($cols as $c) {
        if ($c->Null === 'NO' && $c->Default === null && $c->Extra !== 'auto_increment') {
            $results[$table][] = $c->Field;
        }
    }
}
echo json_encode($results, JSON_PRETTY_PRINT);
