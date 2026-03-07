<?php
// database/migrations/xxxx_xx_xx_update_productos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Obtener la base de datos actual
        $database = DB::connection()->getDatabaseName();
        echo "📊 Base de datos actual: " . $database . "\n";
        
        // Verificar todas las tablas
        $tables = DB::select('SHOW TABLES');
        $tablas = [];
        foreach ($tables as $table) {
            $tablas[] = current($table);
        }
        
        echo "📋 Tablas encontradas: " . implode(', ', $tablas) . "\n";
        
        // Buscar la tabla productos (case insensitive)
        $tableName = null;
        foreach ($tablas as $tabla) {
            if (strtolower($tabla) === 'productos') {
                $tableName = $tabla;
                echo "✅ Tabla encontrada: '" . $tabla . "'\n";
                break;
            }
        }
        
        // Si no encuentra la tabla, la crea
        if (!$tableName) {
            echo "⚠️ Tabla 'productos' no encontrada. Creándola...\n";
            Schema::create('productos', function (Blueprint $table) {
                $table->id('id_producto');
                $table->string('codigo');
                $table->string('nombre');
                $table->text('descripcion')->nullable();
               $table->integer('stock_actual')->default(0);      // ← Stock actual (se actualiza con cada movimiento)
                $table->integer('stock_minimo')->default(0);      // ← Stock mínimo (alerta de reposición)
                $table->integer('stock_maximo')->nullable();      // ← Opcional: stock máximo
                $table->decimal('costo_promedio', 10, 2)->nullable();
                $table->decimal('ultimo_costo', 10, 2)->nullable();
                $table->decimal('precio_venta', 10, 2)->nullable();
                $table->string('imagen', 500)->nullable();
                $table->boolean('activo')->default(true);
                $table->string('unidad_medida')->nullable();
                $table->string('ubicacion')->nullable();
                $table->string('marca')->nullable();
                $table->boolean('frecuente')->default(false);
                $table->timestamps();
            });
            $tableName = 'productos';
            echo "✅ Tabla 'productos' creada exitosamente.\n";
        }
        
        // Ahora modificamos la tabla con el nombre correcto
        try {
            Schema::table($tableName, function (Blueprint $table) {
                // Agregar columnas si no existen
                if (!Schema::hasColumn($table->getTable(), 'id_categoria')) {
                    $table->unsignedBigInteger('id_categoria')->nullable()->after('marca');
                    echo "✅ Columna 'id_categoria' agregada.\n";
                }
                
                if (!Schema::hasColumn($table->getTable(), 'id_proveedor')) {
                    $table->unsignedBigInteger('id_proveedor')->nullable()->after('id_categoria');
                    echo "✅ Columna 'id_proveedor' agregada.\n";
                }
                
                // Agregar foreign keys si las tablas existen
                if (Schema::hasTable('categorias') && Schema::hasColumn($table->getTable(), 'id_categoria')) {
                    try {
                        $table->foreign('id_categoria')
                              ->references('id_categoria')
                              ->on('categorias')
                              ->onDelete('set null');
                        echo "✅ Foreign key 'id_categoria' agregada.\n";
                    } catch (\Exception $e) {
                        echo "⚠️ Error foreign key categorias: " . $e->getMessage() . "\n";
                    }
                }
                
                if (Schema::hasTable('proveedores') && Schema::hasColumn($table->getTable(), 'id_proveedor')) {
                    try {
                        $table->foreign('id_proveedor')
                              ->references('id_proveedor')
                              ->on('proveedores')
                              ->onDelete('set null');
                        echo "✅ Foreign key 'id_proveedor' agregada.\n";
                    } catch (\Exception $e) {
                        echo "⚠️ Error foreign key proveedores: " . $e->getMessage() . "\n";
                    }
                }
            });
            
            echo "🎉 Migración completada exitosamente.\n";
            
        } catch (\Exception $e) {
            echo "❌ Error al modificar la tabla: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function down()
    {
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                // Eliminar foreign keys
                try {
                    $table->dropForeign(['id_categoria']);
                } catch (\Exception $e) {}
                
                try {
                    $table->dropForeign(['id_proveedor']);
                } catch (\Exception $e) {}
                
                // Eliminar columnas
                if (Schema::hasColumn('productos', 'id_categoria')) {
                    $table->dropColumn('id_categoria');
                }
                
                if (Schema::hasColumn('productos', 'id_proveedor')) {
                    $table->dropColumn('id_proveedor');
                }
            });
        }
    }
};