<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
    
    public function getEstadisticas()
    {
        try {
            Log::info('Solicitando estadísticas del dashboard');
            
            $hoy = Carbon::today();
            $ayer = Carbon::yesterday();
            
            // 1. Total ventas hoy
            $ventasHoy = DB::table('ventas')
                ->whereDate('created_at', $hoy)
                ->where('estado', 'completada')
                ->count();
            Log::info('Ventas hoy: ' . $ventasHoy);
            
            // 2. Ventas de ayer para comparativa
            $ventasAyer = DB::table('ventas')
                ->whereDate('created_at', $ayer)
                ->where('estado', 'completada')
                ->count();
            
            // 3. Ingresos totales (hoy)
            $ingresosHoy = DB::table('ventas')
                ->whereDate('created_at', $hoy)
                ->where('estado', 'completada')
                ->sum('total') ?? 0;
            
            // 5. Promedio por venta (hoy)
            $promedioVenta = $ventasHoy > 0 ? $ingresosHoy / $ventasHoy : 0;
            
            // 6. Alertas de stock bajo - Verifica si existe la columna stock_minimo
            try {
                $alertasStock = DB::table('productos')
                    ->where('stock', '<=', DB::raw('IFNULL(stock_minimo, 5)'))
                    ->count();
            } catch (\Exception $e) {
                // Si no existe stock_minimo, usa un valor por defecto
                $alertasStock = DB::table('productos')
                    ->where('stock', '<=', 5)
                    ->count();
            }
            
            // Cálculo de comparativas
            $comparativaVentas = 0;
            if ($ventasAyer > 0) {
                $comparativaVentas = round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 1);
            } elseif ($ventasHoy > 0) {
                $comparativaVentas = 100;
            }
            
            $tendenciaIngresos = 'Sin datos';
            
            return response()->json([
                'success' => true,
                'total_ventas_hoy' => $ventasHoy,
                'comparativa_ventas' => $comparativaVentas,
                'ingresos_totales' => (float) $ingresosHoy,
                'tendencia_ingresos' => $tendenciaIngresos,
                'promedio_venta' => (float) round($promedioVenta, 2),
                'alertas_stock' => $alertasStock
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en getEstadisticas: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }
    
    public function getProductosVendidos(Request $request)
    {
        try {
            Log::info('Solicitando productos vendidos');
            $periodo = $request->input('periodo', 'mes');
            $hoy = Carbon::now();
            
            // Primero verifica si las tablas existen
            $tablasExisten = DB::select("SHOW TABLES LIKE 'ventas'");
            if (empty($tablasExisten)) {
                return response()->json([
                    'success' => true,
                    'productos' => []
                ]);
            }
            
            $query = DB::table('detalle_ventas as dv')
                ->join('productos as p', 'dv.id_producto', '=', 'p.id')
                ->join('ventas as v', 'dv.id_venta', '=', 'v.id')
                ->select(
                    'p.id',
                    'p.nombre',
                    'p.codigo',
                    DB::raw('COALESCE(SUM(dv.cantidad), 0) as cantidad_vendida'),
                    DB::raw('COALESCE(SUM(dv.cantidad * dv.precio_unitario), 0) as total_vendido')
                )
                ->where('v.estado', 'completada')
                ->groupBy('p.id', 'p.nombre', 'p.codigo')
                ->orderByDesc('cantidad_vendida');
            
            // Aplicar filtro por periodo
            switch ($periodo) {
                case 'hoy':
                    $query->whereDate('v.created_at', $hoy->toDateString());
                    break;
                case 'semana':
                    $query->whereBetween('v.created_at', [
                        $hoy->copy()->startOfWeek(),
                        $hoy->copy()->endOfWeek()
                    ]);
                    break;
                case 'mes':
                    $query->whereMonth('v.created_at', $hoy->month)
                          ->whereYear('v.created_at', $hoy->year);
                    break;
                case 'anio':
                    $query->whereYear('v.created_at', $hoy->year);
                    break;
            }
            
            $productos = $query->limit(8)->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en getProductosVendidos: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos vendidos',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }
    
    public function getStockBajo()
    {
        try {
            Log::info('Solicitando stock bajo');
            
            // Primero verifica si la tabla productos existe
            $tablaExiste = DB::select("SHOW TABLES LIKE 'productos'");
            if (empty($tablaExiste)) {
                return response()->json([
                    'success' => true,
                    'productos' => []
                ]);
            }
            
            // Verifica las columnas de la tabla productos
            $columnas = DB::select("SHOW COLUMNS FROM productos");
            $columnasArray = array_column($columnas, 'Field');
            
            // Determina si existe la columna stock_minimo
            $tieneStockMinimo = in_array('stock_minimo', $columnasArray);
            $tieneCategoria = in_array('categoria', $columnasArray);
            
            $query = DB::table('productos')
                ->select('id', 'nombre');
            
            // Agrega categoría si existe
            if ($tieneCategoria) {
                $query->addSelect('categoria');
            } else {
                $query->selectRaw("'General' as categoria");
            }
            
            // Agrega stock
            $query->addSelect('stock as stock_actual');
            
            // Agrega stock_minimo
            if ($tieneStockMinimo) {
                $query->addSelect('stock_minimo');
            } else {
                $query->selectRaw('5 as stock_minimo');
            }
            
            // Filtro para stock bajo
            if ($tieneStockMinimo) {
                $query->where(function($q) {
                    $q->whereColumn('stock', '<=', 'stock_minimo')
                      ->orWhere('stock', '<=', DB::raw('stock_minimo * 1.5'));
                });
            } else {
                $query->where('stock', '<=', 10);
            }
            
            $query->where('stock', '>', 0)
                  ->orderBy('stock', 'asc')
                  ->limit(10);
            
            $productos = $query->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en getStockBajo: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener stock bajo',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }
    
    public function getVentasRecientes()
    {
        try {
            Log::info('Solicitando ventas recientes');
            
            // Primero verifica si las tablas existen
            $tablaVentas = DB::select("SHOW TABLES LIKE 'ventas'");
            $tablaClientes = DB::select("SHOW TABLES LIKE 'clientes'");
            
            if (empty($tablaVentas)) {
                return response()->json([
                    'success' => true,
                    'ventas' => []
                ]);
            }
            
            $query = DB::table('ventas as v')
                ->select(
                    'v.id',
                    'v.numero_factura',
                    'v.created_at as fecha_venta',
                    'v.total',
                    'v.estado'
                );
            
            // Verifica si existe tabla clientes
            if (!empty($tablaClientes)) {
                $query->leftJoin('clientes as c', 'v.id_cliente', '=', 'c.id')
                      ->addSelect('c.nombre as nombre_cliente');
            } else {
                $query->selectRaw("'Cliente no registrado' as nombre_cliente");
            }
            
            // Verifica si existe tabla detalle_ventas
            $tablaDetalle = DB::select("SHOW TABLES LIKE 'detalle_ventas'");
            if (!empty($tablaDetalle)) {
                $query->selectRaw("(SELECT COUNT(*) FROM detalle_ventas WHERE id_venta = v.id) as cantidad_productos");
            } else {
                $query->selectRaw('1 as cantidad_productos');
            }
            
            $ventas = $query->where('v.estado', 'completada')
                           ->orderByDesc('v.created_at')
                           ->limit(5)
                           ->get();
            
            return response()->json([
                'success' => true,
                'ventas' => $ventas
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en getVentasRecientes: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener ventas recientes',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }
}