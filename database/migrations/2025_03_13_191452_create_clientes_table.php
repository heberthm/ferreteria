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
                $table->string('cedula',18)->required();       
                $table->string('nombre');                   
                $table->string('email')->nullable();
                $table->string('telefono')->nullable();
                $table->text('direccion')->nullable();
                 $table->timestamps();
                $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};    
