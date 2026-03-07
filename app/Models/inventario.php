<?php
// app/Models/Inventario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventarios';
    protected $primaryKey = 'id_inventario'; // Ajusta según tu tabla
    
    protected $fillable = [
        'id_producto',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'precio_venta',
        'costo_promedio',
        'ultimo_costo',
        'precio_compra',
        'proveedor',
        'numero_factura',
        'id_venta',
        'fecha_movimiento',
        'notas',
        'userId'
    ];

    protected $casts = [
        'fecha_movimiento' => 'date'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId', 'userId');
    }
}