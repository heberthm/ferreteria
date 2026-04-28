<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Respaldo automático - La hora se configura desde la base de datos
        $schedule->call(function () {
            $config = DB::table('configuraciones')->first();
            
            if ($config && $config->backup_automatico) {
                $hora = $config->hora_backup ?? '00:00';
                $horaActual = date('H:i');
                
                // Ejecutar solo en la hora configurada
                if ($horaActual === $hora) {
                    \Artisan::call('backup:auto');
                }
            }
        })->everyMinute();
        
        // Alternativa: programar directamente a una hora específica
        // $schedule->command('backup:auto')->dailyAt('02:00');
    }
}