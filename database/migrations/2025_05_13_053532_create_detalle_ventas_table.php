<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleVentasTable extends Migration
{
    public function up()
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id('id_detalle_venta');
            
            // Relación con venta
            $table->unsignedBigInteger('id_venta');
            $table->foreign('id_venta')
                  ->references('id_venta')
                  ->on('ventas')
                  ->onDelete('cascade');
            
            // Relación con producto
            $table->unsignedBigInteger('id_producto');
            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('productos')
                  ->onDelete('restrict');
            
            // Detalles
            $table->integer('cantidad');
            $table->string('precio_unitario', 15);
            $table->string('subtotal', 15);
            
            $table->timestamps();
            
            // Índices
            $table->index('id_venta');
            $table->index('id_producto');
            $table->index(['id_venta', 'id_producto']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalle_ventas');
    }
}