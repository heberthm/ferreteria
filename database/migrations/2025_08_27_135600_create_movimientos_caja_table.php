<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas');
            $table->enum('tipo', ['venta', 'ingreso', 'egreso']);
            $table->decimal('monto', 10, 2);
            $table->text('descripcion');
            $table->timestamp('fecha');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_caja');
    }
};