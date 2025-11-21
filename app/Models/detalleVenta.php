<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalleVenta extends Model
{
    use HasFactory;

     protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad', 
        'precio_unitario',
        'total',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
