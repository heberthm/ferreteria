<?php
// app/Models/Compra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id_compra';
    
    protected $fillable = [
        'id_producto',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'id_proveedor',        
        'numero_factura',
        'fecha_compra',
        'metodo_pago',
        'notas',
        'usuario_registro'
    ];

    protected $casts = [
        'fecha_compra' => 'date',        
    ];

    /**
     * Relación con producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    /**
     * Relación con proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * Relación con usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_registro', 'id');
    }

    /**
     * Scope para compras de hoy
     */
    public function scopeDeHoy($query)
    {
        return $query->whereDate('fecha_compra', now()->toDateString());
    }

    /**
     * Scope para rango de fechas
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_compra', [$inicio, $fin]);
    }

    /**
     * Scope para proveedor específico
     */
    public function scopeDeProveedor($query, $idProveedor)
    {
        return $query->where('id_proveedor', $idProveedor);
    }
}