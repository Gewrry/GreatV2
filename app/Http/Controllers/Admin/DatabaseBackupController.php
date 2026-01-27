<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    public function backup()
    {
        $dbName = config('database.connections.mysql.database');
        $fileName = 'database-backup-' . date('Y-m-d_H-i-s') . '.sql';

        return new StreamedResponse(function () use ($dbName) {
            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$dbName}";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // DROP TABLE + CREATE TABLE
                $create = DB::selectOne("SHOW CREATE TABLE `$tableName`");
                echo "\n\n-- Table structure for table `$tableName` \n\n";
                echo "DROP TABLE IF EXISTS `$tableName`;\n";
                echo $create->{'Create Table'} . ";\n\n";

                // Table data
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $columns = array_map(fn($col) => "`$col`", array_keys((array) $row));
                    $values = array_map(fn($val) => isset($val) ? "'" . str_replace("'", "''", $val) . "'" : 'NULL', (array) $row);

                    echo "INSERT INTO `$tableName` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
                }
            }
        }, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }
}
