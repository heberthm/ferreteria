<?php
// database/migrations/xxxx_xx_xx_create_compras_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id('id_compra');
            $table->unsignedBigInteger('id_producto');
            $table->integer('cantidad');
            $table->string('precio_unitario');
            $table->string('precio_total');
            $table->string('proveedor', 190)->nullable();
            $table->string('numero_factura', 100)->nullable();
            $table->date('fecha_compra');
            $table->string('metodo_pago', 50)->nullable();
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('usuario_registro')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('productos')
                  ->onDelete('restrict');
                  
            $table->foreign('usuario_registro')
                  ->references('userId')
                  ->on('users')
                  ->onDelete('set null');
                  
            // Índices
            $table->index('fecha_compra');
            $table->index('proveedor');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('compras');
    }
};