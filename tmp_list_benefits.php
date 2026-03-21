<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\BplsBenefit;

$benefits = BplsBenefit::all();
foreach ($benefits as $b) {
    echo $b->field_key . ": " . $b->discount_percent . "%\n";
}
