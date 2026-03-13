<?php
// app/Models/CotizacionDetalle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    use HasFactory;

    protected $table = 'cotizacion_detalles';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_cotizacion',
        'id_producto',
        'codigo_producto',
        'nombre_producto',
        'unidad_medida',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'total',
        'observaciones'
    ];

    /*
    protected $casts = [
        'cantidad' => 'decimal',
        'precio_unitario' => 'decimal',
        'descuento' => 'decimal',
        'subtotal' => 'decimal',
        'total' => 'decimal'
    ];

    */

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion', 'id_cotizacion');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function getSubtotalFormateadoAttribute()
    {
        return '$' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total, 0, ',', '.');
    }
}