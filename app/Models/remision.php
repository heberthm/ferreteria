<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Remision extends Model
{
    use HasFactory;

    protected $table      = 'remisiones';
    protected $primaryKey = 'id_remision';

    protected $fillable = [
        'numero_remision',
        'id_cliente',
        'cliente_nombre',
        'cliente_cedula',
        'cliente_telefono',
        'cliente_email',
        'id_vendedor',
        'conductor',
        'vehiculo_placa',
        'direccion_entrega',
        'fecha_remision',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'estado',
        'subtotal',
        'descuento',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'fecha_remision'          => 'date',
        'fecha_entrega_estimada'  => 'date',
        'fecha_entrega_real'      => 'date',
        'subtotal'                => 'integer',
        'descuento'               => 'integer',
        'total'                   => 'integer',
    ];

    // ── Relaciones ───────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function vendedor()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_vendedor', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(RemisionDetalle::class, 'id_remision', 'id_remision');
    }

    // ── Helpers ──────────────────────────────────────────────
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pendiente'    => 'warning',
            'en_transito'  => 'primary',
            'entregada'    => 'success',
            'anulada'      => 'danger',
            'parcial'      => 'secondary',
            default        => 'secondary',
        };
    }

    public function getEstadoTextoAttribute(): string
    {
        return match($this->estado) {
            'pendiente'    => 'Pendiente',
            'en_transito'  => 'En Tránsito',
            'entregada'    => 'Entregada',
            'anulada'      => 'Anulada',
            'parcial'      => 'Parcial',
            default        => ucfirst($this->estado),
        };
    }
}