<?php
// app/Models/Producto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock',
        'stock_minimo',
        'precio_venta',
        'imagen',
        'activo',
        'unidad_medida',
        'ubicacion',
        'marca',
        'id_categoria',
        'id_proveedor',
        'frecuente'
    ];

    // 👇 ELIMINADOS: cantidad, precio_compra, categoria, proveedor
    // 'cantidad' - Usa 'stock' en su lugar
    // 'precio_compra' - Este dato pertenece a las compras, no al producto
    // 'categoria' - Usa 'id_categoria' para relación
    // 'proveedor' - Usa 'id_proveedor' para relación

    /**
     * Relación con compras
     * Un producto tiene muchas compras
     */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_producto', 'id_producto');
    }

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Relación con proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * Obtener el último precio de compra
     */
    public function ultimoPrecioCompra()
    {
        $ultimaCompra = $this->compras()
            ->orderBy('fecha_compra', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $ultimaCompra ? $ultimaCompra->precio_unitario : 0;
    }

    /**
     * Obtener precio promedio de compra
     */
    public function precioPromedioCompra()
    {
        return $this->compras()
            ->avg('precio_unitario') ?? 0;
    }

    /**
     * Actualizar stock
     */
    public function actualizarStock($cantidad, $operacion = 'sumar')
    {
        if ($operacion === 'sumar') {
            $this->stock += $cantidad;
        } else {
            $this->stock -= $cantidad;
        }
        $this->save();
        
        return $this->stock;
    }

/**
 * Actualizar el costo promedio ponderado del producto
 * 
 * Fórmula: ((stock_anterior * costo_promedio_anterior) + (cantidad_nueva * costo_nuevo)) / (stock_anterior + cantidad_nueva)
 */
public function actualizarCostoPromedio($cantidad_nueva, $costo_nuevo)
{
    // Stock actual antes de la compra
    $stock_anterior = $this->stock;
    
    // Costo promedio actual (si no existe, usar 0)
    $costo_promedio_anterior = $this->costo_promedio ?? 0;
    
    // Calcular nuevo costo promedio ponderado
    if ($stock_anterior + $cantidad_nueva > 0) {
        $nuevo_costo_promedio = (($stock_anterior * $costo_promedio_anterior) + ($cantidad_nueva * $costo_nuevo)) / ($stock_anterior + $cantidad_nueva);
    } else {
        $nuevo_costo_promedio = $costo_nuevo;
    }
    
    // Guardar el último costo
    $this->ultimo_costo = $costo_nuevo;
    
    // Actualizar el costo promedio
    $this->costo_promedio = round($nuevo_costo_promedio, 2);
    
    // Opcional: Actualizar precio de venta basado en el nuevo costo + margen
    // $this->precio_venta = $this->costo_promedio * (1 + $this->margen_ganancia);
    
    $this->save();
    
    return $this->costo_promedio;
}

}