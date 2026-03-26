<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';
    protected $primaryKey = 'id_inventario';
    
    protected $fillable = [
        'id_producto',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'precio_venta',
        'costo_promedio',
        'ultimo_costo',
        'precio_compra',
        'metodo_pago',
        'proveedor',
        'numero_factura',
        'id_venta',
        'fecha_movimiento',
        'notas',
        'userId'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
        'costo_promedio' => 'decimal:2',
        'ultimo_costo' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'fecha_movimiento' => 'date'
    ];

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    /**
     * Relación con la venta
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    /**
     * Scope para filtrar por tipo de movimiento
     */
    public function scopeTipoMovimiento($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo_movimiento', $tipo);
        }
        return $query;
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeFechaEntre($query, $fechaInicio, $fechaFin)
    {
        if ($fechaInicio && $fechaFin) {
            return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
        }
        return $query;
    }

    /**
     * Scope para filtrar por producto
     */
    public function scopeProducto($query, $productoId)
    {
        if ($productoId) {
            return $query->where('id_producto', $productoId);
        }
        return $query;
    }

    /**
     * Scope para filtrar por proveedor
     */
    public function scopeProveedor($query, $proveedor)
    {
        if ($proveedor) {
            return $query->where('proveedor', 'LIKE', "%{$proveedor}%");
        }
        return $query;
    }

    /**
     * Scope para filtrar por número de factura
     */
    public function scopeNumeroFactura($query, $factura)
    {
        if ($factura) {
            return $query->where('numero_factura', 'LIKE', "%{$factura}%");
        }
        return $query;
    }

    /**
     * Accesor para obtener el tipo de movimiento con formato
     */
    public function getTipoMovimientoFormattedAttribute()
    {
        $tipos = [
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'ajuste' => 'Ajuste de Inventario',
            'devolucion' => 'Devolución'
        ];
        
        return $tipos[$this->tipo_movimiento] ?? $this->tipo_movimiento;
    }

    /**
     * Accesor para obtener el color del tipo de movimiento
     */
    public function getTipoMovimientoColorAttribute()
    {
        $colores = [
            'entrada' => 'success',
            'salida' => 'danger',
            'ajuste' => 'warning',
            'devolucion' => 'info'
        ];
        
        return $colores[$this->tipo_movimiento] ?? 'secondary';
    }

    /**
     * Método para calcular el costo promedio
     */
    public static function calcularCostoPromedio($productoId, $nuevoCosto, $nuevaCantidad)
    {
        $producto = Producto::find($productoId);
        
        if (!$producto) {
            return $nuevoCosto;
        }
        
        $stockActual = $producto->stock;
        $costoActual = $producto->precio_compra;
        
        if ($stockActual == 0) {
            return $nuevoCosto;
        }
        
        $costoPromedio = (($stockActual * $costoActual) + ($nuevaCantidad * $nuevoCosto)) / ($stockActual + $nuevaCantidad);
        
        return round($costoPromedio, 2);
    }
}