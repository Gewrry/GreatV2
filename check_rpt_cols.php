<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$table = 'rpt_property_registrations';
echo "Columns for table: $table\n";
if (Schema::hasTable($table)) {
    $columns = Schema::getColumnListing($table);
    echo implode(", ", $columns) . "\n";
} else {
    echo "Table $table does not exist.\n";
}
