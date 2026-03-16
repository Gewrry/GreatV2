<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RPT\RptaActualUse;
use App\Models\RPT\RptaRevisionYear;

echo "ACTUAL USES:\n";
foreach (RptaActualUse::all() as $u) {
    echo "ID: $u->id - $u->name\n";
}

echo "\nREVISION YEARS:\n";
foreach (RptaRevisionYear::all() as $r) {
    echo "ID: $r->id - $r->year\n";
}
