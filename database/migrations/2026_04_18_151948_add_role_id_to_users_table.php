<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregar columna role_id después de la columna email (opcional)
            $table->foreignId('role_id')->nullable()->after('email')->constrained('roles')->onDelete('set null');
            
            // Alternativa: Si la tabla roles aún no existe, usar esta línea en lugar de la anterior:
            // $table->unsignedBigInteger('role_id')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['role_id']);
            // Luego eliminar la columna
            $table->dropColumn('role_id');
        });
    }
};