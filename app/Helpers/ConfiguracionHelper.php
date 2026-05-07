<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ConfiguracionHelper
{
    /**
     * Obtener toda la configuración completa
     */
    public static function getConfiguracionCompleta()
    {
        return Cache::remember('configuracion_completa', 3600, function () {
            $config = DB::table('configuraciones')->first();
            
            if (!$config) {
                return self::getDefaultConfiguracion();
            }
            
            return [
                'general' => self::getGeneralConfig($config),
                'facturacion' => self::getFacturacionConfig($config),
                'negocio' => self::getNegocioConfig($config),
                'impuestos' => self::getImpuestosConfig($config),
                'alertas' => self::getAlertasConfig($config),
                'backup' => self::getBackupConfig($config),
            ];
        });
    }
    
    /**
     * Obtener configuración general
     */
    public static function getGeneralConfig($config = null)
    {
        if (!$config) {
            $config = DB::table('configuraciones')->first();
        }
        
        if (!$config) {
            return [
                'nombre_sistema' => 'Sistema Ferretero',
                'version' => '1.0.0',
                'zona_horaria' => 'America/Bogota',
                'formato_fecha' => 'd/m/Y',
                'moneda' => 'COP',
                'simbolo_moneda' => '$',
            ];
        }
        
        return [
            'nombre_sistema' => $config->nombre_sistema ?? 'Sistema Ferretero',
            'version' => $config->version ?? '1.0.0',
            'zona_horaria' => $config->zona_horaria ?? 'America/Bogota',
            'formato_fecha' => $config->formato_fecha ?? 'd/m/Y',
            'moneda' => $config->moneda ?? 'COP',
            'simbolo_moneda' => $config->simbolo_moneda ?? '$',
        ];
    }
    
    /**
     * Obtener configuración de facturación
     */
    public static function getFacturacionConfig($config = null)
    {
        if (!$config) {
            $config = DB::table('configuraciones')->first();
        }
        
        if (!$config) {
            return self::getDefaultFacturacionConfig();
        }
        
        return [
            'prefijo_factura' => $config->prefijo_factura ?? 'FAC',
            'consecutivo_inicial' => (int)($config->consecutivo_inicial ?? 1),
            'consecutivo_actual' => (int)($config->consecutivo_actual ?? 1),
            'proximo_numero' => (int)($config->consecutivo_actual ?? 1) + 1,
            'longitud_numero' => (int)($config->longitud_numero ?? 6),
            'formato_factura' => $config->formato_factura ?? 'simple',
            'autogenerar' => (bool)($config->autogenerar ?? 1),
            'validar_duplicados' => (bool)($config->validar_duplicados ?? 1),
            'factura_electronica' => (bool)($config->factura_electronica ?? 0),
            'tamaño_papel' => $config->tamaño_papel ?? 'thermal',
            'copias' => (int)($config->copias ?? 1),
        ];
    }
    
    /**
     * Obtener configuración de impuestos
     */
    public static function getImpuestosConfig($config = null)
    {
        if (!$config) {
            $config = DB::table('configuraciones')->first();
        }
        
        if (!$config) {
            return [
                'iva' => 19.00,
                'incluir_iva' => true,
                'mostrar_iva' => true,
                'tasa_iva' => 19.00, // Alias para compatibilidad
                'calcular_iva' => true,
            ];
        }
        
        return [
            'iva' => (float)($config->iva ?? 19),
            'incluir_iva' => (bool)($config->incluir_iva ?? 1),
            'mostrar_iva' => (bool)($config->mostrar_iva ?? 1),
            'tasa_iva' => (float)($config->iva ?? 19), // Alias
            'calcular_iva' => (bool)($config->incluir_iva ?? 1),
        ];
    }
    
    /**
     * Obtener configuración de alertas
     */
    public static function getAlertasConfig($config = null)
    {
        if (!$config) {
            $config = DB::table('configuraciones')->first();
        }
        
        if (!$config) {
            return [
                'stock_minimo_alerta' => 5,
                'alertar_stock' => true,
                'alertar_vencimiento' => false,
                'dias_vencimiento' => 30,
                'mostrar_alertas_dashboard' => true,
                'notificar_stock_bajo' => true,
                'notificar_productos_vencidos' => false,
            ];
        }
        
        return [
            'stock_minimo_alerta' => (int)($config->stock_minimo_alerta ?? 5),
            'alertar_stock' => (bool)($config->alertar_stock ?? 1),
            'alertar_vencimiento' => (bool)($config->alertar_vencimiento ?? 0),
            'dias_vencimiento' => (int)($config->dias_vencimiento ?? 30),
            'mostrar_alertas_dashboard' => (bool)($config->alertar_stock ?? 1),
            'notificar_stock_bajo' => (bool)($config->alertar_stock ?? 1),
            'notificar_productos_vencidos' => (bool)($config->alertar_vencimiento ?? 0),
        ];
    }
    
    /**
     * Obtener configuración del negocio
     */
    public static function getNegocioConfig($config = null)
    {
        if (!$config) {
            $config = DB::table('configuraciones')->first();
        }
        
        if (!$config) {
            return [
                'nombre_negocio' => 'Mi Negocio',
                'nit' => '',
                'direccion' => '',
                'telefono_negocio' => '',
                'email_negocio' => '',
                'website' => '',
                'mensaje_factura' => 'Gracias por su compra',
                'logo_negocio' => null,
            ];
        }
        
        return [
            'nombre_negocio' => $config->nombre_negocio ?? 'Mi Negocio',
            'nit' => $config->nit ?? '',
            'direccion' => $config->direccion ?? '',
            'telefono_negocio' => $config->telefono_negocio ?? '',
            'email_negocio' => $config->email_negocio ?? '',
            'website' => $config->website ?? '',
            'mensaje_factura' => $config->mensaje_factura ?? 'Gracias por su compra',
            'logo_negocio' => $config->logo_negocio ?? null,
        ];
    }
    
    /**
     * Obtener configuración de backup
     */
    public static function getBackupConfig($config = null)
    {
        if (!$config) {
            $config = DB::table('configuraciones')->first();
        }
        
        if (!$config) {
            return [
                'backup_automatico' => false,
                'hora_backup' => '00:00',
                'periodo_backup' => 'diario',
                'ultimo_backup' => null,
            ];
        }
        
        return [
            'backup_automatico' => (bool)($config->backup_automatico ?? 0),
            'hora_backup' => $config->hora_backup ?? '00:00',
            'periodo_backup' => $config->periodo_backup ?? 'diario',
            'ultimo_backup' => $config->ultimo_backup ?? null,
        ];
    }
    
    /**
     * Configuración por defecto completa
     */
    private static function getDefaultConfiguracion()
    {
        return [
            'general' => [
                'nombre_sistema' => 'Sistema Ferretero',
                'version' => '1.0.0',
                'zona_horaria' => 'America/Bogota',
                'formato_fecha' => 'd/m/Y',
                'moneda' => 'COP',
                'simbolo_moneda' => '$',
            ],
            'facturacion' => self::getDefaultFacturacionConfig(),
            'negocio' => [
                'nombre_negocio' => 'Mi Negocio',
                'nit' => '',
                'direccion' => '',
                'telefono_negocio' => '',
                'email_negocio' => '',
                'website' => '',
                'mensaje_factura' => 'Gracias por su compra',
                'logo_negocio' => null,
            ],
            'impuestos' => [
                'iva' => 19.00,
                'incluir_iva' => true,
                'mostrar_iva' => true,
                'tasa_iva' => 19.00,
                'calcular_iva' => true,
            ],
            'alertas' => [
                'stock_minimo_alerta' => 5,
                'alertar_stock' => true,
                'alertar_vencimiento' => false,
                'dias_vencimiento' => 30,
                'mostrar_alertas_dashboard' => true,
                'notificar_stock_bajo' => true,
                'notificar_productos_vencidos' => false,
            ],
            'backup' => [
                'backup_automatico' => false,
                'hora_backup' => '00:00',
                'periodo_backup' => 'diario',
                'ultimo_backup' => null,
            ],
        ];
    }
    
    /**
     * Configuración por defecto de facturación
     */
    private static function getDefaultFacturacionConfig()
    {
        return [
            'prefijo_factura' => 'FAC',
            'consecutivo_inicial' => 1,
            'consecutivo_actual' => 1,
            'proximo_numero' => 2,
            'longitud_numero' => 6,
            'formato_factura' => 'simple',
            'autogenerar' => true,
            'validar_duplicados' => true,
            'factura_electronica' => false,
            'tamaño_papel' => 'thermal',
            'copias' => 1,
        ];
    }
    
    /**
     * Generar número de factura según configuración
     */
    public static function generarNumeroFactura()
    {
        $config = DB::table('configuraciones')->first();
        
        if (!$config) {
            $prefijo = 'FAC';
            $longitud = 6;
            $consecutivo = 1;
        } else {
            $prefijo = $config->prefijo_factura ?? 'FAC';
            $longitud = (int)($config->longitud_numero ?? 6);
            
            $consecutivo = DB::transaction(function () use ($config) {
                $actual = $config->consecutivo_actual ?? 1;
                $nuevoConsecutivo = $actual + 1;
                DB::table('configuraciones')->update([
                    'consecutivo_actual' => $nuevoConsecutivo,
                    'updated_at' => now()
                ]);
                return $actual;
            });
        }
        
        // Formatear el número con ceros a la izquierda
        $numeroFormateado = str_pad($consecutivo, $longitud, '0', STR_PAD_LEFT);
        
        return $prefijo . '-' . $numeroFormateado;
    }
    
    /**
     * Obtener el próximo número de factura (sin incrementar)
     */
    public static function getProximoNumeroFactura()
    {
        $config = DB::table('configuraciones')->first();
        
        if (!$config) {
            $prefijo = 'FAC';
            $longitud = 6;
            $consecutivo = 1;
        } else {
            $prefijo = $config->prefijo_factura ?? 'FAC';
            $longitud = (int)($config->longitud_numero ?? 6);
            $consecutivo = ($config->consecutivo_actual ?? 1) + 1;
        }
        
        $numeroFormateado = str_pad($consecutivo, $longitud, '0', STR_PAD_LEFT);
        
        return $prefijo . '-' . $numeroFormateado;
    }
    
    /**
     * Calcular IVA según configuración
     */
    public static function calcularIVA($subtotal)
    {
        $config = self::getImpuestosConfig();
        $tasaIVA = $config['iva'] / 100;
        
        if ($config['incluir_iva']) {
            // El subtotal ya incluye IVA
            $iva = $subtotal - ($subtotal / (1 + $tasaIVA));
            $subtotal_sin_iva = $subtotal / (1 + $tasaIVA);
        } else {
            // El subtotal no incluye IVA
            $iva = $subtotal * $tasaIVA;
            $subtotal_sin_iva = $subtotal;
        }
        
        return [
            'subtotal_sin_iva' => round($subtotal_sin_iva, 2),
            'iva' => round($iva, 2),
            'total' => round($subtotal_sin_iva + $iva, 2),
            'tasa_iva' => $config['iva'],
            'incluye_iva' => $config['incluir_iva'],
        ];
    }
    
    /**
     * Verificar si un producto tiene stock bajo según configuración
     */
    public static function verificarStockBajo($stockActual, $stockMinimo = null)
    {
        $config = self::getAlertasConfig();
        
        if (!$config['alertar_stock']) {
            return false;
        }
        
        $limite = $stockMinimo ?? $config['stock_minimo_alerta'];
        
        return $stockActual <= $limite;
    }
    
    /**
     * Verificar si un producto está próximo a vencer según configuración
     */
    public static function verificarVencimientoProximo($fechaVencimiento)
    {
        $config = self::getAlertasConfig();
        
        if (!$config['alertar_vencimiento']) {
            return false;
        }
        
        if (!$fechaVencimiento) {
            return false;
        }
        
        $diasRestantes = now()->diffInDays($fechaVencimiento, false);
        
        return $diasRestantes <= $config['dias_vencimiento'] && $diasRestantes >= 0;
    }
    
    /**
     * Limpiar caché de configuración
     */
    public static function clearCache()
    {
        Cache::forget('configuracion_completa');
        Cache::forget('config_facturacion');
        Cache::forget('config_impuestos');
        Cache::forget('config_alertas');
        Cache::forget('config_general');
        Cache::forget('config_negocio');
    }
}