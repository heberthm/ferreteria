<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla principal
        Schema::create('remisiones', function (Blueprint $table) {
            $table->id('id_remision');
            $table->string('numero_remision', 50)->unique();

            // Cliente registrado o general
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->string('cliente_nombre', 255)->nullable();
            $table->string('cliente_cedula', 50)->nullable();
            $table->string('cliente_telefono', 50)->nullable();
            $table->string('cliente_email', 255)->nullable();

            // Vendedor/creador
            $table->unsignedBigInteger('id_vendedor')->nullable();

            // Transporte
            $table->string('conductor', 255)->nullable();
            $table->string('vehiculo_placa', 20)->nullable();
            $table->string('direccion_entrega', 500)->nullable();

            // Fechas
            $table->date('fecha_remision')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_entrega_real')->nullable();

            // Estado
            $table->enum('estado', ['pendiente', 'en_transito', 'entregada', 'anulada', 'parcial'])
                  ->default('pendiente');

            // Totales
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('descuento')->default(0);
            $table->bigInteger('total')->default(0);

            // Extras
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->nullOnDelete();
            $table->foreign('id_vendedor')->references('id')->on('users')->nullOnDelete();
        });

        // Tabla de detalles
        Schema::create('remision_detalles', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_remision');
            $table->unsignedBigInteger('id_producto');
            $table->string('codigo_producto', 100)->nullable();
            $table->string('nombre_producto', 255);
            $table->string('unidad_medida', 50)->nullable();
            $table->integer('cantidad')->default(1);
            $table->bigInteger('precio_unitario')->default(0);
            $table->bigInteger('descuento')->default(0);
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('total')->default(0);
            $table->timestamps();

            $table->foreign('id_remision')->references('id_remision')->on('remisiones')->cascadeOnDelete();
            $table->foreign('id_producto')->references('id_producto')->on('productos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remision_detalles');
        Schema::dropIfExists('remisiones');
    }
};