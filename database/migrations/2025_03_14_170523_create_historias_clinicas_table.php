<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->bigIncrements('id_historia_clinica');
            $table->string('user_id')->required();
            $table->integer('id_cliente')->unsigned();  
            $table->string('nombre',50);
            $table->string('edad',10);
            $table->string('profesional',50);
            $table->string('estatura',3);
            $table->string('peso_inicial',3)->nullable();
            $table->string('abd_inicial',3)->nullable();
            $table->string('grasa_inicial',3)->nullable();
            $table->string('agua_inicial',3)->nullable();
            $table->string('imc',3)->nullable();
            $table->string('grasa_viseral',3)->nullable();
            $table->string('edad_metabolica',3)->nullable();
            $table->string('terapias',60)->nullable();
            $table->string('paquete_desintoxicacion',100)->nullable();
            $table->string('terapias_adicionales',60)->nullable();
            $table->string('tipo_lavado',60)->nullable();
            $table->string('num_lavado',3)->nullable();
            $table->string('dias_lavados',3)->nullable();
            $table->string('observaciones',400)->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
