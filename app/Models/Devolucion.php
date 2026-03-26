<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devolucion extends Model
{
    use SoftDeletes;

    protected $table = 'devoluciones';
    protected $primaryKey = 'id_devolucion';

    protected $fillable = [
        'numero_devolucion',
        'fecha_devolucion',
        'numero_devolucion',
        'id_venta',
        'id_cliente',
        'cliente_nombre',
        'cliente_cedula',
        'cliente_telefono',
        'cliente_email',
        'tipo_devolucion',
        'motivo',
        'motivo_descripcion',
        'subtotal',
        'descuento_total',
        'iva_total',
        'total',
        'estado',
        'metodo_reembolso',
        'reingresar_inventario',
        'observaciones',
        'notas_internas',
        'creado_por',
        'aprobado_por',
        'fecha_aprobacion'
    ];

    protected $casts = [
        'fecha_devolucion' => 'date',
        'fecha_aprobacion' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'iva_total' => 'decimal:2',
        'total' => 'decimal:2',
        'reingresar_inventario' => 'boolean'
    ];

    // Relaciones
    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class, 'id_devolucion', 'id_devolucion');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por', 'userId');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por', 'userId');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_devolucion', [$desde, $hasta]);
    }

    // Métodos
    public function aprobar($userId)
    {
        $this->estado = 'aprobada';
        $this->aprobado_por = $userId;
        $this->fecha_aprobacion = now();
        $this->save();

        // Si reingresar inventario está activo, aumentar stock
        if ($this->reingresar_inventario) {
            foreach ($this->detalles as $detalle) {
                $producto = Producto::find($detalle->id_producto);
                if ($producto) {
                    $producto->stock_actual += $detalle->cantidad_devuelta;
                    $producto->save();
                }
            }
        }
    }

    public function rechazar($userId, $motivo = null)
    {
        $this->estado = 'rechazada';
        $this->aprobado_por = $userId;
        $this->fecha_aprobacion = now();
        if ($motivo) {
            $this->notas_internas = ($this->notas_internas ? $this->notas_internas . "\n" : '') . 
                "Rechazada: " . $motivo;
        }
        $this->save();
    }

    public function completar()
    {
        $this->estado = 'completada';
        $this->save();
    }

    public function cancelar()
    {
        $this->estado = 'cancelada';
        $this->save();
    }

    // Generar número de devolución
   // ── MÉTODO PARA GENERAR NÚMERO DE DEVOLUCIÓN ───────────────────────────────
    public static function generarNumeroDevolucion(): string
    {
        $prefijo = 'DEV-' . date('Ymd') . '-';
        $ultimo  = self::where('numero_devolucion', 'like', $prefijo . '%')
            ->orderBy('numero_devolucion', 'desc')
            ->value('numero_devolucion');

        $siguiente = $ultimo
            ? (int) substr($ultimo, -5) + 1
            : 1;

        return $prefijo . str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }

    // ── BOOT: Asignar número automáticamente al crear ──────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($devolucion) {
            if (empty($devolucion->numero_devolucion)) {
                $devolucion->numero_devolucion = self::generarNumeroDevolucion();
            }
        });
    }

    // ── RELACIONES (si las tienes) ────────────────────────────────────────────
    public function remision()
    {
        return $this->belongsTo(Remision::class, 'id_remision', 'id_remision');
    }
}