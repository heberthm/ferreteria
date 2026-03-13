<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->integer('tipo_iva')->default(19)->after('descuento');
            $table->decimal('iva', 12, 2)->default(0)->after('tipo_iva');
        });
    }

    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn(['tipo_iva', 'iva']);
        });
    }
};