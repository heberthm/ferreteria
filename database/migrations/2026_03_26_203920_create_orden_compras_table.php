<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabla principal ─────────────────────────────────────────
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id('id_orden');
            $table->string('numero_orden', 30)->unique();          // OC-20240101-00001
            $table->date('fecha_orden');
            $table->date('fecha_entrega_esperada')->nullable();
            $table->date('fecha_entrega_real')->nullable();

            // Proveedor — puede ser un registro o datos libres
            $table->unsignedBigInteger('id_proveedor')->nullable();
            $table->string('proveedor_nombre', 150)->nullable();   // fallback texto libre
            $table->string('proveedor_nit', 30)->nullable();
            $table->string('proveedor_telefono', 30)->nullable();
            $table->string('proveedor_email', 100)->nullable();
            $table->string('proveedor_direccion', 255)->nullable();

            // Condiciones
            $table->enum('metodo_pago', [
                'efectivo', 'transferencia', 'cheque', 'credito_30', 'credito_60', 'credito_90'
            ])->nullable();
            $table->integer('dias_credito')->default(0);

            // Totales (calculados al guardar)
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('descuento_total', 14, 2)->default(0);
            $table->decimal('impuesto_porcentaje', 5, 2)->default(0);   // 0, 5, 16, 19 …
            $table->decimal('impuesto_valor', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            // Estado del ciclo de vida
            $table->enum('estado', [
                'borrador', 'enviada', 'confirmada', 'recibida_parcial',
                'recibida', 'cancelada'
            ])->default('borrador');

            $table->text('observaciones')->nullable();
            $table->text('terminos_condiciones')->nullable();

            // Auditoría
            $table->unsignedBigInteger('userId');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('userId')->references('id')->on('users');
        });

        // ── Detalle (productos) ──────────────────────────────────────
        Schema::create('ordenes_compra_detalle', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_orden');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->string('codigo_producto', 50)->nullable();
            $table->string('nombre_producto', 200);
            $table->string('unidad_medida', 30)->nullable();
            $table->decimal('cantidad', 12, 2);
            $table->decimal('cantidad_recibida', 12, 2)->default(0);
            $table->decimal('precio_unitario', 14, 2);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('total_linea', 14, 2);

            $table->foreign('id_orden')->references('id_orden')->on('ordenes_compra')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra_detalle');
        Schema::dropIfExists('ordenes_compra');
    }
};