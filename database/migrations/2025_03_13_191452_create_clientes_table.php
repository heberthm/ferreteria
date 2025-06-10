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
        Schema::create('clientes', function (Blueprint $table) {
                $table->bigIncrements('id_cliente');
                $table->string('userId')->required();
                $table->string('cedula',18)->unique()->required();                
                $table->date('fecha_nacimiento',18)->required();
                $table->string('edad',14)->required();
                $table->string('nombre',60)->required();
                $table->string('direccion',150)->nullable();
                $table->string('celular',30)->nullable();
                $table->string('email',60)->nullable();
                $table->boolean('estado')->default(0);
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};    
