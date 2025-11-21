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
            $table->string('userId')->required();
            $table->string('codigo')->unique();;
            $table->string('nombre');
            $table->string('descripcion')->nullable(); 
            $table->string('marca')->nullable(); 
            $table->string('categoria')->nullable(); 
            $table->integer('cantidad');
            $table->integer('precio_compra');
            $table->integer('precio_venta');
            $table->integer('stock');
            $table->integer('stock_minimo')->default(5);
            $table->string('unidad_medida');
            $table->string('ubicacion')->nullable();
            $table->string('proveedor')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('id_categoria')->constrained('clientes');
            $table->foreignId('id_proveedor')->constrained('proveedores');
             $table->boolean('frecuente')->default(false);
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
