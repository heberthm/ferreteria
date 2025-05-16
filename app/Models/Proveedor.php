<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

     protected $fillable = [
        'nombre',
        'nombre_contacto',
        'telefono',
        'email',
        'direccion',
    ];

    public function producto()
    {
        return $this->hasMany(Producto::class);
    }
}
