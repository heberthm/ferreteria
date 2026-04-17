<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCompra extends Model
{
    use SoftDeletes;

    protected $table      = 'ordenes_compra';
    protected $primaryKey = 'id_orden';

    protected $fillable = [
        'numero_orden',
        'fecha_orden',
        'fecha_entrega_esperada',
        'fecha_entrega_real',
        'id_proveedor',
        'proveedor_nombre',
        'proveedor_nit',
        'proveedor_telefono',
        'proveedor_email',
        'proveedor_direccion',
        'metodo_pago',
        'dias_credito',
        'subtotal',
        'descuento_total',
        'impuesto_porcentaje',
        'impuesto_valor',
        'total',
        'estado',
        'observaciones',
        'terminos_condiciones',
        'userId',
    ];

    protected $casts = [
        'fecha_orden'            => 'date',
        'fecha_entrega_esperada' => 'date',
        'fecha_entrega_real'     => 'date',
        'subtotal'               => 'float',
        'descuento_total'        => 'float',
        'impuesto_porcentaje'    => 'float',
        'impuesto_valor'         => 'float',
        'total'                  => 'float',
    ];

    // ── Relaciones ───────────────────────────────────────────────────

    public function detalles()
    {
        return $this->hasMany(OrdenCompraDetalle::class, 'id_orden', 'id_orden');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'userId', 'id');
    }

    // ── Accessors ────────────────────────────────────────────────────

    /** Badge HTML para el estado */
    public function getEstadoBadgeAttribute(): string
    {
        return '<span class="badge badge-' . $this->estado_color . '">'
             . $this->estado_texto . '</span>';
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'borrador'         => 'secondary',
            'enviada'          => 'info',
            'confirmada'       => 'primary',
            'recibida_parcial' => 'warning',
            'recibida'         => 'success',
            'cancelada'        => 'danger',
            default            => 'secondary',
        };
    }

    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado) {
            'borrador'         => 'Borrador',
            'enviada'          => 'Enviada',
            'confirmada'       => 'Confirmada',
            'recibida_parcial' => 'Rec. Parcial',
            'recibida'         => 'Recibida',
            'cancelada'        => 'Cancelada',
            default            => ucfirst($this->estado),
        };
    }

    // ── Helpers estáticos ────────────────────────────────────────────

    /** Genera el siguiente número de orden correlativo */
    public static function siguienteNumero(): string
    {
        $hoy     = now()->format('Ymd');
        $prefijo = 'OC-' . $hoy . '-';

        $ultimo = static::withTrashed()
            ->where('numero_orden', 'like', $prefijo . '%')
            ->orderByDesc('numero_orden')
            ->value('numero_orden');

        $secuencia = $ultimo
            ? (int) substr($ultimo, -5) + 1
            : 1;

        return $prefijo . str_pad($secuencia, 5, '0', STR_PAD_LEFT);
    }
}