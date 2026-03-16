<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fks = DB::select("SELECT 
    f.name AS foreign_key_name,
    OBJECT_NAME(f.parent_object_id) AS table_name,
    COL_NAME(fc.parent_object_id, fc.parent_column_id) AS column_name,
    OBJECT_NAME (f.referenced_object_id) AS referenced_table_name,
    COL_NAME(fc.referenced_object_id, fc.referenced_column_id) AS referenced_column_name
FROM sys.foreign_keys AS f
INNER JOIN sys.foreign_key_columns AS fc 
   ON f.object_id = fc.constraint_object_id
WHERE OBJECT_NAME(f.parent_object_id) = 'tax_declarations'");

foreach ($fks as $fk) {
    echo "FK: {$fk->foreign_key_name} | Col: {$fk->column_name} -> {$fk->referenced_table_name}({$fk->referenced_column_name})\n";
}
