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
        Schema::create('gastos', function (Blueprint $table) {
            $table->bigIncrements('id_gasto');
            $table->string('userId')->required();
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->text('descripcion')->nullable();
             $table->string('comprobante')->nullable();
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'cheque', 'otro'])->default('efectivo');
            $table->enum('tipo', ['diario', 'mensual'])->default('diario');
            $table->enum('estado', ['pendiente', 'pagado'])->default('pagado');
            $table->foreignId('id_categoria_gasto')->constrained('categorias_gastos');
            $table->foreignId('user_id')->nullable()->constrained('users');
         
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
        Schema::dropIfExists('gastos');
    }
};
