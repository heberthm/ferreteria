<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caja extends Model
{
    use HasFactory;

 protected $fillable = [
        'fecha_caja',
        'saldo_inicial',
        'saldo_final',
        'total_ingresos',
        'total_egresos',
        'estado_caja',
        'userId',
        'observaciones',
 ];

 protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];
    
    // Constantes para estados
    const ABIERTA = 'abierta';
    const CERRADA = 'cerrada';
    const EN_REVISION = 'en_revision';
    
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
    
    // Scope para cajas abiertas
    public function scopeAbierta($query)
    {
        return $query->where('estado', self::ABIERTA);
    }
    
    // Scope para cajas cerradas
    public function scopeCerrada($query)
    {
        return $query->where('estado', self::CERRADA);
    }
    
    // Scope para cajas en revisión
    public function scopeEnRevision($query)
    {
        return $query->where('estado', self::EN_REVISION);
    }
    
    // Método para verificar si la caja está abierta
    public function estaAbierta()
    {
        return $this->estado === self::ABIERTA;
    }
    
    // Método para verificar si la caja está cerrada
    public function estaCerrada()
    {
        return $this->estado === self::CERRADA;
    }
    
    // Método para verificar si la caja está en revisión
    public function estaEnRevision()
    {
        return $this->estado === self::EN_REVISION;
    }
}