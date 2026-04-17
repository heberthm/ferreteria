<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_grupo_to_configuraciones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGrupoToConfiguracionesTable extends Migration
{
    public function up()
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->string('grupo')->default('general')->after('id_configuraciones');
            $table->index('grupo');
        });
    }

    public function down()
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropColumn('grupo');
        });
    }
}