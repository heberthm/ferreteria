<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            
            // Relación con venta
            $table->unsignedBigInteger('id_venta');
                      
            // Información del pago
            $table->string('metodo_pago', 50);
            $table->string('monto', 15);
            $table->string('referencia', 100)->nullable();
            $table->string('estado', 20)->default('pendiente');
            
            // Información adicional según método de pago
            $table->string('banco', 100)->nullable();
            $table->string('numero_cuenta', 50)->nullable();
            $table->string('numero_cheque', 50)->nullable();
            $table->string('numero_tarjeta', 20)->nullable();
            $table->string('titular_tarjeta', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();

            $table->foreign('id_venta')->references('id_venta')->on('ventas')->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
            
            // Índices
            $table->index('id_venta');
            $table->index('metodo_pago');
            $table->index('estado');
            $table->index(['id_venta', 'metodo_pago']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}