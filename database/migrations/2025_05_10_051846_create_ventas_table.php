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
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('descuento', 8, 2)->default(0.00);
            $table->decimal('iva', 8, 2)->default(0.00); // Corregí: agregué 2 decimales
            $table->decimal('total', 10, 2)->default(0.00);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado', 'completada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->string('metodo_pago');
            $table->string('tipo_comprobante');
            $table->string('vendedor')->default('Sistema');
            $table->string('referencia_pago')->nullable();
            $table->decimal('efectivo_recibido', 10, 2)->default(0.00)->nullable();
            $table->decimal('cambio', 10, 2)->default(0.00)->nullable();
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