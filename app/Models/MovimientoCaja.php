<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    // CAMBIA ESTA LÍNEA CON EL NOMBRE CORRECTO DE TU TABLA
    protected $table = 'movimiento_caja'; // o el nombre que tengas
    
    protected $fillable = [
        'id_caja',
        'tipo',
        'monto',
        'concepto',
        'descripcion',
        'userId'
    ];

    public function cajaMenor()
    {
        return $this->belongsTo(CajaMenor::class, 'id_caja', 'id_caja');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}