<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

 protected $fillable = [
        'numero_factura', 'fecha_venta', 'total', 'iva', 
        'descuento', 'gran_total', 'metodo_pago', 
        'observaciones', 'id_cliente', 'user_id'
    ];

    protected $dates = ['fecha_venta'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
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
        return 'FAC' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }


}
