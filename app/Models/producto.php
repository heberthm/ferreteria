<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class producto extends Model
{
    use HasFactory;

     protected $table = 'productos';
    protected $primaryKey = 'id_producto'; // Especificar la clave primaria

       protected $fillable = [
        
        'codigo',
        'nombre',
        'descripcion',
        'cantidad',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'imagen',
        'activo',
        'unidad_medida',
        'ubicacion',
        'marca',
        'categoria',
        'proveedor',
        'id_categoria',
        'id_proveedor',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Agotado';
        } elseif ($this->stock < $this->min_stock) {
            return 'Bajo stock';
        }
        return 'Disponible';
    }

}
