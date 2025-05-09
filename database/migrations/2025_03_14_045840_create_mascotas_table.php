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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->bigIncrements('id_mascota');
            $table->string('id_cliente');
            $table->string('user_id');
            $table->string('mascota',20)->required();
            $table->date('fecha_nacimiento',12)->required();
            $table->string('anos',12)->required();
            $table->string('meses',5)->required();
            $table->string('especie',16)->required();
            $table->string('raza',26)->required();
            $table->string('sexo',12)->required();
            $table->string('pelaje',10)->required();
            $table->string('color',12)->required();
            $table->string('peso',10)->required();
            $table->string('alimento',30)->nullable();
            $table->string('vivienda_compartida',20)->nullable();
            $table->string('chip',16)->nullable();
            $table->string('ultimo_celo',12)->nullable();
            $table->string('pedigree',2)->nullable();
            $table->string('fallecido',2)->nullable();
            $table->string('donante',2)->nullable();
            $table->string('transfusiones',2)->nullable();
            $table->string('reproductor',2)->nullable();
            $table->string('esterilizado',2)->nullable();
            $table->string('factor_DEA',15)->nullable();
            $table->string('adopcion',2)->nullable();
            $table->string('agresividad',2)->nullable();
            $table->string('preexistencia',50)->nullable();
            $table->string('veterinario_remitente',60)->nullable();

            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mascotas');
    }
};
