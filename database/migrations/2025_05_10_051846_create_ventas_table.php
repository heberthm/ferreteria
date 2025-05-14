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
            $table->id(); // Clave primaria autoincremental
            $table->foreignId('id_cliente')->nullable()->constrained('clientes'); // Clave foránea a la tabla clientes (opcional)
            $table->dateTime('fecha_venta');
            $table->decimal('subtotal', 10, 2)->default(0.00); // Subtotal antes de impuestos y descuentos
            $table->decimal('descuento', 8, 2)->default(0.00);
            $table->decimal('iva', 8)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            $table->text('notas')->nullable();
          

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
        Schema::dropIfExists('ventas');
    }
};
