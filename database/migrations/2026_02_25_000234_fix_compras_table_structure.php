<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            // Agregar solo las columnas necesarias
            if (!Schema::hasColumn('compras', 'precio_compra')) {
                $table->decimal('precio_compra', 10, 2)->nullable()->after('cantidad');
            }
            
            if (!Schema::hasColumn('compras', 'precio_total')) {
                $table->decimal('precio_total', 10, 2)->nullable()->after('precio_compra');
            }
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['precio_compra', 'precio_total']);
        });
    }
};