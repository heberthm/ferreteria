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
        
        // Obtener usuarios
        $usuarios = User::all();
        
        // Roles disponibles
        $rolesDisponibles = ['Administrador', 'Vendedor', 'Almacenista'];
        
        // Pasar configuraciones a la vista
        return view('configuracion', compact('configuracion', 'usuarios', 'rolesDisponibles'));
    }
    
    // Configuración General
    public function guardarGeneral(Request $request)
    {
        $request->validate([
            'nombre_sistema' => 'required|string|max:255',
            'zona_horaria' => 'required|string',
            'formato_fecha' => 'required|string',
            'moneda' => 'required|string',
            'simbolo_moneda' => 'required|string|max:10',
        ]);
        
        DB::table('configuraciones')->updateOrInsert(
            ['id_configuracion' => 1], // Asumiendo que solo hay un registro
            [
                'nombre_sistema' => $request->nombre_sistema,
                'zona_horaria' => $request->zona_horaria,
                'formato_fecha' => $request->formato_fecha,
                'moneda' => $request->moneda,
                'simbolo_moneda' => $request->simbolo_moneda,
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())')
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Configuración general guardada correctamente']);
    }
    
    // Configuración Facturación
    public function guardarFacturacion(Request $request)
    {
        $request->validate([
            'prefijo_factura' => 'required|string|max:10',
            'consecutivo_inicial' => 'required|integer|min:1',
            'consecutivo_actual' => 'required|integer|min:1',
            'longitud_numero' => 'required|integer',
            'formato_factura' => 'required|string',
        ]);
        
        $data = [
            'prefijo_factura' => $request->prefijo_factura,
            'consecutivo_inicial' => $request->consecutivo_inicial,
            'consecutivo_actual' => $request->consecutivo_actual,
            'longitud_numero' => $request->longitud_numero,
            'formato_factura' => $request->formato_factura,
            'autogenerar' => $request->has('autogenerar') ? 1 : 0,
            'validar_duplicados' => $request->has('validar_duplicados') ? 1 : 0,
            'factura_electronica' => $request->has('factura_electronica') ? 1 : 0,
            'tamaño_papel' => $request->tamaño_papel ?? 'thermal',
            'copias' => $request->copias ?? 1,
            'updated_at' => now(),
        ];
        
        DB::table('configuraciones')->updateOrInsert(['id_configuracion' => 1], $data);
        
        // Guardar logo si se subió
        if ($request->hasFile('logo_factura')) {
            $path = $request->file('logo_factura')->store('logos', 'public');
            DB::table('configuraciones')->where('id_configuracion', 1)->update(['logo_negocio' => $path]);
        }
        
        return response()->json(['success' => true, 'message' => 'Configuración de facturación guardada correctamente']);
    }
    
    // Mi Perfil
    public function actualizarPerfil(Request $request)
    {
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
                return response()->json(['success' => false, 'message' => 'Contraseña actual incorrecta'], 422);
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
        
        return response()->json(['success' => true, 'message' => 'Perfil actualizado correctamente']);
    }
    
    // Datos del Negocio
    public function guardarNegocio(Request $request)
    {
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
        
        DB::table('configuraciones')->updateOrInsert(['id_configuracion' => 1], $data);
        
        if ($request->hasFile('logo_negocio')) {
            $path = $request->file('logo_negocio')->store('negocios', 'public');
            DB::table('configuraciones')->where('id_configuracion', 1)->update(['logo_negocio' => $path]);
        }
        
        return response()->json(['success' => true, 'message' => 'Información del negocio guardada correctamente']);
    }
    
    // Impuestos
    public function guardarImpuestos(Request $request)
    {
        $request->validate([
            'iva' => 'required|numeric|min:0|max:100',
        ]);
        
        DB::table('configuraciones')->updateOrInsert(
            ['id_configuracion' => 1],
            [
                'iva' => $request->iva,
                'incluir_iva' => $request->has('incluir_iva') ? 1 : 0,
                'mostrar_iva' => $request->has('mostrar_iva') ? 1 : 0,
                'updated_at' => now(),
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Configuración de impuestos guardada correctamente']);
    }
    
    // Alertas
    public function guardarAlertas(Request $request)
    {
        $request->validate([
            'stock_minimo_alerta' => 'required|integer|min:0',
            'dias_vencimiento' => 'required|integer|min:0',
        ]);
        
        DB::table('configuraciones')->updateOrInsert(
            ['id_configuracion' => 1],
            [
                'stock_minimo_alerta' => $request->stock_minimo_alerta,
                'alertar_stock' => $request->has('alertar_stock') ? 1 : 0,
                'alertar_vencimiento' => $request->has('alertar_vencimiento') ? 1 : 0,
                'dias_vencimiento' => $request->dias_vencimiento,
                'updated_at' => now(),
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Configuración de alertas guardada correctamente']);
    }
    
    // Cargar todas las configuraciones
    public function cargarConfiguraciones()
    {
        $config = DB::table('configuraciones')->first();
        
        // Organizar por grupos para el frontend
        $result = [
            'general' => [
                'nombre_sistema' => $config->nombre_sistema ?? 'Sistema Ferretero',
                'zona_horaria' => $config->zona_horaria ?? 'America/Bogota',
                'formato_fecha' => $config->formato_fecha ?? 'd/m/Y',
                'moneda' => $config->moneda ?? 'COP',
                'simbolo_moneda' => $config->simbolo_moneda ?? '$',
            ],
            'facturacion' => [
                'prefijo_factura' => $config->prefijo_factura ?? 'FAC',
                'consecutivo_inicial' => $config->consecutivo_inicial ?? 1,
                'consecutivo_actual' => $config->consecutivo_actual ?? 1,
                'longitud_numero' => $config->longitud_numero ?? 6,
                'formato_factura' => $config->formato_factura ?? 'simple',
                'autogenerar' => $config->autogenerar ?? 1,
                'validar_duplicados' => $config->validar_duplicados ?? 1,
                'factura_electronica' => $config->factura_electronica ?? 0,
                'tamaño_papel' => $config->tamaño_papel ?? 'thermal',
                'copias' => $config->copias ?? 1,
            ],
            'negocio' => [
                'nombre_negocio' => $config->nombre_negocio,
                'nit' => $config->nit,
                'direccion' => $config->direccion,
                'telefono_negocio' => $config->telefono_negocio,
                'email_negocio' => $config->email_negocio,
                'website' => $config->website,
                'mensaje_factura' => $config->mensaje_factura,
                'logo_negocio' => $config->logo_negocio,
            ],
            'impuestos' => [
                'iva' => $config->iva ?? 19,
                'incluir_iva' => $config->incluir_iva ?? 1,
                'mostrar_iva' => $config->mostrar_iva ?? 1,
            ],
            'alertas' => [
                'stock_minimo_alerta' => $config->stock_minimo_alerta ?? 5,
                'alertar_stock' => $config->alertar_stock ?? 1,
                'alertar_vencimiento' => $config->alertar_vencimiento ?? 0,
                'dias_vencimiento' => $config->dias_vencimiento ?? 30,
            ],
        ];
        
        return response()->json($result);
    }
    
    // Cargar configuración de facturación específicamente
    public function cargarFacturacion()
    {
        $config = DB::table('configuraciones')->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'prefijo_factura' => $config->prefijo_factura ?? 'FAC',
                'consecutivo_inicial' => $config->consecutivo_inicial ?? 1,
                'consecutivo_actual' => $config->consecutivo_actual ?? 1,
                'longitud_numero' => $config->longitud_numero ?? 6,
                'formato_factura' => $config->formato_factura ?? 'simple',
                'autogenerar' => (string)($config->autogenerar ?? 1),
                'validar_duplicados' => (string)($config->validar_duplicados ?? 1),
                'factura_electronica' => (string)($config->factura_electronica ?? 0),
                'tamaño_papel' => $config->tamaño_papel ?? 'thermal',
                'copias' => $config->copias ?? 1,
            ]
        ]);
    }
    
    // Obtener próximo número de factura
    public function getProximoNumeroFactura()
    {
        $config = DB::table('configuraciones')->first();
        $consecutivo = ($config->consecutivo_actual ?? 1) + 1;
        $longitud = $config->longitud_numero ?? 6;
        
        return response()->json([
            'success' => true,
            'proximo_numero' => str_pad($consecutivo, $longitud, '0', STR_PAD_LEFT)
        ]);
    }
    
    // Obtener consecutivo actual
    public function obtenerConsecutivo()
    {
        $config = DB::table('configuraciones')->first();
        
        $consecutivo = $config->consecutivo_actual ?? 1;
        $prefijo = $config->prefijo_factura ?? 'FAC';
        $longitud = $config->longitud_numero ?? 6;
        
        $numeroFormateado = $prefijo . str_pad($consecutivo, $longitud, '0', STR_PAD_LEFT);
        
        return response()->json([
            'success' => true,
            'consecutivo' => $consecutivo,
            'numero_formateado' => $numeroFormateado
        ]);
    }
    
    // Incrementar consecutivo
    public function incrementarConsecutivo()
    {
        $config = DB::table('configuraciones')->first();
        $nuevoConsecutivo = ($config->consecutivo_actual ?? 1) + 1;
        
        DB::table('configuraciones')
            ->where('id_configuracion', 1)
            ->update(['consecutivo_actual' => $nuevoConsecutivo]);
        
        return response()->json([
            'success' => true,
            'consecutivo_nuevo' => $nuevoConsecutivo
        ]);
    }
    
    // Reiniciar consecutivo
    public function reiniciarConsecutivo()
    {
        DB::table('configuraciones')
            ->where('id_configuracion', 1)
            ->update(['consecutivo_actual' => 1]);
            
        return response()->json(['success' => true, 'message' => 'Consecutivo reiniciado correctamente']);
    }
    
    // Usuarios - Guardar nuevo usuario
    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string|max:255|unique:users,name',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'rol' => 'required|string',
            'password' => 'required|min:6',
        ]);
        
        $user = User::create([
            'name' => $request->usuario,
            'email' => $request->email,
            'rol' => $request->rol,
            'password' => Hash::make($request->password),
        ]);
        
        return response()->json(['success' => true, 'message' => 'Usuario creado correctamente', 'user' => $user]);
    }
    
    // Actualizar usuario
    public function actualizarUsuario(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'usuario' => 'required|string|max:255|unique:users,name,'.$id,
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'rol' => 'required|string',
        ]);
        
        $user->name = $request->usuario;
        $user->email = $request->email;
        $user->rol = $request->rol;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    }
    
    // Eliminar usuario
    public function eliminarUsuario($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propio usuario'], 422);
        }
        
        $user->delete();
        
        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    }
    
    // Listar usuarios con filtros
    public function listarUsuarios(Request $request)
    {
        $query = User::query();
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }
        
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }
        
        $orden = $request->get('orden', 'name');
        $direccion = $request->get('direccion', 'asc');
        
        if (in_array($orden, ['name', 'email', 'rol', 'created_at'])) {
            $query->orderBy($orden, $direccion === 'desc' ? 'desc' : 'asc');
        }
        
        $usuarios = $query->get(['id', 'name', 'email', 'rol', 'created_at']);
        
        return response()->json([
            'success' => true,
            'usuarios' => $usuarios,
            'total' => $usuarios->count(),
        ]);
    }
    
    // Crear backup
    public function crearBackup(Request $request)
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            $path = storage_path("app/backups/{$filename}");
            
            if (!is_dir(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                env('DB_HOST'),
                env('DB_DATABASE'),
                $path
            );
            
            exec($command);
            
            return response()->json(['success' => true, 'message' => 'Respaldo creado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el respaldo'], 500);
        }
    }
    
    
    // Listar backups
    public function listarBackups()
    {
        $backups = [];
        $path = storage_path('app/backups');
        
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                    $backups[] = [
                        'name' => $file,
                        'size' => round(filesize($path . '/' . $file) / 1024, 2),
                        'date' => date('Y-m-d H:i:s', filemtime($path . '/' . $file)),
                    ];
                }
            }
        }
        
        return response()->json($backups);
    }
    
    // Cargar lista de roles (opcional)
    public function listarRoles()
    {
        $roles = ['Administrador', 'Vendedor', 'Almacenista'];
        return response()->json($roles);
    }
    
    public function guardarRol(Request $request)
    {
        // Implementar según necesidades
        return response()->json(['success' => true, 'message' => 'Rol guardado correctamente']);
    }
    
    public function actualizarRol(Request $request, $id)
    {
        return response()->json(['success' => true, 'message' => 'Rol actualizado correctamente']);
    }
    
    public function eliminarRol($id)
    {
        return response()->json(['success' => true, 'message' => 'Rol eliminado correctamente']);
    }
    
    public function getConfiguracion()
    {
        $config = DB::table('configuraciones')->first();
        return response()->json($config);
    }
}