<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'nombre',
        'cedula', 
        'email',
        'telefono',
        'direccion'
    ];

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente');
    }
}