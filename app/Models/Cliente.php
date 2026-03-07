<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'userId',
        'cedula',
        'nombre',
        'telefono',
        'email',
        'direccion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'string'
    ];

    // Relación con el usuario que creó/modificó
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relación con las ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente');
    }
}