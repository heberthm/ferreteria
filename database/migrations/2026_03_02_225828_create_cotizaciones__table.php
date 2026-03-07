<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id('id_cotizacion');
            $table->string('numero_cotizacion')->unique();
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_cedula')->nullable();
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_email')->nullable();
            $table->unsignedBigInteger('id_vendedor');
            $table->datetime('fecha_cotizacion');
            $table->date('fecha_validez')->nullable();
            $table->decimal('subtotal', 10)->default(0);
            $table->decimal('impuesto', 10)->default(0);
            $table->decimal('descuento', 10)->default(0);
            $table->decimal('total', 10)->default(0);
            $table->text('observaciones')->nullable();
            $table->string('estado')->default('activa'); // activa, vencida, aceptada, rechazada
            $table->string('metodo_pago_sugerido')->nullable();
            $table->text('terminos_condiciones')->nullable();
            $table->timestamps();
            
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('set null');
            $table->foreign('id_vendedor')->references('userId')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cotizaciones');
    }
};