<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$data = [];
foreach (['faas_lands', 'tax_declarations', 'rpt_billings'] as $table) {
    $data[$table] = DB::select("DESCRIBE $table");
}
echo json_encode($data, JSON_PRETTY_PRINT);
