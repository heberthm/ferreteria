<?php
// app/Models/Cotizacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';
    protected $primaryKey = 'id_cotizacion';

    protected $fillable = [
        'numero_cotizacion',
        'id_cliente',
        'cliente_nombre',
        'cliente_cedula',
        'cliente_telefono',
        'cliente_email',
        'id_vendedor',
        'fecha_cotizacion',
        'fecha_validez',
        'subtotal',
        'impuesto',
        'descuento',
        'total',
        'observaciones',
        'estado',
        'metodo_pago_sugerido',
        'terminos_condiciones'
    ];

    protected $casts = [
        'fecha_cotizacion' => 'datetime',
        'fecha_validez' => 'date',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor');
    }

    public function detalles()
    {
        return $this->hasMany(CotizacionDetalle::class, 'id_cotizacion', 'id_cotizacion');
    }

    public function getEstadoColorAttribute()
    {
        $colores = [
            'activa' => 'success',
            'vencida' => 'danger',
            'aceptada' => 'primary',
            'rechazada' => 'secondary'
        ];
        return $colores[$this->estado] ?? 'secondary';
    }

    public function getEstadoTextoAttribute()
    {
        $textos = [
            'activa' => 'Activa',
            'vencida' => 'Vencida',
            'aceptada' => 'Aceptada',
            'rechazada' => 'Rechazada'
        ];
        return $textos[$this->estado] ?? $this->estado;
    }

    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total, 0, ',', '.');
    }
}