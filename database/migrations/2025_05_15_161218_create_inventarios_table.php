<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id('id_inventario');
            $table->unsignedBigInteger('id_producto');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'devolucion']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('precio_venta', 10)->nullable();
            $table->decimal('costo_promedio', 10, 2)->nullable();
            $table->decimal('ultimo_costo', 10, 2)->nullable();
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->string('proveedor', 100)->nullable();
            $table->string('numero_factura', 50)->nullable();
            $table->unsignedBigInteger('id_venta')->nullable();
            $table->date('fecha_movimiento');
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('userId')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('cascade');
            $table->foreign('id_venta')->references('id_venta')->on('ventas')->onDelete('set null');
            $table->foreign('userId')->references('id')->on('users')->onDelete('set null');
            
            // Índices para mejorar el rendimiento
            $table->index(['id_producto', 'fecha_movimiento']);
            $table->index('tipo_movimiento');
            $table->index('fecha_movimiento');
            $table->index('numero_factura');
            $table->index('id_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};