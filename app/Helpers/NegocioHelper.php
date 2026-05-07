<?php

namespace App\Helpers;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NegocioHelper
{
    /**
     * Obtener todos los datos de la empresa
     */
    public static function getDatosEmpresa()
    {
        try {
            return Cache::remember('datos_empresa', 3600, function () {
                // Obtener la configuración (solo hay un registro)
                $config = Configuracion::first();
                
                if ($config) {
                    return [
                        'nombre' => $config->nombre_negocio ?? 'SUPERMERCADO XYZ',
                        'nit' => $config->nit ?? '123456789-0',
                        'telefono' => $config->telefono_negocio ?? '(601) 123-4567',
                        'direccion' => $config->direccion ?? 'Calle 123 #45-67',
                        'email' => $config->email_negocio ?? 'info@superxyz.com',
                        'mensaje_factura' => $config->mensaje_factura ?? '¡Gracias por su compra!',
                        'logo_url' => self::getLogoUrl($config)
                    ];
                }
                
                // Valores por defecto si no hay configuración
                return [
                    'nombre' => 'SUPERMERCADO XYZ',
                    'nit' => '123456789-0',
                    'telefono' => '(601) 123-4567',
                    'direccion' => 'Calle 123 #45-67',
                    'email' => 'info@superxyz.com',
                    'mensaje_factura' => '¡Gracias por su compra!',
                    'logo_url' => null
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error al obtener datos empresa: ' . $e->getMessage());
            
            return [
                'nombre' => 'SUPERMERCADO XYZ',
                'nit' => '123456789-0',
                'telefono' => '(601) 123-4567',
                'direccion' => 'Calle 123 #45-67',
                'email' => 'info@superxyz.com',
                'mensaje_factura' => '¡Gracias por su compra!',
                'logo_url' => null
            ];
        }
    }
    
    /**
     * Obtener la URL del logo
     */
    public static function getLogoUrl($config = null)
    {
        try {
            if (!$config) {
                $config = Configuracion::first();
            }
            
            $logo = $config ? $config->logo_negocio : null;
            
            if ($logo && !empty($logo) && file_exists(storage_path('app/public/' . $logo))) {
                return asset('storage/' . $logo);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error al obtener logo: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Limpiar caché
     */
    public static function clearCache()
    {
        Cache::forget('datos_empresa');
    }
    
    /**
     * Generar HTML del ticket con datos de la empresa
     */
    public static function generarTicketHTML($venta, $configFacturacion = [])
    {
        $empresa = self::getDatosEmpresa();
        
        $html = '<div style="width: 80mm; font-family: monospace; font-size: 12px;">';
        $html .= '<div style="text-align: center; border-bottom: 1px dashed #000;">';
        
        if ($empresa['logo_url']) {
            $html .= '<img src="' . $empresa['logo_url'] . '" style="max-height: 40px;">';
        }
        
        $html .= '<h3>' . $empresa['nombre'] . '</h3>';
        $html .= '<p>NIT: ' . $empresa['nit'] . '</p>';
        $html .= '<p>' . $empresa['direccion'] . '</p>';
        $html .= '<p>Tel: ' . $empresa['telefono'] . '</p>';
        $html .= '</div>';
        
        // Resto del ticket...
        $html .= '<div style="text-align: center; margin-top: 10px;">';
        $html .= '<p>' . $empresa['mensaje_factura'] . '</p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Generar HTML de la factura con datos de la empresa
     */
    public static function generarFacturaHTML($venta)
    {
        $empresa = self::getDatosEmpresa();
        
        $html = '<div style="font-family: Arial; max-width: 800px; margin: 0 auto;">';
        $html .= '<div style="text-align: center; border-bottom: 2px solid #000;">';
        
        if ($empresa['logo_url']) {
            $html .= '<img src="' . $empresa['logo_url'] . '" style="max-height: 80px;">';
        }
        
        $html .= '<h1>' . $empresa['nombre'] . '</h1>';
        $html .= '<p>NIT: ' . $empresa['nit'] . ' | Tel: ' . $empresa['telefono'] . '</p>';
        $html .= '<p>' . $empresa['direccion'] . '</p>';
        if ($empresa['email']) {
            $html .= '<p>Email: ' . $empresa['email'] . '</p>';
        }
        $html .= '</div>';
        
        // Resto de la factura...
        $html .= '<div style="margin-top: 30px; text-align: center;">';
        $html .= '<p><strong>' . $empresa['mensaje_factura'] . '</strong></p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
}