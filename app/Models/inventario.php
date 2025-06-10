<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventario extends Model
{
    use HasFactory;

      protected $table = 'inventario';
    
      protected $fillable = [
        'id_producto',
        'descripcion',
        'cantidad',
        'stock_minimo',
        'stock_maximo',
    ];


    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}


