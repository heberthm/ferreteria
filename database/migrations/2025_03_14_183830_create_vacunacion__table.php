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
        Schema::create('vacunacion_', function (Blueprint $table) {
            $table->bigIncrements('id_vacunacion');
            $table->foreignId('id_mascota')->constrained()->onDelete('cascade');
            $table->foreignId('id_veterinario')->constrained();
            $table->string('vacuna');
            $table->date('fecha_aplicacion');
            $table->date('fecha_proxima')->nullable();
            $table->string('lote')->nullable();
            $table->text('notas')->nullable();
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
        Schema::dropIfExists('vacunacion_');
    }
};
