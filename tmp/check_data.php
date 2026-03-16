<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RPT\RptaActualUse;
use App\Models\Barangay;

echo "Actual Uses:\n";
foreach (RptaActualUse::all() as $u) {
    echo "ID: {$u->id}, Name: {$u->name}\n";
}

echo "\nBarangays:\n";
foreach (Barangay::all() as $b) {
    echo "ID: {$b->id}, Name: {$b->name}\n";
}
