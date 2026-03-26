<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_devolucion_detalles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevolucionesTable  extends Migration
{
    public function up()
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_devolucion');
            $table->unsignedBigInteger('id_producto');
            $table->string('nombre_producto', 255);
            $table->string('codigo_producto', 100)->nullable();
            $table->integer('cantidad_devuelta');
            $table->integer('cantidad_original')->nullable();
            $table->decimal('precio_unitario', 15, 2);
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('iva', 5, 2)->default(19);
            $table->decimal('total', 15, 2);
            $table->enum('condicion_producto', [
                'nuevo_sin_uso',
                'abierto_sin_uso',
                'usado_buen_estado',
                'danado',
                'incompleto'
            ])->default('nuevo_sin_uso');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_devolucion')->references('id_devolucion')->on('devoluciones')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devolucion_detalles');
    }
}