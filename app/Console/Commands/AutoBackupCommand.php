<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AutoBackupCommand extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Realiza respaldo automático de la base de datos';

    public function handle()
    {
        try {
            // Verificar si el respaldo automático está habilitado
            $config = DB::table('configuraciones')->first();
            
            if (!$config || !$config->backup_automatico) {
                $this->info('Respaldo automático deshabilitado');
                return;
            }
            
            $this->info('Iniciando respaldo automático...');
            
            // Crear respaldo
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $filePath = $backupPath . '/' . $filename;
            
            // Generar el respaldo
            $tables = DB::select('SHOW TABLES');
            $databaseName = env('DB_DATABASE');
            $tableKey = "Tables_in_{$databaseName}";
            
            $sql = "-- Respaldo Automático - " . date('Y-m-d H:i:s') . "\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Estructura
                $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{"Create Table"} . ";\n\n";
                
                // Datos
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "INSERT INTO `{$tableName}` VALUES ";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $escapedValues = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $value) . "'";
                        }, $rowArray);
                        $values[] = "(" . implode(',', $escapedValues) . ")";
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Guardar archivo
            file_put_contents($filePath, $sql);
            
            // Limpiar respaldos antiguos (mantener solo últimos 20)
            $this->limpiarBackupsAntiguos();
            
            $this->info('Respaldo automático completado: ' . $filename);
            Log::info('Respaldo automático creado: ' . $filename);
            
        } catch (\Exception $e) {
            Log::error('Error en respaldo automático: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }
    }
    
    private function limpiarBackupsAntiguos()
    {
        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) return;
        
        $files = glob($backupPath . '/backup_*.sql');
        if (count($files) > 20) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $filesToDelete = array_slice($files, 0, count($files) - 20);
            foreach ($filesToDelete as $file) {
                unlink($file);
                Log::info('Backup antiguo eliminado: ' . basename($file));
            }
        }
    }
}