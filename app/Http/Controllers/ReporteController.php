<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\DetalleCompra;
use Carbon\Carbon;
use DB;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes');
    }

    public function getVentasData(Request $request)
    {
        $periodo = $request->periodo ?? 'mensual';
        
        switch($periodo) {
            case 'diario':
                $ventas = Venta::select(
                        DB::raw('DATE(created_at) as fecha'),
                        DB::raw('COUNT(*) as total_ventas'),
                        DB::raw('SUM(total) as monto_total')
                    )
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->groupBy('fecha')
                    ->orderBy('fecha', 'ASC')
                    ->get();
                break;
            case 'mensual':
                $ventas = Venta::select(
                        DB::raw('YEAR(created_at) as año'),
                        DB::raw('MONTH(created_at) as mes'),
                        DB::raw('COUNT(*) as total_ventas'),
                        DB::raw('SUM(total) as monto_total')
                    )
                    ->whereYear('created_at', Carbon::now()->year)
                    ->groupBy('año', 'mes')
                    ->orderBy('año', 'ASC')
                    ->orderBy('mes', 'ASC')
                    ->get();
                
                $ventas = $ventas->map(function($item) {
                    $item->fecha = Carbon::create()->month($item->mes)->format('F');
                    return $item;
                });
                break;
            case 'anual':
                $ventas = Venta::select(
                        DB::raw('YEAR(created_at) as año'),
                        DB::raw('COUNT(*) as total_ventas'),
                        DB::raw('SUM(total) as monto_total')
                    )
                    ->groupBy('año')
                    ->orderBy('año', 'ASC')
                    ->get();
                $ventas = $ventas->map(function($item) {
                    $item->fecha = $item->año;
                    return $item;
                });
                break;
            default:
                $ventas = collect();
                break;
        }

        // Productos más vendidos
        $productosTop = DetalleVenta::select(
                'productos.nombre as nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_ventas.subtotal) as monto_total')
            )
            ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
            ->groupBy('productos.id_producto', 'productos.nombre')
            ->orderBy('total_vendido', 'DESC')
            ->limit(5)
            ->get();

        // Resumen general
        $resumen = [
            'total_ventas' => Venta::count(),
            'monto_total' => Venta::sum('total'),
            'ventas_hoy' => Venta::whereDate('created_at', Carbon::today())->count(),
            'monto_hoy' => Venta::whereDate('created_at', Carbon::today())->sum('total'),
            'promedio_venta' => Venta::avg('total'),
        ];

        return response()->json([
            'success' => true,
            'data' => $ventas,
            'productos_top' => $productosTop,
            'resumen' => $resumen
        ]);
    }

    public function getComprasData(Request $request)
    {
        $periodo = $request->periodo ?? 'mensual';
        
        switch($periodo) {
            case 'diario':
                $compras = Compra::select(
                        DB::raw('DATE(created_at) as fecha'),
                        DB::raw('COUNT(*) as total_compras'),
                        DB::raw('SUM(precio_total) as monto_total')
                    )
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->groupBy('fecha')
                    ->orderBy('fecha', 'ASC')
                    ->get();
                break;
            case 'mensual':
                $compras = Compra::select(
                        DB::raw('YEAR(created_at) as año'),
                        DB::raw('MONTH(created_at) as mes'),
                        DB::raw('COUNT(*) as total_compras'),
                        DB::raw('SUM(precio_total) as monto_total')
                    )
                    ->whereYear('created_at', Carbon::now()->year)
                    ->groupBy('año', 'mes')
                    ->orderBy('año', 'ASC')
                    ->orderBy('mes', 'ASC')
                    ->get();
                
                $compras = $compras->map(function($item) {
                    $item->fecha = Carbon::create()->month($item->mes)->format('F');
                    return $item;
                });
                break;
            case 'anual':
                $compras = Compra::select(
                        DB::raw('YEAR(created_at) as año'),
                        DB::raw('COUNT(*) as total_compras'),
                        DB::raw('SUM(precio_total) as monto_total')
                    )
                    ->groupBy('año')
                    ->orderBy('año', 'ASC')
                    ->get();
                $compras = $compras->map(function($item) {
                    $item->fecha = $item->año;
                    return $item;
                });
                break;
            default:
                $compras = collect();
                break;
        }

        // Proveedores más frecuentes
        $proveedoresTop = Compra::select(
                'proveedores.razon_social as nombre',
                DB::raw('COUNT(compras.id_compra) as total_compras'),
                DB::raw('SUM(compras.precio_total) as monto_total')
            )
            ->join('proveedores', 'compras.id_proveedor', '=', 'proveedores.id_proveedor')
            ->groupBy('proveedores.id_proveedor', 'proveedores.razon_social')
            ->orderBy('total_compras', 'DESC')
            ->limit(5)
            ->get();

        // Resumen general
        $resumen = [
            'total_compras' => Compra::count(),
            'monto_total' => Compra::sum('precio_total'),
            'compras_mes' => Compra::whereMonth('created_at', Carbon::now()->month)->count(),
            'monto_mes' => Compra::whereMonth('created_at', Carbon::now()->month)->sum('precio_total'),
            'promedio_compra' => Compra::avg('precio_total'),
        ];

        return response()->json([
            'success' => true,
            'data' => $compras,
            'proveedores_top' => $proveedoresTop,
            'resumen' => $resumen
        ]);
    }

    public function getInventarioData(Request $request)
    {
        // Productos con bajo stock
        $bajoStock = Producto::where('stock_actual', '<=', DB::raw('stock_minimo'))
            ->where('stock_actual', '>', 0)
            ->select('id_producto', 'nombre as nombre', 'stock_actual as stock', 'stock_minimo', 'precio_venta')
            ->get();

        // Productos sin stock
        $sinStock = Producto::where('stock_actual', '<=', 0)
            ->select('id_producto', 'nombre as nombre', 'stock_actual as stock', 'precio_venta')
            ->get();

        // Productos por categoría - Cambiado: nombre_categoria -> nombre
        $productosPorCategoria = Producto::select(
                'categorias.nombre as categoria',
                DB::raw('COUNT(productos.id_producto) as total_productos'),
                DB::raw('SUM(productos.stock_actual) as stock_total'),
                DB::raw('SUM(productos.precio_venta * productos.stock_actual) as valor_inventario')
            )
            ->join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->groupBy('categorias.id_categoria', 'categorias.nombre')
            ->get();

        // Top 10 productos con más valor en inventario
        $topValorInventario = Producto::select(
                'nombre as nombre',
                'stock_actual as stock',
                'precio_venta',
                DB::raw('stock_actual * precio_venta as valor_total')
            )
            ->orderBy('valor_total', 'DESC')
            ->limit(10)
            ->get();

        // Resumen general
        $resumen = [
            'total_productos' => Producto::count(),
            'stock_total' => Producto::sum('stock_actual'),
            'valor_inventario' => Producto::sum(DB::raw('stock_actual * precio_venta')),
            'productos_bajo_stock' => Producto::where('stock_actual', '<=', DB::raw('stock_minimo'))->count(),
            'productos_sin_stock' => Producto::where('stock_actual', '<=', 0)->count(),
        ];

        return response()->json([
            'success' => true,
            'bajo_stock' => $bajoStock,
            'sin_stock' => $sinStock,
            'por_categoria' => $productosPorCategoria,
            'top_valor' => $topValorInventario,
            'resumen' => $resumen
        ]);
    }
}