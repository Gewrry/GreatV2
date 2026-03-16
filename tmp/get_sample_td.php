<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$td = App\Models\RPT\TaxDeclaration::first();
if ($td) {
    echo json_encode($td->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "No TD found";
}
