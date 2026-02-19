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
}