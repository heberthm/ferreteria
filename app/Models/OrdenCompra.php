<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';
    protected $primaryKey = 'id_orden';
    
    protected $fillable = [
        'numero_orden',
        'fecha_orden',
        'fecha_entrega_esperada',
        'id_proveedor',
        'proveedor_nombre',
        'proveedor_nit',
        'proveedor_telefono',
        'proveedor_email',
        'proveedor_direccion',
        'nombre_contacto',
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
        'fecha_orden' => 'date',
        'fecha_entrega_esperada' => 'date',
        'fecha_entrega_real' => 'date',
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto_valor' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    
    public function detalles()
    {
        return $this->hasMany(OrdenCompraDetalle::class, 'id_orden');
    }

        public static function siguienteNumero()
    {
        $ultimaOrden = self::orderBy('id_orden', 'desc')->first();
        
        if (!$ultimaOrden) {
            return 'OC-' . date('Ymd') . '-00001';
        }
        
        $ultimoNumero = $ultimaOrden->numero_orden;
        
        // Buscar el patrón OC-YYYYMMDD-XXXXX
        if (preg_match('/OC-(\d{8})-(\d+)$/', $ultimoNumero, $matches)) {
            $fechaUltima = $matches[1];
            $secuencial = intval($matches[2]);
            
            // Si es del mismo día, incrementar secuencial
            if ($fechaUltima == date('Ymd')) {
                $nuevoSecuencial = $secuencial + 1;
                return 'OC-' . date('Ymd') . '-' . str_pad($nuevoSecuencial, 5, '0', STR_PAD_LEFT);
            }
        }
        
        // Si es un día diferente, empezar desde 1
        return 'OC-' . date('Ymd') . '-00001';
    }

}