<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevolucionDetalle extends Model
{
    use SoftDeletes;

    protected $table      = 'devoluciones_detalle';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_devolucion',
        'id_producto',
        'nombre_producto',
        'codigo_producto',
        'cantidad_devuelta',
        'cantidad_original',
        'precio_unitario',
        'descuento',
        'subtotal',
        'iva',
        'total',
        'condicion_producto',
        'observaciones',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'descuento'       => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'id_devolucion', 'id_devolucion');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}