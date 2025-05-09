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
        Schema::create('prescripciones_medicas', function (Blueprint $table) {
            $table->bigIncrements('id_prescripcion');
            $table->foreignId('id_hitoria_clinica')->constrained()->onDelete('cascade');
            $table->foreignId('id_medicamento')->constrained();
            $table->string('dosis');
            $table->string('frecuencia');
            $table->integer('duracion');
            $table->enum('unidad_duracion', ['Días', 'Semanas', 'Meses'])->default('Días');
            $table->text('instrucciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prescripciones_medicas');
    }
};
