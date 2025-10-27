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

        
        Schema::create('caja_menor', function (Blueprint $table) {
            $table->bigIncrements('id_caja');
            $table->decimal('monto_inicial', 10, 2)->default(0);
            $table->decimal('monto_actual', 10, 2)->default(0);
            $table->enum('estado', ['abierta', 'cerrada'])->default('cerrada');
            $table->dateTime('fecha_apertura')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->text('observaciones_apertura')->nullable();
            $table->text('observaciones_cierre')->nullable();
            $table->foreignId('user_id_apertura')->nullable()->constrained('users');
            $table->foreignId('user_id_cierre')->nullable()->constrained('users');
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
