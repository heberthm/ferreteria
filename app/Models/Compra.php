<?php
// app/Models/Compra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    
    protected $table = 'compras';
    protected $primaryKey = 'id_compra';
    
    // ⚠️ IMPORTANTE: Estos son los campos que deben estar en $fillable
    protected $fillable = [
        'id_producto',
        'id_proveedor',
        'cantidad',
        'precio_compra',      // Precio unitario de compra
        'precio_unitario',     // (alias opcional)
        'precio_total',        // cantidad * precio_compra
        'fecha_compra',
        'numero_factura',
        'notas',
        'user_id',             // o 'id_usuario' según tu convención
        'estado'               // 'completada', 'anulada', etc.
    ];
    
    // Casts para tipos de datos
    protected $casts = [
        'fecha_compra' => 'date',
        'precio_compra' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
        'cantidad' => 'integer'
    ];
    
    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
    
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function inventario()
    {
        return $this->hasOne(Inventario::class, 'id_compra', 'id_compra');
    }
    
    // Accessors y Mutators
    public function getPrecioCompraAttribute($value)
    {
        return $value ? number_format($value, 2) : '0.00';
    }
    
    public function getPrecioTotalAttribute($value)
    {
        return $value ? number_format($value, 2) : '0.00';
    }
    
    // Mutator para calcular total automáticamente
    public function setPrecioCompraAttribute($value)
    {
        $this->attributes['precio_compra'] = $value;
        $this->calcularTotal();
    }
    
    public function setCantidadAttribute($value)
    {
        $this->attributes['cantidad'] = $value;
        $this->calcularTotal();
    }
    
    private function calcularTotal()
    {
        if (isset($this->attributes['cantidad']) && isset($this->attributes['precio_compra'])) {
            $this->attributes['precio_total'] = 
                $this->attributes['cantidad'] * $this->attributes['precio_compra'];
        }
    }
    
    // Scopes útiles
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_compra', today());
    }
    
    public function scopeEsteMes($query)
    {
        return $query->whereMonth('fecha_compra', now()->month)
                     ->whereYear('fecha_compra', now()->year);
    }
    
    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('id_proveedor', $proveedorId);
    }
    
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
    
    // Métodos de ayuda
    public function getTotalFormateadoAttribute()
    {
        return '$ ' . number_format($this->precio_total, 2);
    }
    
    public function getPrecioCompraFormateadoAttribute()
    {
        return '$ ' . number_format($this->precio_compra, 2);
    }
    
    public function anular()
    {
        $this->estado = 'anulada';
        $this->save();
        
        // Aquí puedes agregar lógica para revertir stock
    }
}