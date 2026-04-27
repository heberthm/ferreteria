<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar la página de configuración
     */
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
        
        // Obtener roles (si existe la tabla)
        $roles = [];
        if (Schema::hasTable('roles')) {
            $roles = Role::all();
        }
        
        // Pasar variables a la vista
        return view('configuracion', compact('configuracion', 'usuarios', 'roles'));
    }

    /**
     * Crear configuración por defecto si no existe
     */
    private function crearConfiguracionPorDefecto()
    {
        $configData = [
            'id_configuracion' => 1,
            'nombre_sistema' => 'Sistema Ferretero',
            'version' => '1.0.0',
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

    /**
     * Obtener el usuario actual (API)
     */
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
            
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telefono' => $user->telefono ?? '',
                'avatar' => $user->avatar ?? null,
                'rol' => $user->rol ?? 'Usuario'
            ];
            
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
     * Cargar todas las configuraciones (API)
     */
    public function cargarConfiguraciones()
    {
        try {
            $config = DB::table('configuraciones')->first();
            
            if (!$config) {
                $config = $this->crearConfiguracionPorDefecto();
            }
            
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
            
        } catch (\Exception $e) {
            \Log::error('Error en cargarConfiguraciones: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar configuraciones'
            ], 500);
        }
    }

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
            
            $data = [
                'nombre_sistema' => $request->nombre_sistema,
                'zona_horaria' => $request->zona_horaria,
                'formato_fecha' => $request->formato_fecha,
                'moneda' => $request->moneda,
                'simbolo_moneda' => $request->simbolo_moneda,
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
 * Listar todos los usuarios (para AJAX)
 */
public function listarUsuarios()
{
    try {
        $usuarios = User::all();
        
        return response()->json([
            'success' => true,
            'usuarios' => $usuarios
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al listar usuarios'
        ], 500);
    }
}

/**
 * Listar todos los roles (para AJAX)
 */
public function listarRoles()
{
    try {
        $roles = Role::all();
        
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al listar roles'
        ], 500);
    }
}

/**
 * Actualizar usuario
 */
public function actualizarUsuario(Request $request, $id)
{
    try {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'rol' => 'required|string'
        ]);
        
        $user->name = $request->nombre;
        $user->email = $request->email;
        $user->rol = $request->rol;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar usuario: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Eliminar usuario
 */
public function eliminarUsuario($id)
{
    try {
        $user = User::findOrFail($id);
        
        // No permitir eliminar el propio usuario
        if ($user->id == auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 422);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar usuario'
        ], 500);
    }
}

/**
 * Guardar nuevo rol
 */
public function guardarRol(Request $request)
{
    try {
        $request->validate([
            'nombre_rol' => 'required|string|max:255|unique:roles,name',
            'descripcion' => 'nullable|string'
        ]);
        
        $rol = Role::create([
            'name' => $request->nombre_rol,
            'description' => $request->descripcion
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Rol creado correctamente',
            'rol' => $rol
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar rol: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Actualizar rol
 */
public function actualizarRol(Request $request, $id)
{
    try {
        $rol = Role::findOrFail($id);
        
        // No permitir editar el rol de Administrador si es el único
        if ($rol->name == 'Administrador' && $request->nombre_rol != 'Administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cambiar el nombre del rol Administrador'
            ], 422);
        }
        
        $request->validate([
            'nombre_rol' => 'required|string|max:255|unique:roles,name,' . $id,
            'descripcion' => 'nullable|string'
        ]);
        
        $rol->name = $request->nombre_rol;
        $rol->description = $request->descripcion;
        $rol->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar rol: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Eliminar rol
 */
public function eliminarRol($id)
{
    try {
        // ✅ buscar por la PK correcta
        $rol = Role::where('id_rol', $id)->firstOrFail();

        $rolesProtegidos = ['Administrador', 'Vendedor', 'Almacenista'];
        if (in_array($rol->name, $rolesProtegidos)) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un rol del sistema'
            ], 422);
        }

        if ($rol->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el rol porque tiene usuarios asignados'
            ], 422);
        }

        $rol->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado correctamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar rol: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Obtener permisos de un rol
 */
public function getPermisosRol($id)
{
    try {
        $rol = Role::where('id_rol', $id)->firstOrFail();

        // Si guardas permisos como JSON en columna 'permisos'
        $permisos = $rol->permisos ? json_decode($rol->permisos, true) : [];

        return response()->json([
            'success' => true,
            'permisos' => $permisos
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al obtener permisos: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al obtener permisos: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Guardar permisos de un rol
 */
public function guardarPermisosRol(Request $request, $id)
{
    try {
        $rol = Role::where('id_rol', $id)->firstOrFail();

        // Guardar permisos como JSON en columna 'permisos'
        $rol->permisos = json_encode($request->permisos ?? []);
        $rol->save();

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados correctamente'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al guardar permisos: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al guardar permisos: ' . $e->getMessage()
        ], 500);
    }
}

    
   /**
 * Actualizar perfil de usuario (sin contraseña actual)
 */
public function actualizarPerfil(Request $request)
{
    try {
        $user = auth()->user();
        
        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:35',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Eliminamos la validación de password_actual
            'password_nueva' => 'nullable|min:6|same:password_confirmacion',
            'password_confirmacion' => 'nullable|min:6',
        ]);
        
        $user->email = $request->email;
        $user->name = $request->nombre_completo;
        $user->telefono = $request->telefono;
       
        
        // Cambiar contraseña - SIN validar contraseña actual
        if ($request->filled('password_nueva') && $request->filled('password_confirmacion')) {
            // Validar que las contraseñas coincidan
            if ($request->password_nueva !== $request->password_confirmacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las contraseñas no coinciden'
                ], 422);
            }
            
            // Validar longitud mínima
            if (strlen($request->password_nueva) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña debe tener al menos 6 caracteres'
                ], 422);
            }
            
            $user->password = Hash::make($request->password_nueva);
        }
        
        // Guardar avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
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

    /**
     * Eliminar avatar del usuario
     */
    public function eliminarAvatar(Request $request)
    {
        try {
            $user = auth()->user();
            
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
                $user->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al eliminar avatar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el avatar'
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
     * Guardar usuario
     */
    public function guardarUsuario(Request $request)
    {
        try {
            $request->validate([
                'usuario' => 'required|string|max:255|unique:users,name',
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);
            
            $user = User::create([
                'name' => $request->usuario,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol ?? 'Usuario',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al guardar usuario: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear respaldo de la base de datos
     */
    public function crearBackup(Request $request)
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $path = $backupPath . '/' . $filename;
            
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
     * Listar respaldos
     */
    public function listarBackups()
    {
        $backups = [];
        $path = storage_path('app/backups');
        
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                    $filePath = $path . '/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => round(filesize($filePath) / 1024, 2),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                    ];
                }
            }
            
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        return response()->json($backups);
    }

    /**
     * Descargar respaldo
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
     * Reiniciar consecutivo
     */
    public function reiniciarConsecutivo(Request $request)
    {
        try {
            DB::table('configuraciones')->update([
                'consecutivo_actual' => 1,
                'updated_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Consecutivo reiniciado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reiniciar el consecutivo'
            ], 500);
        }
    }
}