<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devoluciones_detalle', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_devolucion');
            $table->unsignedBigInteger('id_producto');
            $table->string('nombre_producto', 200)->nullable();
            $table->string('codigo_producto', 50)->nullable();
            $table->integer('cantidad_devuelta');
            $table->integer('cantidad_original')->nullable();
            $table->decimal('precio_unitario', 15, 2)->default(0);
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva', 5, 2)->default(19);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('condicion_producto', [
                'nuevo_sin_uso',
                'abierto_sin_uso',
                'usado_buen_estado',
                'danado',
                'incompleto'
            ])->default('nuevo_sin_uso');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_devolucion')
                  ->references('id_devolucion')->on('devoluciones')
                  ->cascadeOnDelete();
            $table->foreign('id_producto')
                  ->references('id_producto')->on('productos')
                  ->restrictOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devoluciones_detalle');
    }
};