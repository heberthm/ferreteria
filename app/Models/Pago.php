<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    
    protected $primaryKey = 'id_pago';
    
    protected $fillable = [
        'id_venta',
        'metodo_pago',
        'monto',
        'referencia',
        'estado'
    ];
    
    public $timestamps = true;
    
    /**
     * Relación con venta
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }
}