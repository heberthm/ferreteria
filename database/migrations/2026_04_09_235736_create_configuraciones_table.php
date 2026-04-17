<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_configuraciones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionesTable extends Migration
{
    public function up()
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id('id_configuracion');
            
            // General
            $table->string('nombre_sistema')->default('Sistema Ferretero');
            $table->string('zona_horaria')->default('America/Bogota');
            $table->string('formato_fecha')->default('d/m/Y');
            $table->string('moneda')->default('COP');
            $table->string('simbolo_moneda')->default('$');
            
            // Facturación
            $table->string('prefijo_factura')->default('FAC');
            $table->integer('consecutivo_inicial')->default(1);
            $table->integer('consecutivo_actual')->default(1);
            $table->integer('longitud_numero')->default(6);
            $table->string('formato_factura')->default('simple');
            $table->boolean('autogenerar')->default(true);
            $table->boolean('validar_duplicados')->default(true);
            $table->boolean('factura_electronica')->default(false);
            $table->string('tamaño_papel')->default('thermal');
            $table->integer('copias')->default(1);
            
            // Negocio
            $table->string('nombre_negocio')->nullable();
            $table->string('nit')->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono_negocio')->nullable();
            $table->string('email_negocio')->nullable();
            $table->string('website')->nullable();
            $table->text('mensaje_factura')->nullable();
            $table->string('logo_negocio')->nullable();
            
            // Impuestos
            $table->decimal('iva', 5, 2)->default(19);
            $table->boolean('incluir_iva')->default(true);
            $table->boolean('mostrar_iva')->default(true);
            
            // Alertas
            $table->integer('stock_minimo_alerta')->default(5);
            $table->boolean('alertar_stock')->default(true);
            $table->boolean('alertar_vencimiento')->default(false);
            $table->integer('dias_vencimiento')->default(30);
            
            // Backups
            $table->json('backups')->nullable();
            
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('configuraciones');
    }
}