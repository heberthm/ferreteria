<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraDetalle extends Model
{
    protected $table = 'ordenes_compra_detalle';
    protected $primaryKey = 'id_detalle';
    
    protected $fillable = [
        'id_orden',           
        'id_producto',
        'codigo_producto',
        'nombre_producto',
        'unidad_medida',
        'cantidad',
        'precio_unitario',
        'descuento',
        'total_linea',
    ];
    
    // Relaciones
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_orden');
    }
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}