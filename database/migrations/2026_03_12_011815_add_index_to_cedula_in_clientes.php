<?php

public function up()
{
    Schema::table('clientes', function (Blueprint $table) {
        $table->index('cedula'); // Agregar índice para búsquedas más rápidas
    });
}

public function down()
{
    Schema::table('clientes', function (Blueprint $table) {
        $table->dropIndex(['cedula']);
    });
}


