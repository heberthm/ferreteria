<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_caja', function (Blueprint $table) {
            $table->bigIncrements('id_movimiento_caja');
            $table->unsignedBigInteger('id_caja');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->decimal('monto', 10, 2);
            $table->string('concepto', 191);
            $table->text('descripcion')->nullable();
            $table->string('comprobante', 191)->nullable();
            $table->unsignedBigInteger('userId');
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_caja')->references('id_caja')->on('caja_menor')->onDelete('cascade');
            $table->foreign('userId')->references('userId')->on('users')->onDelete('cascade');

            // Índices
            $table->index('id_caja');
            $table->index('userId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimiento_caja');
    }
}