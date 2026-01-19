<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $hoy = Carbon::today();
            $ayer = Carbon::yesterday();
            
            // 1. Total ventas hoy
            $ventasHoy = DB::table('ventas')
                ->whereDate('created_at', $hoy)
                ->where('estado', 'completada')
                ->count();
            
            // 2. Ventas de ayer para comparativa
            $ventasAyer = DB::table('ventas')
                ->whereDate('created_at', $ayer)
                ->where('estado', 'completada')
                ->count();
            
            // 3. Ingresos totales (hoy)
            $ingresosHoy = DB::table('ventas')
                ->whereDate('created_at', $hoy)
                ->where('estado', 'completada')
                ->sum('total');
            
            // 4. Ingresos del mes actual
            $ingresosMes = DB::table('ventas')
                ->whereMonth('created_at', $hoy->month)
                ->whereYear('created_at', $hoy->year)
                ->where('estado', 'completada')
                ->sum('total');
            
            // 5. Promedio por venta (hoy)
            $promedioVenta = $ventasHoy > 0 ? $ingresosHoy / $ventasHoy : 0;
            
            // 6. Alertas de stock bajo
            $alertasStock = DB::table('productos')
                ->where('stock', '<=', DB::raw('stock_minimo'))
                ->count();
            
            // Cálculo de comparativas
            $comparativaVentas = $ventasAyer > 0 
                ? round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 1)
                : ($ventasHoy > 0 ? 100 : 0);
            
            // Tendencia de ingresos (comparativa mensual)
            $mesAnterior = $hoy->copy()->subMonth();
            $ingresosMesAnterior = DB::table('ventas')
                ->whereMonth('created_at', $mesAnterior->month)
                ->whereYear('created_at', $mesAnterior->year)
                ->where('estado', 'completada')
                ->sum('total');
            
            $tendenciaIngresos = $ingresosMesAnterior > 0 
                ? round((($ingresosMes - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1)
                : ($ingresosMes > 0 ? '↑ 100%' : 'Sin datos');
            
            return response()->json([
                'success' => true,
                'total_ventas_hoy' => $ventasHoy,
                'comparativa_ventas' => $comparativaVentas,
                'ingresos_totales' => (float) $ingresosHoy,
                'tendencia_ingresos' => $tendenciaIngresos > 0 ? "↑ {$tendenciaIngresos}%" : "↓ " . abs($tendenciaIngresos) . "%",
                'promedio_venta' => (float) $promedioVenta,
                'alertas_stock' => $alertasStock
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getProductosVendidos(Request $request)
    {
        try {
            $periodo = $request->input('periodo', 'mes');
            
            $query = DB::table('detalle_ventas as dv')
                ->join('productos as p', 'dv.id_producto', '=', 'p.id')
                ->join('ventas as v', 'dv.id_venta', '=', 'v.id')
                ->select(
                    'p.id',
                    'p.nombre',
                    'p.codigo',
                    DB::raw('SUM(dv.cantidad) as cantidad_vendida'),
                    DB::raw('SUM(dv.cantidad * dv.precio_unitario) as total_vendido')
                )
                ->where('v.estado', 'completada')
                ->groupBy('p.id', 'p.nombre', 'p.codigo')
                ->orderByDesc('cantidad_vendida');
            
            // Aplicar filtro por periodo
            switch ($periodo) {
                case 'hoy':
                    $query->whereDate('v.created_at', Carbon::today());
                    break;
                case 'semana':
                    $query->whereBetween('v.created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'mes':
                    $query->whereMonth('v.created_at', Carbon::now()->month)
                          ->whereYear('v.created_at', Carbon::now()->year);
                    break;
                case 'anio':
                    $query->whereYear('v.created_at', Carbon::now()->year);
                    break;
            }
            
            $productos = $query->limit(8)->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getStockBajo()
    {
        try {
            $productos = DB::table('productos')
                ->select('id', 'nombre', 'categoria', 'stock as stock_actual', 'stock_minimo')
                ->where('stock', '<=', DB::raw('stock_minimo * 1.5'))
                ->where('stock', '>', 0) // Solo productos con stock positivo
                ->orderByRaw('(stock / stock_minimo) ASC')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getVentasRecientes()
    {
        try {
            $ventas = DB::table('ventas as v')
                ->leftJoin('clientes as c', 'v.id_cliente', '=', 'c.id')
                ->select(
                    'v.id',
                    'v.numero_factura',
                    'v.created_at as fecha_venta',
                    'v.total',
                    'v.estado',
                    'c.nombre as nombre_cliente',
                    DB::raw('(SELECT COUNT(*) FROM detalle_ventas WHERE venta_id = v.id) as cantidad_productos')
                )
                ->where('v.estado', 'completada')
                ->orderByDesc('v.created_at')
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'ventas' => $ventas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}