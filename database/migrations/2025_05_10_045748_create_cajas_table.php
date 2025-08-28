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

        
        Schema::create('cajas', function (Blueprint $table) {
            $table->bigIncrements('id_caja');
              $table->foreignId('userId')->constrained(); // Usuario responsable
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->decimal('saldo_inicial', 12, 2); // Saldo inicial
            $table->decimal('saldo_final', 12, 2)->nullable(); // Saldo final (se calcula al cerrar)
            $table->enum('estado', ['abierta', 'cerrada', 'en_revision'])->default('cerrada');         
            $table->text('observaciones')->nullable(); // Observaciones  


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
        Schema::dropIfExists('cajas');
    }
};
