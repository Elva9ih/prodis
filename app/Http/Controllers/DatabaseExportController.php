<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DatabaseExportController extends Controller
{
    private $driver;
    private $database;
    
    public function __construct()
    {
        $connection = Config::get('database.default');
        $this->driver = Config::get("database.connections.{$connection}.driver");
        $this->database = Config::get("database.connections.{$connection}.database");
    }
    
    public function export()
    {
        $filename = basename($this->database, '.sqlite') . '_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = storage_path('app/backups/' . $filename);
        
        // Create backups directory if it doesn't exist
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        $tables = $this->getTables();
        
        // Open file for writing
        $file = fopen($filepath, 'w');
        
        // Write header
        fwrite($file, "-- Database Export\n");
        fwrite($file, "-- Database: {$this->database}\n");
        fwrite($file, "-- Driver: {$this->driver}\n");
        fwrite($file, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
        
        if ($this->driver === 'mysql' || $this->driver === 'mariadb') {
            fwrite($file, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($file, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($file, "SET time_zone = \"+00:00\";\n\n");
        }
        
        foreach ($tables as $table) {
            fwrite($file, $this->getTableStructure($table));
            $this->writeTableData($file, $table);
        }
        
        if ($this->driver === 'mysql' || $this->driver === 'mariadb') {
            fwrite($file, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        }
        
        fclose($file);
        
        return response()->download($filepath)->deleteFileAfterSend(true);
    }
    
    private function getTables()
    {
        switch ($this->driver) {
            case 'mysql':
            case 'mariadb':
                $tables = DB::select('SHOW TABLES');
                $key = "Tables_in_{$this->database}";
                return array_map(function($table) use ($key) {
                    return $table->$key;
                }, $tables);
                
            case 'sqlite':
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                return array_map(function($table) {
                    return $table->name;
                }, $tables);
                
            case 'pgsql':
                $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                return array_map(function($table) {
                    return $table->tablename;
                }, $tables);
                
            case 'sqlsrv':
                $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
                return array_map(function($table) {
                    return $table->TABLE_NAME;
                }, $tables);
                
            default:
                return [];
        }
    }
    
    private function getTableStructure($table)
    {
        $sql = "-- Table structure for `{$table}`\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        switch ($this->driver) {
            case 'mysql':
            case 'mariadb':
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                break;
                
            case 'sqlite':
                $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table]);
                if (!empty($createTable)) {
                    $sql .= $createTable[0]->sql . ";\n\n";
                }
                break;
                
            case 'pgsql':
                // Get column definitions
                $columns = DB::select("
                    SELECT column_name, data_type, character_maximum_length, is_nullable, column_default
                    FROM information_schema.columns 
                    WHERE table_name = ? 
                    ORDER BY ordinal_position
                ", [$table]);
                
                $sql .= "CREATE TABLE \"{$table}\" (\n";
                $columnDefs = [];
                foreach ($columns as $column) {
                    $def = "  \"{$column->column_name}\" {$column->data_type}";
                    if ($column->character_maximum_length) {
                        $def .= "({$column->character_maximum_length})";
                    }
                    if ($column->is_nullable === 'NO') {
                        $def .= " NOT NULL";
                    }
                    if ($column->column_default) {
                        $def .= " DEFAULT {$column->column_default}";
                    }
                    $columnDefs[] = $def;
                }
                $sql .= implode(",\n", $columnDefs);
                $sql .= "\n);\n\n";
                break;
                
            case 'sqlsrv':
                // Get column definitions for SQL Server
                $columns = DB::select("
                    SELECT 
                        COLUMN_NAME, 
                        DATA_TYPE, 
                        CHARACTER_MAXIMUM_LENGTH, 
                        IS_NULLABLE, 
                        COLUMN_DEFAULT
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_NAME = ? 
                    ORDER BY ORDINAL_POSITION
                ", [$table]);
                
                $sql .= "CREATE TABLE [{$table}] (\n";
                $columnDefs = [];
                foreach ($columns as $column) {
                    $def = "  [{$column->COLUMN_NAME}] {$column->DATA_TYPE}";
                    if ($column->CHARACTER_MAXIMUM_LENGTH) {
                        $def .= "({$column->CHARACTER_MAXIMUM_LENGTH})";
                    }
                    if ($column->IS_NULLABLE === 'NO') {
                        $def .= " NOT NULL";
                    }
                    if ($column->COLUMN_DEFAULT) {
                        $def .= " DEFAULT {$column->COLUMN_DEFAULT}";
                    }
                    $columnDefs[] = $def;
                }
                $sql .= implode(",\n", $columnDefs);
                $sql .= "\n);\n\n";
                break;
        }
        
        return $sql;
    }
    
    private function writeTableData($file, $table)
    {
        $count = DB::table($table)->count();
        
        if ($count === 0) {
            fwrite($file, "-- No data for table `{$table}`\n\n");
            return;
        }
        
        fwrite($file, "-- Data for table `{$table}`\n");
        
        // Process in chunks to avoid memory issues
        DB::table($table)->orderBy(DB::raw('1'))->chunk(1000, function($rows) use ($file, $table) {
            foreach ($rows as $row) {
                $row = (array) $row;
                $columns = array_keys($row);
                $values = array_values($row);
                
                // Escape values based on driver
                $values = array_map(function($value) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    if (is_bool($value)) {
                        return $value ? '1' : '0';
                    }
                    if (is_numeric($value)) {
                        return $value;
                    }
                    // Escape single quotes and backslashes
                    $value = str_replace(['\\', "'"], ['\\\\', "''"], $value);
                    return "'" . $value . "'";
                }, $values);
                
                $columnList = $this->formatColumnList($columns);
                $sql = "INSERT INTO {$this->formatTableName($table)} ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
                fwrite($file, $sql);
            }
        });
        
        fwrite($file, "\n");
    }
    
    private function formatTableName($table)
    {
        switch ($this->driver) {
            case 'mysql':
            case 'mariadb':
                return "`{$table}`";
            case 'pgsql':
                return "\"{$table}\"";
            case 'sqlsrv':
                return "[{$table}]";
            default:
                return $table;
        }
    }
    
    private function formatColumnList($columns)
    {
        switch ($this->driver) {
            case 'mysql':
            case 'mariadb':
                return '`' . implode('`, `', $columns) . '`';
            case 'pgsql':
                return '"' . implode('", "', $columns) . '"';
            case 'sqlsrv':
                return '[' . implode('], [', $columns) . ']';
            default:
                return implode(', ', $columns);
        }
    }
}