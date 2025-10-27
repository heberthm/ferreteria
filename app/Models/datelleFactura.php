<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'detalle_factura';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_factura',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descuento',
        'impuesto',
        'total_linea',
        'observaciones'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total_linea' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Factura
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Calcular subtotal automáticamente
     */
    public function calcularSubtotal()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Calcular total de línea
     */
    public function calcularTotalLinea()
    {
        $subtotal = $this->calcularSubtotal();
        $descuento = $subtotal * ($this->descuento / 100);
        $impuesto = ($subtotal - $descuento) * ($this->impuesto / 100);
        
        return $subtotal - $descuento + $impuesto;
    }
}