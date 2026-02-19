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
        Schema::create('ventas', function (Blueprint $table) {
            $table->bigIncrements('id_venta'); // Clave primaria autoincremental
            
            // Primero definimos las columnas
            $table->unsignedBigInteger('id_cliente')->nullable(); // Columna para la FK
            $table->unsignedBigInteger('userId'); // Columna para la FK de usuario
            
            // Luego el resto de las columnas
            $table->dateTime('fecha_venta');
            $table->string('numero_factura')->unique();
            $table->string('nombre_usuario')->nullable();
            $table->json('productos')->nullable();
            $table->string('subtotal', 10);
            $table->string('iva', 8);
            $table->string('total', 10);
            $table->string('descuento', 10)->default(0);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado', 'completada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->string('metodo_pago');
            $table->string('tipo_comprobante');
            $table->string('vendedor')->default('Sistema');
            $table->string('referencia_pago')->nullable();
            $table->string('efectivo_recibido', 10)->nullable();
            $table->string('cambio', 10)->nullable();
            $table->timestamps();
            
            // Luego definimos las foreign keys
            $table->foreign('id_cliente')
                  ->references('id')
                  ->on('clientes')
                  ->onDelete('set null'); // O usar 'cascade' si prefieres
            
            $table->foreign('userId')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Primero eliminar las foreign keys
            $table->dropForeign(['id_cliente']);
            $table->dropForeign(['userId']);
        });
        
        Schema::dropIfExists('ventas');
    }
};