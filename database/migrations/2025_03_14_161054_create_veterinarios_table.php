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
        Schema::create('veterinarios', function (Blueprint $table) {
            $table->bigIncrements('id_veterinario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono', 15)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('num_licencia')->unique();
            $table->string('especialidad')->nullable();
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('veterinarios');
    }
};
