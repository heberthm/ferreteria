<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'venta_id', 'metodo_pago', 'monto', 'datos_pago', 'fecha_pago'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'datos_pago' => 'array',
        'fecha_pago' => 'datetime'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}