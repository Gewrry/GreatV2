<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

file_put_contents('debug_columns_businesses.log', implode("\n", \DB::getSchemaBuilder()->getColumnListing('bpls_businesses')));
