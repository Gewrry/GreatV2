<?php
$dir = __DIR__ . '/database/migrations';
$files = scandir($dir);
$pattern = 'faas_gen_rev_geometries';

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $content = file_get_contents($dir . '/' . $file);
    if (strpos($content, $pattern) !== false) {
        echo "File: $file\n";
        if (strpos($content, "Schema::create") !== false) {
            echo "  - Contains Schema::create\n";
        }
        if (strpos($content, "Schema::table") !== false) {
            echo "  - Contains Schema::table\n";
        }
    }
}
