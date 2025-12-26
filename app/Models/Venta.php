<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

 protected $fillable = [
        'numero_factura',
        'fecha_venta',
        'total',
        'iva', 
        'descuento',       
        'metodo_pago', 
        'observaciones',
        'id_cliente',
        'userId',
        'total',
        'metodo_pago',
        'tipo_comprobante',
        'vendedor'
    ];

    protected $dates = ['fecha_venta'];

     public function cliente()
    {
        // Si la clave foránea en ventas es 'cliente_id' y la primaria en clientes es 'id_cliente'
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalleVenta()
    {
        return $this->hasMany(detalleVenta::class);
    }

    // Generar número de factura automático
    public static function generateInvoiceNumber()
    {
        $lastSale = static::latest()->first();
        $number = $lastSale ? (int) substr($lastSale->numero_factura, 3) + 1 : 1;
        return  str_pad($number, 6, '0', STR_PAD_LEFT);
    }


}
