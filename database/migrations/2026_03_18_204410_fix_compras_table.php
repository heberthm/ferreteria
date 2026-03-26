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
    Schema::table('compras', function (Blueprint $table) {
        // Agregar columnas faltantes
        $table->unsignedBigInteger('id_proveedor')->nullable()->after('id_producto');
        $table->string('estado', 20)->default('completada')->after('notas');
        $table->unsignedBigInteger('user_id')->nullable()->after('usuario_registro');

        $table->foreign('id_proveedor')
              ->references('id_proveedor')
              ->on('proveedores')
              ->onDelete('set null');
    });
}
};
