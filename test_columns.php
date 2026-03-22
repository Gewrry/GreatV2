<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo implode(',', \DB::getSchemaBuilder()->getColumnListing('bpls_business_entries'));
