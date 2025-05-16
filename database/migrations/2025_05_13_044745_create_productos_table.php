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
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id_producto');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->text('codigo');
            $table->integer('precio_compra', 10);
            $table->integer('precio_venta', 10);
            $table->integer('stock');
            $table->integer('stock_minimo')->default(5);
            $table->string('unidad_medida');
            $table->string('ubicacion')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('id_categoria')->constrained();
            $table->foreignId('id_proveedor')->constrained();
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
        Schema::dropIfExists('productos');
    }
};
