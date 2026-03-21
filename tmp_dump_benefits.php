<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BplsBenefit;

$benefits = BplsBenefit::all();
echo "ID | NAME | LABEL | RATE | APPLY_TO\n";
foreach ($benefits as $b) {
    echo "{$b->id} | {$b->name} | {$b->label} | {$b->discount_percent} | " . ($b->apply_to ?: 'NULL') . "\n";
}
