<?php
// app/Http/Controllers/ConfiguracionController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        // Obtener la configuración (solo hay un registro)
        $configuracion = DB::table('configuraciones')->first();
        
        // Si no existe configuración, crear una por defecto
        if (!$configuracion) {
            $configuracion = $this->crearConfiguracionPorDefecto();
        }
        
        // Obtener usuarios
        $usuarios = User::all();
        
        // Pasar configuraciones a la vista
        return view('configuracion', compact('configuracion', 'usuarios'));
    }

    // app/Http/Controllers/ConfiguracionController.php

  public function usuarioActual()
{
    try {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }
        
        // Datos básicos del usuario
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
        
        // Agregar teléfono si existe la columna
        if (Schema::hasColumn('users', 'telefono')) {
            $userData['telefono'] = $user->telefono ?? '';
        } else {
            $userData['telefono'] = '';
        }
        
        // Agregar avatar si existe la columna
        if (Schema::hasColumn('users', 'avatar')) {
            $userData['avatar'] = $user->avatar ?? null;
        } else {
            $userData['avatar'] = null;
        }
        
        // Agregar rol si existe la columna
        if (Schema::hasColumn('users', 'rol')) {
            $userData['rol'] = $user->rol ?? 'Usuario';
        } else {
            $userData['rol'] = 'Usuario';
        }
        
        return response()->json([
            'success' => true,
            'user' => $userData
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error en usuarioActual: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener datos del usuario: ' . $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Crear configuración por defecto si no existe
     */
    private function crearConfiguracionPorDefecto()
    {
        $configData = [
            'id_configuracion' => 1,
            'nombre_sistema' => 'Sistema Ferretero',
            'zona_horaria' => 'America/Bogota',
            'formato_fecha' => 'd/m/Y',
            'moneda' => 'COP',
            'simbolo_moneda' => '$',
            'prefijo_factura' => 'FAC',
            'consecutivo_inicial' => 1,
            'consecutivo_actual' => 1,
            'longitud_numero' => 6,
            'formato_factura' => 'simple',
            'autogenerar' => 1,
            'validar_duplicados' => 1,
            'factura_electronica' => 0,
            'tamaño_papel' => 'thermal',
            'copias' => 1,
            'nombre_negocio' => 'Mi Negocio',
            'iva' => 19,
            'incluir_iva' => 1,
            'mostrar_iva' => 1,
            'stock_minimo_alerta' => 5,
            'alertar_stock' => 1,
            'alertar_vencimiento' => 0,
            'dias_vencimiento' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        DB::table('configuraciones')->insert($configData);
        
        return (object) $configData;
    }

    // app/Http/Controllers/ConfiguracionController.php

/**
 * Guardar configuración general
 */
    public function guardarGeneral(Request $request)
    {
        try {
            $request->validate([
                'nombre_sistema' => 'required|string|max:255',
                'zona_horaria' => 'required|string',
                'formato_fecha' => 'required|string',
                'moneda' => 'required|string',
                'simbolo_moneda' => 'required|string|max:10',
            ]);
            
            // Verificar si existe la tabla configuraciones
            if (!Schema::hasTable('configuraciones')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'La tabla configuraciones no existe. Ejecute las migraciones primero.'
                ], 500);
            }
            
            // Verificar si existe un registro
            $existe = DB::table('configuraciones')->exists();
            
            if ($existe) {
                // Actualizar registro existente
                DB::table('configuraciones')->update([
                    'nombre_sistema' => $request->nombre_sistema,
                    'zona_horaria' => $request->zona_horaria,
                    'formato_fecha' => $request->formato_fecha,
                    'moneda' => $request->moneda,
                    'simbolo_moneda' => $request->simbolo_moneda,
                    'updated_at' => now(),
                ]);
            } else {
                // Crear nuevo registro
                DB::table('configuraciones')->insert([
                    'id_configuracion' => 1,
                    'nombre_sistema' => $request->nombre_sistema,
                    'zona_horaria' => $request->zona_horaria,
                    'formato_fecha' => $request->formato_fecha,
                    'moneda' => $request->moneda,
                    'simbolo_moneda' => $request->simbolo_moneda,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Configuración general guardada correctamente'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al guardar configuración general: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Guardar configuración de facturación
 */
public function guardarFacturacion(Request $request)
{
    try {
        $request->validate([
            'prefijo_factura' => 'required|string|max:10',
            'consecutivo_inicial' => 'required|integer|min:1',
            'longitud_numero' => 'required|integer|min:1|max:10',
            'formato_factura' => 'required|string',
        ]);
        
        $data = [
            'prefijo_factura' => $request->prefijo_factura,
            'consecutivo_inicial' => $request->consecutivo_inicial,
            'consecutivo_actual' => $request->consecutivo_actual ?? $request->consecutivo_inicial,
            'longitud_numero' => $request->longitud_numero,
            'formato_factura' => $request->formato_factura,
            'autogenerar' => $request->has('autogenerar') ? 1 : 0,
            'validar_duplicados' => $request->has('validar_duplicados') ? 1 : 0,
            'factura_electronica' => $request->has('factura_electronica') ? 1 : 0,
            'tamaño_papel' => $request->tamaño_papel ?? 'thermal',
            'copias' => $request->copias ?? 1,
            'updated_at' => now(),
        ];
        
        $existe = DB::table('configuraciones')->exists();
        
        if ($existe) {
            DB::table('configuraciones')->update($data);
        } else {
            $data['id_configuracion'] = 1;
            $data['created_at'] = now();
            DB::table('configuraciones')->insert($data);
        }
        
        // Guardar logo si se subió
        if ($request->hasFile('logo_factura')) {
            $path = $request->file('logo_factura')->store('logos', 'public');
            DB::table('configuraciones')->update(['logo_negocio' => $path]);
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Configuración de facturación guardada correctamente'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al guardar facturación: ' . $e->getMessage());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error al guardar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Guardar datos del negocio
 */
public function guardarNegocio(Request $request)
{
    try {
        $request->validate([
            'nombre_negocio' => 'required|string|max:255',
            'nit' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'telefono_negocio' => 'nullable|string|max:20',
            'email_negocio' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);
        
        $data = [
            'nombre_negocio' => $request->nombre_negocio,
            'nit' => $request->nit,
            'direccion' => $request->direccion,
            'telefono_negocio' => $request->telefono_negocio,
            'email_negocio' => $request->email_negocio,
            'website' => $request->website,
            'mensaje_factura' => $request->mensaje_factura,
            'updated_at' => now(),
        ];
        
        $existe = DB::table('configuraciones')->exists();
        
        if ($existe) {
            DB::table('configuraciones')->update($data);
        } else {
            $data['id_configuracion'] = 1;
            $data['created_at'] = now();
            DB::table('configuraciones')->insert($data);
        }
        
        if ($request->hasFile('logo_negocio')) {
            $path = $request->file('logo_negocio')->store('negocios', 'public');
            DB::table('configuraciones')->update(['logo_negocio' => $path]);
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Información del negocio guardada correctamente'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al guardar negocio: ' . $e->getMessage());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error al guardar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Guardar configuración de impuestos
 */
public function guardarImpuestos(Request $request)
{
    try {
        $request->validate([
            'iva' => 'required|numeric|min:0|max:100',
        ]);
        
        $data = [
            'iva' => $request->iva,
            'incluir_iva' => $request->has('incluir_iva') ? 1 : 0,
            'mostrar_iva' => $request->has('mostrar_iva') ? 1 : 0,
            'updated_at' => now(),
        ];
        
        $existe = DB::table('configuraciones')->exists();
        
        if ($existe) {
            DB::table('configuraciones')->update($data);
        } else {
            $data['id_configuracion'] = 1;
            $data['created_at'] = now();
            DB::table('configuraciones')->insert($data);
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Configuración de impuestos guardada correctamente'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al guardar impuestos: ' . $e->getMessage());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error al guardar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Guardar configuración de alertas
 */
public function guardarAlertas(Request $request)
{
    try {
        $request->validate([
            'stock_minimo_alerta' => 'required|integer|min:0',
            'dias_vencimiento' => 'required|integer|min:0',
        ]);
        
        $data = [
            'stock_minimo_alerta' => $request->stock_minimo_alerta,
            'alertar_stock' => $request->has('alertar_stock') ? 1 : 0,
            'alertar_vencimiento' => $request->has('alertar_vencimiento') ? 1 : 0,
            'dias_vencimiento' => $request->dias_vencimiento,
            'updated_at' => now(),
        ];
        
        $existe = DB::table('configuraciones')->exists();
        
        if ($existe) {
            DB::table('configuraciones')->update($data);
        } else {
            $data['id_configuracion'] = 1;
            $data['created_at'] = now();
            DB::table('configuraciones')->insert($data);
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Configuración de alertas guardada correctamente'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al guardar alertas: ' . $e->getMessage());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error al guardar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Actualizar perfil de usuario
 */
    public function actualizarPerfil(Request $request)
    {
        try {
            $user = auth()->user();
            
            $request->validate([
                'email' => 'required|email|unique:users,email,'.$user->id,
                'nombre_completo' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:20',
            ]);
            
            $user->email = $request->email;
            $user->name = $request->nombre_completo;
            
            // Agregar teléfono si existe la columna
            if (Schema::hasColumn('users', 'telefono')) {
                $user->telefono = $request->telefono;
            }
            
            // Cambiar contraseña
            if ($request->filled('password_actual') && $request->filled('password_nueva')) {
                if (!Hash::check($request->password_actual, $user->password)) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Contraseña actual incorrecta'
                    ], 422);
                }
                
                $request->validate([
                    'password_nueva' => 'required|min:6|same:password_confirmacion',
                ]);
                
                $user->password = Hash::make($request->password_nueva);
            }
            
            // Guardar avatar
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }
            
            $user->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'Perfil actualizado correctamente'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al actualizar perfil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }
    

    
    // Cargar todas las configuraciones (CORREGIDO)
    public function cargarConfiguraciones()
    {
        $config = DB::table('configuraciones')->first();
        
        // Si no hay configuración, crear una
        if (!$config) {
            $config = $this->crearConfiguracionPorDefecto();
        }
        
        // Organizar por grupos para el frontend
        $result = [
            'general' => [
                'nombre_sistema' => $config->nombre_sistema ?? 'Sistema Ferretero',
                'version' => $config->version ?? '1.0.0',
                'zona_horaria' => $config->zona_horaria ?? 'America/Bogota',
                'formato_fecha' => $config->formato_fecha ?? 'd/m/Y',
                'moneda' => $config->moneda ?? 'COP',
                'simbolo_moneda' => $config->simbolo_moneda ?? '$',
            ],
            'facturacion' => [
                'prefijo_factura' => $config->prefijo_factura ?? 'FAC',
                'consecutivo_inicial' => (int)($config->consecutivo_inicial ?? 1),
                'consecutivo_actual' => (int)($config->consecutivo_actual ?? 1),
                'proximo_numero' => (int)($config->consecutivo_actual ?? 1) + 1,
                'longitud_numero' => (int)($config->longitud_numero ?? 6),
                'formato_factura' => $config->formato_factura ?? 'simple',
                'autogenerar' => (string)($config->autogenerar ?? 1),
                'validar_duplicados' => (string)($config->validar_duplicados ?? 1),
                'factura_electronica' => (string)($config->factura_electronica ?? 0),
                'tamaño_papel' => $config->tamaño_papel ?? 'thermal',
                'copias' => (int)($config->copias ?? 1),
            ],
            'negocio' => [
                'nombre_negocio' => $config->nombre_negocio ?? 'Mi Negocio',
                'nit' => $config->nit ?? '',
                'direccion' => $config->direccion ?? '',
                'telefono_negocio' => $config->telefono_negocio ?? '',
                'email_negocio' => $config->email_negocio ?? '',
                'website' => $config->website ?? '',
                'mensaje_factura' => $config->mensaje_factura ?? 'Gracias por su compra',
                'logo_negocio' => $config->logo_negocio ?? null,
            ],
            'impuestos' => [
                'iva' => (float)($config->iva ?? 19),
                'incluir_iva' => (string)($config->incluir_iva ?? 1),
                'mostrar_iva' => (string)($config->mostrar_iva ?? 1),
            ],
            'alertas' => [
                'stock_minimo_alerta' => (int)($config->stock_minimo_alerta ?? 5),
                'alertar_stock' => (string)($config->alertar_stock ?? 1),
                'alertar_vencimiento' => (string)($config->alertar_vencimiento ?? 0),
                'dias_vencimiento' => (int)($config->dias_vencimiento ?? 30),
            ],
        ];
        
        return response()->json($result);
    }

    // app/Http/Controllers/ConfiguracionController.php



/**
 * Crear un respaldo de la base de datos
 */
    public function crearBackup(Request $request)
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            
            // Crear directorio si no existe
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $path = $backupPath . '/' . $filename;
            
            // Construir comando mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg(env('DB_USERNAME')),
                escapeshellarg(env('DB_PASSWORD')),
                escapeshellarg(env('DB_HOST')),
                escapeshellarg(env('DB_DATABASE')),
                escapeshellarg($path)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception("Error al ejecutar mysqldump: " . implode("\n", $output));
            }
            
            if (!file_exists($path) || filesize($path) === 0) {
                throw new \Exception("El archivo de respaldo está vacío o no se creó correctamente");
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Respaldo creado exitosamente',
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al crear backup: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear el respaldo: ' . $e->getMessage()
            ], 500);
        }
    }

 /**
 * Descargar un respaldo específico
 */
    public function descargarBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }
   
/**
 * Listar los respaldos disponibles
 */
    public function listarBackups()
    {
        $backups = [];
        $path = storage_path('app/backups');
        
        // Verificar si el directorio existe
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                // Solo archivos .sql
                if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                    $filePath = $path . '/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => round(filesize($filePath) / 1024, 2),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                    ];
                }
            }
            
            // Ordenar por fecha (más reciente primero)
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        return response()->json($backups);
    }

}