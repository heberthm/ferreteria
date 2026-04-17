<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreContactoToOrdenesCompraTable extends Migration
{
    public function up()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->string('nombre_contacto')->nullable()->after('proveedor_nombre');
        });
    }

    public function down()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->dropColumn('nombre_contacto');
        });
    }
}