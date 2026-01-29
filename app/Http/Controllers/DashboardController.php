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
            
            // 2. STOCK BAJO
            Log::info('Dashboard: Consultando stock bajo');
            
            $stockBajo = DB::table('productos')
                ->whereRaw('stock <= stock_minimo')
                ->orWhere('stock', '<=', 2)
                ->orderBy('stock', 'asc')
                ->limit(10)
                ->get(['id_producto', 'nombre', 'codigo', 'stock', 'stock_minimo'])
                ->map(function($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->codigo ?? '',
                        'stock_actual' => $producto->stock ?? 0,
                        'stock_minimo' => $producto->stock_minimo ?? 5,
                        'estado' => ($producto->stock ?? 0) <= 2 ? 'CRÍTICO' : 'BAJO'
                    ];
                });
            
            Log::info('Dashboard: Stock bajo OK', ['cantidad' => $stockBajo->count()]);
            
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
                ->limit(10)
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
                        'productos_stock_bajo' => $stockBajo->count()
                    ],
                    'stock_bajo' => $stockBajo,
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
}