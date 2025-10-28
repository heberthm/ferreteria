<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimiento_caja'; // Asegúrate de que este sea el nombre correcto
    
    protected $fillable = [
        'id_caja',
        'tipo',
        'monto',
        'concepto',
        'descripcion',
        'userId'
    ];

    // Relación con CajaMenor
    public function cajaMenor()
    {
        return $this->belongsTo(CajaMenor::class, 'id_caja', 'id_caja');
    }

    // Relación con User - VERIFICA QUE ESTÉ CORRECTA
    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
} 