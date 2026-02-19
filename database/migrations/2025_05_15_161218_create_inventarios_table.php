<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id('id_inventario');
            $table->unsignedBigInteger('id_producto');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'devolucion']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->string('proveedor', 100)->nullable();
            $table->string('numero_factura', 50)->nullable();
            $table->unsignedBigInteger('id_venta')->nullable(); // Si es una salida por venta
            $table->date('fecha_movimiento');
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
            
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('cascade');
            $table->index(['id_producto', 'tipo_movimiento', 'fecha_movimiento']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventarios');
    }
};