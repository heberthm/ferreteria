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
        Schema::create('cajas', function (Blueprint $table) {
            $table->bigIncrements('id_caja');
            $table->string('userId')->required();
            $table->dateTime('fecha_caja')->unique(); // Fecha de la caja diaria
            $table->decimal('saldo_inicial', 12, 2); // Saldo inicial
            $table->decimal('saldo_final', 12, 2)->nullable(); // Saldo final (se calcula al cerrar)
            $table->decimal('total_ingresos', 12, 2)->default(0); // Total de ingresos
            $table->decimal('total_egresos', 12, 2)->default(0); // Total de egresos
            $table->enum('estado_caja', ['open', 'closed'])->default('open'); // Estado de la caja
            $table->foreignId('user_id')->constrained(); // Usuario responsable
            $table->text('observaciones')->nullable(); // Observaciones  


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
        Schema::dropIfExists('cajas');
    }
};
