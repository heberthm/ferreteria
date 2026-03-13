<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemisionDetalle extends Model
{
    protected $table      = 'remision_detalles';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_remision',
        'id_producto',
        'codigo_producto',
        'nombre_producto',
        'unidad_medida',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'cantidad'        => 'integer',
        'precio_unitario' => 'integer',
        'descuento'       => 'integer',
        'subtotal'        => 'integer',
        'total'           => 'integer',
    ];

    public function remision()
    {
        return $this->belongsTo(Remision::class, 'id_remision', 'id_remision');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}