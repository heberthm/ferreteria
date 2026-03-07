<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getDashboardData(Request $request)
    {
        try {
            Log::info('🔍 Dashboard: Iniciando getDashboardData');
            
            $periodo = $request->get('periodo', 'mes');
            $hoy = Carbon::today();
            
            // 1. ESTADÍSTICAS BÁSICAS
            Log::info('Dashboard: Consultando estadísticas básicas');
            
            $ventasHoy = DB::table('ventas')
                ->whereDate('fecha_venta', $hoy)
                ->count();
            
            $ingresosHoy = DB::table('ventas')
                ->whereDate('fecha_venta', $hoy)
                ->sum('total') ?? 0;
            
            $promedioVenta = $ventasHoy > 0 ? $ingresosHoy / $ventasHoy : 0;
            
            Log::info('Dashboard: Estadísticas OK', [
                'ventas' => $ventasHoy,
                'ingresos' => $ingresosHoy
            ]);
            
            // 2. stock_actual BAJO
            Log::info('Dashboard: Consultando stock_actual bajo');
            
            $stock_actualBajo = DB::table('productos')
                ->whereRaw('stock_actual <= stock_minimo')
                ->orWhere('stock_actual', '<=', 2)
                ->orderBy('stock_actual', 'asc')
                ->limit(10)
                ->get(['id_producto', 'nombre', 'codigo', 'stock_actual', 'stock_minimo'])
                ->map(function($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->codigo ?? '',
                        'stock_actual_actual' => $producto->stock_actual ?? 0,
                        'stock_minimo' => $producto->stock_minimo ?? 5,
                        'estado' => ($producto->stock_actual ?? 0) <= 2 ? 'CRÍTICO' : 'BAJO'
                    ];
                });
            
            Log::info('Dashboard: stock_actual bajo OK', ['cantidad' => $stock_actualBajo->count()]);
            
            // 3. PRODUCTOS MÁS VENDIDOS
            Log::info('Dashboard: Consultando productos vendidos');
            
            $fechaInicio = $this->getFechaInicio($periodo);
            
            $productosVendidos = DB::table('detalle_ventas')
                ->select(
                    'productos.nombre',
                    'productos.codigo',
                    DB::raw('COALESCE(SUM(detalle_ventas.cantidad), 0) as total_cantidad'),
                    DB::raw('COALESCE(SUM(detalle_ventas.subtotal), 0) as total_vendido')
                )
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->where('ventas.fecha_venta', '>=', $fechaInicio)
                ->groupBy('productos.id_producto', 'productos.nombre', 'productos.codigo')
                ->orderByDesc('total_cantidad')
                ->limit(5)
                ->get();
            
            Log::info('Dashboard: Productos vendidos OK', ['cantidad' => $productosVendidos->count()]);
            
            // Calcular porcentajes
            $totalVendido = $productosVendidos->sum('total_vendido');
            $productosVendidos = $productosVendidos->map(function($item) use ($totalVendido) {
                $item->porcentaje = $totalVendido > 0 ? ($item->total_vendido / $totalVendido) * 100 : 0;
                return $item;
            });
            
            // 4. VENTAS RECIENTES
            Log::info('Dashboard: Consultando ventas recientes');
            
            $ventasRecientes = DB::table('ventas')
                ->orderByDesc('id_venta')
                ->limit(10)
                ->get()
                ->map(function($venta) {
                    // Obtener nombre del cliente
                    $nombreCliente = 'Cliente General';
                    if ($venta->id_cliente) {
                        $cliente = DB::table('clientes')
                            ->where('id_cliente', $venta->id_cliente)
                            ->first();
                        if ($cliente) {
                            $nombreCliente = $cliente->nombre;
                        }
                    }
                    
                    // Contar productos desde detalle_ventas
                    $totalProductos = DB::table('detalle_ventas')
                        ->where('id_venta', $venta->id_venta)
                        ->sum('cantidad') ?? 0;
                    
                    return [
                        'id' => $venta->id_venta,
                        'numero_factura' => $venta->numero_factura ?? 'F-' . str_pad($venta->id_venta, 6, '0', STR_PAD_LEFT),
                        'fecha_venta' => $venta->fecha_venta,
                        'cliente' => $nombreCliente,
                        'total_productos' => (int)$totalProductos,
                        'total' => (float)($venta->total ?? 0),
                        'estado' => $venta->estado ?? 'completada',
                        'metodo_pago' => $venta->metodo_pago ?? 'efectivo'
                    ];
                });
            
            Log::info('Dashboard: Ventas recientes OK', ['cantidad' => $ventasRecientes->count()]);
            
            // RESPUESTA FINAL
            Log::info('✅ Dashboard: Todos los datos preparados correctamente');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'estadisticas' => [
                        'ventas_hoy' => (int)$ventasHoy,
                        'ingresos_hoy' => (float)$ingresosHoy,
                        'promedio_venta' => (float)round($promedioVenta, 2),
                        'productos_stock_actual_bajo' => $stock_actualBajo->count()
                    ],
                    'stock_actual_bajo' => $stock_actualBajo,
                    'productos_vendidos' => $productosVendidos,
                    'ventas_recientes' => $ventasRecientes,
                    'periodo' => $periodo
                ],
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error en getDashboardData');
            Log::error('Mensaje: ' . $e->getMessage());
            Log::error('Línea: ' . $e->getLine());
            Log::error('Archivo: ' . $e->getFile());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos del dashboard',
                'error' => $e->getMessage(),   
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
    
    /**
     * Obtener fecha de inicio según el período
     */
    private function getFechaInicio($periodo)
    {
        switch ($periodo) {
            case 'hoy':
                return Carbon::today();
            case 'semana':
                return Carbon::now()->startOfWeek();
            case 'mes':
                return Carbon::now()->startOfMonth();
            case 'anio':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

 public function getDetalleVenta($id)
{
    try {
        \Log::info("🔍 Dashboard: Obteniendo detalle de venta ID: $id");
        
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            \Log::error("❌ ID no válido: $id");
            return response()->json([
                'success' => false,
                'message' => 'ID de venta no válido'
            ], 400);
        }
        
        // Obtener información básica de la venta
        $venta = DB::table('ventas')
            ->where('id_venta', (int)$id)
            ->first();
            
        \Log::info("📊 Venta encontrada: " . ($venta ? "Sí" : "No"));
        
        if (!$venta) {
            \Log::warning("⚠️ Venta no encontrada ID: $id");
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada'
            ], 404);
        }
        
        \Log::info("📋 Datos de venta: ", (array)$venta);
        
        // Formatear fecha
        try {
            $fecha = \Carbon\Carbon::parse($venta->fecha_venta ?? now());
        } catch (\Exception $e) {
            \Log::error("❌ Error parseando fecha: " . $e->getMessage());
            $fecha = now();
        }
        
        // Obtener cliente
        $cliente = null;
        if (isset($venta->id_cliente) && $venta->id_cliente) {
            $cliente = DB::table('clientes')
                ->where('id_cliente', $venta->id_cliente)
                ->first();
            \Log::info("👤 Cliente encontrado: " . ($cliente ? "Sí" : "No"));
        }
        
        // Obtener información del usuario que realizó la venta ORIGINAL
        $usuarioVenta = null;
        if (isset($venta->id_usuario) && $venta->id_usuario) {
            try {
                $usuarioVenta = DB::table('usuarios')
                    ->where('id_usuario', $venta->id_usuario)
                    ->first(['nombre', 'email']);
                \Log::info("👤 Usuario venta encontrado: " . ($usuarioVenta ? $usuarioVenta->nombre : "No"));
            } catch (\Exception $e) {
                \Log::error("❌ Error obteniendo usuario venta: " . $e->getMessage());
            }
        }
        
        // Obtener usuario ACTUALMENTE LOGEADO (quien está viendo/imprimiendo)
        $usuarioLogeado = null;
        try {
            // Intentar obtener el usuario autenticado
            if (auth()->check()) {
                $user = auth()->user();
                \Log::info("👤 Usuario autenticado detectado");
                
                // Crear objeto con los datos del usuario logeado
                // Usamos el operador null-safe para evitar errores
                $usuarioLogeado = [
                    'nombre' => $user->nombre ?? $user->name ?? 'Usuario Sistema',
                    'email' => $user->email ?? ''
                ];
                
                \Log::info("👤 Usuario logeado procesado: " . $usuarioLogeado['nombre']);
            } else {
                \Log::warning("⚠️ No hay usuario autenticado");
                $usuarioLogeado = [
                    'nombre' => 'Usuario Sistema',
                    'email' => ''
                ];
            }
        } catch (\Exception $e) {
            \Log::error("❌ Error obteniendo usuario logeado: " . $e->getMessage());
            $usuarioLogeado = [
                'nombre' => 'Usuario Sistema',
                'email' => ''
            ];
        }
        
        // Obtener detalle de productos de la venta
        $detalles = collect([]);
        try {
            $detalles = DB::table('detalle_ventas as dv')
                ->select(
                    'dv.*',
                    'p.nombre',
                    'p.codigo',
                    'p.precio_venta'
                )
                ->leftJoin('productos as p', 'dv.id_producto', '=', 'p.id_producto')
                ->where('dv.id_venta', (int)$id)
                ->get();
            \Log::info("📦 Detalles encontrados: " . $detalles->count());
        } catch (\Exception $e) {
            \Log::error("❌ Error obteniendo detalles: " . $e->getMessage());
        }
        
        // Preparar respuesta
        $response = [
            'success' => true,
            'message' => 'Detalle obtenido correctamente',
            'data' => [
                'venta' => [
                    'id' => $venta->id_venta,
                    'numero_factura' => $venta->numero_factura ?? 'F-' . str_pad($venta->id_venta, 6, '0', STR_PAD_LEFT),
                    'fecha' => $fecha->format('d/m/Y'),
                    'hora' => $fecha->format('H:i:s'),
                    'total' => (float)($venta->total ?? 0),
                    'estado' => $venta->estado ?? 'completada',
                    'metodo_pago' => $venta->metodo_pago ?? 'efectivo',
                    'observaciones' => $venta->observaciones ?? null,
                ],
                'cliente' => $cliente ? [
                    'nombre' => $cliente->nombre ?? 'Cliente General',
                    'telefono' => $cliente->telefono ?? '',
                    'email' => $cliente->email ?? ''
                ] : [
                    'nombre' => 'Cliente General',
                    'telefono' => '',
                    'email' => ''
                ],
                // Usuario que REALIZÓ la venta originalmente
                'vendedor_original' => $usuarioVenta ? [
                    'nombre' => $usuarioVenta->nombre ?? 'No especificado',
                    'email' => $usuarioVenta->email ?? ''
                ] : [
                    'nombre' => 'No especificado',
                    'email' => ''
                ],
                // Usuario que ESTÁ LOGEADO actualmente (quien imprime)
                'usuario_actual' => $usuarioLogeado,
                'detalles' => $detalles->map(function($item) {
                    return [
                        'id_producto' => $item->id_producto ?? 0,
                        'nombre' => $item->nombre ?? 'Producto eliminado',
                        'codigo' => $item->codigo ?? 'N/A',
                        'cantidad' => (int)($item->cantidad ?? 0),
                        'precio_unitario' => (float)($item->precio_venta ?? 0),
                        'subtotal' => (float)($item->subtotal ?? 0)
                    ];
                })
            ]
        ];
        
        \Log::info("✅ Respuesta preparada correctamente");
        \Log::info("📤 Vendedor original: " . ($response['data']['vendedor_original']['nombre'] ?? 'N/A'));
        \Log::info("📤 Usuario actual: " . ($response['data']['usuario_actual']['nombre'] ?? 'N/A'));
        
        return response()->json($response);
        
    } catch (\Exception $e) {
        \Log::error("❌ Error crítico en getDetalleVenta ID: $id");
        \Log::error("📌 Mensaje: " . $e->getMessage());
        \Log::error("📌 Línea: " . $e->getLine());
        \Log::error("📌 Archivo: " . $e->getFile());
        \Log::error("📌 Trace: " . $e->getTraceAsString());
        
        // Para desarrollo, mostrar error completo
        if (env('APP_DEBUG')) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'error' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile()),
                    'trace' => explode("\n", $e->getTraceAsString())
                ]
            ], 500);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor'
        ], 500);
    }
}
} 
