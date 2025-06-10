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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->bigIncrements('id_inventario');
            $table->string('userId')->required();
            $table->foreignId('id_producto')->constrained('productos');
            $table->text('descripcion');
            $table->integer('cantidad');
            $table->integer('stock_minimo')->default(5);
            $table->integer('stock_maximo')->nullable();
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
        Schema::dropIfExists('inventarios');
    }
};
