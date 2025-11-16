<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimiento_caja';

    protected $fillable = [
        'fecha',
        'descripcion',
        'concepto',
        'tipo',
        'monto', 
        'estado',
        'userId',
        'referencia',
        'id_caja'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2'
    ];

    // Relación con usuario - CORREGIDA
    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relación con caja (si existe)
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja');
    }

    // ELIMINAR la relación tipoMovimiento si no existe
    // public function tipoMovimiento() { ... } ← Remover esta línea si existe

    // Métodos helpers para acceder al tipo
    public function getTipoTextoAttribute()
    {
        return $this->tipo === 'ingreso' ? 'INGRESO' : 'EGRESO';
    }

    public function getColorTipoAttribute()
    {
        return $this->tipo === 'ingreso' ? 'success' : 'danger';
    }

    public function getColorEstadoAttribute()
    {
        $colores = [
            'completado' => 'primary',
            'pendiente' => 'warning',
            'anulado' => 'secondary'
        ];
        
        return $colores[$this->estado] ?? 'secondary';
    }
}