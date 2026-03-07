<?php
// database/migrations/2024_01_01_000002_create_cotizacion_detalles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cotizacion_detalles', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_cotizacion');
            $table->unsignedBigInteger('id_producto');
            $table->string('codigo_producto');
            $table->string('nombre_producto');
            $table->string('unidad_medida')->nullable();
            $table->decimal('cantidad', 10);
            $table->decimal('precio_unitario', 10);
            $table->decimal('descuento', 10)->default(0);
            $table->decimal('subtotal', 10);
            $table->decimal('total', 10);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('cotizaciones')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cotizacion_detalles');
    }
};