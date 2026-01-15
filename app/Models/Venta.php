<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    
    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'numero_factura',
        'id_cliente',
        'subtotal',
        'iva',
        'total',
        'metodo_pago',
        'tipo_comprobante',
        'referencia_pago',
        'efectivo_recibido',
        'cambio',
        'userId',
        'fecha_venta',
        'estado'
    ];
    
    public $timestamps = true;


    // ← AGREGAR ESTA PROPIEDAD PARA CASTING AUTOMÁTICO
    protected $casts = [
        'productos' => 'array', // Convierte automáticamente JSON ↔ Array
        'fecha_venta' => 'datetime',
        'subtotal' => 'string',
        'iva' => 'string',
        'total' => 'string',
        'efectivo_recibido' => 'string',
        'cambio' => 'string'
    ];
    
    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
    
    /**
     * Relación con detalles de venta - CORREGIDO
     */
    public function detalles()
    {
      
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }
    
    /**
     * Relación con pago
     */
    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_venta', 'id_venta');
    }
    
    /**
     * Relación con usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}