<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackupColumnsToConfiguracionesTable extends Migration
{
    public function up()
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            // Agregar columnas para respaldo automático
            if (!Schema::hasColumn('configuraciones', 'backup_automatico')) {
                $table->boolean('backup_automatico')->default(0)->after('dias_vencimiento');
            }
            
            if (!Schema::hasColumn('configuraciones', 'hora_backup')) {
                $table->string('hora_backup', 10)->default('00:00')->after('backup_automatico');
            }
            
            if (!Schema::hasColumn('configuraciones', 'periodo_backup')) {
                $table->string('periodo_backup', 20)->default('diario')->after('hora_backup');
            }
            
            if (!Schema::hasColumn('configuraciones', 'ultimo_backup')) {
                $table->timestamp('ultimo_backup')->nullable()->after('periodo_backup');
            }
        });
    }

    public function down()
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropColumn([
                'backup_automatico',
                'hora_backup', 
                'periodo_backup',
                'ultimo_backup'
            ]);
        });
    }
}