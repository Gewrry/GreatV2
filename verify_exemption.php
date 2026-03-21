<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$results = [
    'rpt_property_registrations' => Schema::hasColumn('rpt_property_registrations', 'exemption_basis'),
    'faas_properties' => Schema::hasColumn('faas_properties', 'exemption_basis'),
];

print_r($results);
