<?php
// app/Models/Configuracion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    protected $primaryKey = 'id_configuracion';
    
    protected $fillable = [
        // General
        'nombre_sistema',
        'zona_horaria',
        'formato_fecha',
        'moneda',
        'simbolo_moneda',
        
        // Facturación
        'prefijo_factura',
        'consecutivo_inicial',
        'consecutivo_actual',
        'longitud_numero',
        'formato_factura',
        'autogenerar',
        'validar_duplicados',
        'factura_electronica',
        'tamaño_papel',
        'copias',
        
        // Negocio
        'nombre_negocio',
        'nit',
        'direccion',
        'telefono_negocio',
        'email_negocio',
        'website',
        'mensaje_factura',
        'logo_negocio',
        
        // Impuestos
        'iva',
        'incluir_iva',
        'mostrar_iva',
        
        // Alertas
        'stock_minimo_alerta',
        'alertar_stock',
        'alertar_vencimiento',
        'dias_vencimiento',
        
        // Backups
        'backups'
    ];
    
    protected $casts = [
        'autogenerar' => 'boolean',
        'validar_duplicados' => 'boolean',
        'factura_electronica' => 'boolean',
        'incluir_iva' => 'boolean',
        'mostrar_iva' => 'boolean',
        'alertar_stock' => 'boolean',
        'alertar_vencimiento' => 'boolean',
        'backups' => 'array'
    ];
}