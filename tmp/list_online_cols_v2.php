<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Illuminate\Support\Facades\Schema::getColumnListing('bpls_online_applications');
file_put_contents('tmp/cols.txt', implode("\n", $columns));
echo "Done.\n";
