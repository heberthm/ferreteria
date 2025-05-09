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
        Schema::create('citas', function (Blueprint $table) {
          $table->increments('id_cita');
          $table->foreignId('id_mascota')->constrained()->onDelete('cascade');
          $table->foreignId('id_veterinario')->constrained();
          $table->string('userId'); 
          $table->string('title');
          $table->dateTime('start');
          $table->dateTime('end');
          $table->string('cliente',50);
          $table->string('telefono',25);
          $table->string('descripcion',250)->nullable();
          $table->string('medico',50);
          $table->string('color',20);
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
        Schema::dropIfExists('citas');
    }
};
