<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventarioController extends Controller
{
    /**
     * Ver historial de movimientos de inventario
     */
    public function index(Request $request)
    {
        $query = Inventario::with(['producto', 'usuario', 'venta']);
        
        // Filtrar por producto
        if ($request->filled('id_producto')) {
            $query->where('id_producto', $request->id_producto);
        }
        
        // Filtrar por tipo de movimiento
        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }
        
        // Filtrar por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_fin);
        }
        
        $inventarios = $query->orderBy('created_at', 'desc')->paginate(50);
        
        return view('inventarios.index', compact('inventarios'));
    }
    
    /**
     * Ver kardex de un producto específico
     */
    public function kardex($id_producto)
    {
        $producto = Producto::findOrFail($id_producto);
        
        $movimientos = Inventario::where('id_producto', $id_producto)
            ->with(['usuario', 'venta'])
            ->orderBy('fecha_movimiento', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('inventarios.kardex', compact('producto', 'movimientos'));
    }
    
    /**
     * Reporte de entradas y salidas
     */
    public function reporte(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', Carbon::now());
        
        $entradas = Inventario::entradas()
            ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin])
            ->sum('cantidad');
        
        $salidas = Inventario::salidas()
            ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin])
            ->sum('cantidad');
        
        return response()->json([
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'entradas' => $entradas,
            'salidas' => $salidas,
            'diferencia' => $entradas - $salidas
        ]);
    }

    /**
 * Listar inventarios para DataTables
 */
public function listar(Request $request)
{
    $query = Inventario::with(['producto', 'usuario'])
        ->where('tipo_movimiento', 'entrada')
        ->orderBy('created_at', 'desc');
    
    // Filtros
    if ($request->filled('fecha_inicio')) {
        $query->whereDate('fecha_movimiento', '>=', $request->fecha_inicio);
    }
    
    if ($request->filled('fecha_fin')) {
        $query->whereDate('fecha_movimiento', '<=', $request->fecha_fin);
    }
    
    if ($request->filled('proveedor')) {
        $query->where('proveedor', $request->proveedor);
    }
    
    $inventarios = $query->get();
    
    return response()->json([
        'success' => true,
        'inventarios' => $inventarios
    ]);
}

/**
 * Mostrar detalle de un inventario
 */
public function show($id)
{
    $inventario = Inventario::with(['producto', 'usuario'])->findOrFail($id);
    
    return response()->json([
        'success' => true,
        'inventario' => $inventario
    ]);
}

/**
 * Estadísticas para el dashboard
 */
public function estadisticas()
{
    $comprasHoy = Inventario::entradas()
        ->whereDate('fecha_movimiento', today())
        ->count();
    
    $totalInvertido = Inventario::entradas()
        ->whereDate('fecha_movimiento', today())
        ->get()
        ->sum(function($item) {
            return $item->cantidad * ($item->precio_compra ?? 0);
        });
    
    $productosComprados = Inventario::entradas()
        ->whereDate('fecha_movimiento', today())
        ->sum('cantidad');
    
    $comprasMes = Inventario::entradas()
        ->whereMonth('fecha_movimiento', now()->month)
        ->whereYear('fecha_movimiento', now()->year)
        ->count();
    
    return response()->json([
        'success' => true,
        'compras_hoy' => $comprasHoy,
        'total_invertido' => $totalInvertido,
        'productos_comprados' => $productosComprados,
        'compras_mes' => $comprasMes
    ]);
  }
}