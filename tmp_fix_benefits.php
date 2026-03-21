<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BplsBenefit;

$count = BplsBenefit::where(function($q) {
    $q->whereNull('apply_to')->orWhere('apply_to', 'permit_only');
})->update(['apply_to' => 'total']);

echo "Updated {$count} benefits to 'total' base.\n";
