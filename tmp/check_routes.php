<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "treasury.gis.batch-nod: " . route('treasury.gis.batch-nod') . "\n";
echo "rpt.gis.batch-nod: " . route('rpt.gis.batch-nod') . "\n";
