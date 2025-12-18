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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->bigIncrements('id_detalle');
            
            // Asegúrate que el nombre de columna sea consistente
            $table->unsignedBigInteger('id_venta');
            $table->unsignedBigInteger('id_producto'); // Este nombre debe coincidir con la FK
            
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_venta')
                  ->references('id_venta') // Asegúrate que ventas tenga id_venta
                  ->on('ventas')
                  ->onDelete('cascade');
            
            $table->foreign('id_producto')
                  ->references('id_producto') // Esto debe coincidir con productos
                  ->on('productos')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropForeign(['producto_id']);
        });
        
        Schema::dropIfExists('detalle_ventas');
    }
};