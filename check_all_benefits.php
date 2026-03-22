<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BplsBenefit;

echo "All Benefits:\n";
foreach (BplsBenefit::all() as $b) {
    echo "  - " . $b->name . " (" . $b->discount_percent . "%)\n";
}
