<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select('DESCRIBE faas_properties');
$req = [];
foreach ($cols as $c) {
    if ($c->Null === 'NO' && $c->Default === null && $c->Extra !== 'auto_increment') {
        $req[] = $c->Field;
    }
}
echo implode(',', $req) . "\n";
