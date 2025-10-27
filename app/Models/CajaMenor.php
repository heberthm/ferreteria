<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaMenor extends Model
{
    use HasFactory;

    protected $table = 'caja_menor';
    
    protected $primaryKey = 'id_caja';
    
    public $incrementing = true;
    
    protected $fillable = [
        'id_caja',
        'monto_inicial',
        'monto_actual',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones_apertura',
        'observaciones_cierre',
        'user_id_apertura',
        'user_id_cierre'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relación con movimientos
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'id_caja', 'id_caja');
    }

    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'user_id_apertura', 'id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'user_id_cierre', 'id');
    }
}