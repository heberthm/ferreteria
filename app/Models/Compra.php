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
        'precio_unitario',
        'precio_compra',      
        'precio_total',
         'total_compra',
        'proveedor',
        'numero_factura',
        'fecha_compra',
        'metodo_pago',
        'notas',
        'estado',             
        'usuario_registro',
        'user_id',           
    ];
    
    // Casts para tipos de datos
    protected $casts = [
        'fecha_compra'    => 'date',
        'precio_unitario' => 'decimal:2',
        'precio_compra'   => 'decimal:2',
        'precio_total'    => 'decimal:2',
        'cantidad'        => 'integer',
    ];
    
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

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_compra', today());
    }

    public function scopeEsteMes($query)
    {
        return $query->whereMonth('fecha_compra', now()->month)
                     ->whereYear('fecha_compra', now()->year);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
   
   
    public function anular()
    {
        $this->estado = 'anulada';
        $this->save();
        
        // Aquí puedes agregar lógica para revertir stock
    }
}