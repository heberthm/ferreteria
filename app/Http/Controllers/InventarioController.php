<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Venta;
use App\Exports\InventarioExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('inventarios');
    }

    /**
     * Get data for DataTable
     */
    public function getData(Request $request)
    {
        $query = Inventario::with(['producto', 'usuario', 'venta']);
        
        // Aplicar filtros
        if ($request->producto_id) {
            $query->where('id_producto', $request->producto_id);
        }
        
        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha_movimiento', [$request->fecha_inicio, $request->fecha_fin]);
        }
        
        if ($request->tipo_movimiento) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }
        
        if ($request->proveedor) {
            $query->where('proveedor', 'LIKE', "%{$request->proveedor}%");
        }
        
        if ($request->numero_factura) {
            $query->where('numero_factura', 'LIKE', "%{$request->numero_factura}%");
        }
        
        return DataTables::of($query)
            ->editColumn('fecha_movimiento', function($row) {
                return $row->fecha_movimiento->format('d/m/Y');
            })
            ->addColumn('producto_nombre', function($row) {
                return $row->producto ? $row->producto->nombre : 'Producto Eliminado';
            })
            ->addColumn('producto_codigo', function($row) {
                return $row->producto ? $row->producto->codigo : 'N/A';
            })
            ->addColumn('usuario_nombre', function($row) {
                return $row->usuario ? $row->usuario->name : 'N/A';
            })
            ->addColumn('tipo_movimiento_badge', function($row) {
                return '<span class="badge badge-' . $row->tipo_movimiento_color . '">' . 
                       $row->tipo_movimiento_formatted . '</span>';
            })
            ->editColumn('precio_venta', function($row) {
                return $row->precio_venta ? '$' . number_format($row->precio_venta, 2) : 'N/A';
            })
            ->editColumn('costo_promedio', function($row) {
                return $row->costo_promedio ? '$' . number_format($row->costo_promedio, 2) : 'N/A';
            })
            ->editColumn('ultimo_costo', function($row) {
                return $row->ultimo_costo ? '$' . number_format($row->ultimo_costo, 2) : 'N/A';
            })
            ->editColumn('precio_compra', function($row) {
                return $row->precio_compra ? '$' . number_format($row->precio_compra, 2) : 'N/A';
            })
            ->addColumn('acciones', function($row) {
                return '<div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm verMovimiento" data-id="'.$row->id_inventario.'">
                                <i class="fas fa-eye"></i> Ver
                            </button>                                  
                        </div>';
            })
            ->rawColumns(['tipo_movimiento_badge', 'acciones'])
            ->make(true);
    }

    /**
     * Get resume data for dashboard
     */
    public function getResumen(Request $request)
    {
        $query = Inventario::query();
        
        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha_movimiento', [$request->fecha_inicio, $request->fecha_fin]);
        }
        
        $totalMovimientos = $query->count();
        
        $totalEntradas = $query->clone()
            ->where('tipo_movimiento', 'entrada')
            ->sum('cantidad');
            
        $totalSalidas = $query->clone()
            ->where('tipo_movimiento', 'salida')
            ->sum('cantidad');
            
        $totalAjustes = $query->clone()
            ->where('tipo_movimiento', 'ajuste')
            ->sum('cantidad');
            
        $totalDevoluciones = $query->clone()
            ->where('tipo_movimiento', 'devolucion')
            ->sum('cantidad');
            
        $valorTotalInventario = Producto::sum(DB::raw('stock_actual * costo_promedio'));
        
        $productosMasMovidos = Inventario::select('id_producto', DB::raw('SUM(cantidad) as total_movimientos'))
            ->whereIn('tipo_movimiento', ['entrada', 'salida'])
            ->groupBy('id_producto')
            ->orderBy('total_movimientos', 'desc')
            ->with('producto')
            ->limit(5)
            ->get();
        
        return response()->json([
            'total_movimientos' => $totalMovimientos,
            'total_entradas' => $totalEntradas,
            'total_salidas' => $totalSalidas,
            'total_ajustes' => $totalAjustes,
            'total_devoluciones' => $totalDevoluciones,
            'valor_total_inventario' => $valorTotalInventario,
            'productos_mas_movidos' => $productosMasMovidos
        ]);
    }

    /**
     * Get detail of a specific movement
     */
   public function getDetalle($id)
{
    try {
        $inventario = Inventario::with(['producto.categoria', 'usuario', 'venta'])
            ->findOrFail($id);
            
        return response()->json([
            'id_inventario' => $inventario->id_inventario,
            'producto_codigo' => $inventario->producto ? $inventario->producto->codigo : 'N/A',
            'producto_nombre' => $inventario->producto ? $inventario->producto->nombre : 'Producto Eliminado',
            'producto_categoria' => $inventario->producto && $inventario->producto->categoria ? $inventario->producto->categoria->nombre : 'N/A',
            'tipo_movimiento' => $inventario->tipo_movimiento_formatted,
            'tipo_movimiento_color' => $inventario->tipo_movimiento_color,
            'cantidad' => $inventario->cantidad,
            'stock_anterior' => $inventario->stock_anterior,
            'stock_nuevo' => $inventario->stock_nuevo,
            'precio_venta' => $inventario->precio_venta ? '$' . number_format($inventario->precio_venta, 2) : 'N/A',
            'costo_promedio' => $inventario->costo_promedio ? '$' . number_format($inventario->costo_promedio, 2) : 'N/A',
            'ultimo_costo' => $inventario->ultimo_costo ? '$' . number_format($inventario->ultimo_costo, 2) : 'N/A',
            'precio_compra' => $inventario->precio_compra ? '$' . number_format($inventario->precio_compra, 2) : 'N/A',
            'metodo_pago' => $inventario->metodo_pago,
            'proveedor' => $inventario->proveedor,
            'numero_factura' => $inventario->numero_factura,
            'fecha_movimiento' => $inventario->fecha_movimiento->format('d/m/Y'),
            'notas' => $inventario->notas,
            'usuario_nombre' => $inventario->usuario ? $inventario->usuario->name : 'N/A',
            'created_at' => $inventario->created_at ? $inventario->created_at->format('d/m/Y H:i') : 'N/A'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Movimiento no encontrado',
            'message' => $e->getMessage()
        ], 404);
    }
}

    /**
     * Register a new inventory movement
     */
    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste,devolucion',
            'cantidad' => 'required|integer|min:1',
            'precio_compra' => 'required_if:tipo_movimiento,entrada|nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|string|max:50',
            'proveedor' => 'nullable|string|max:100',
            'numero_factura' => 'nullable|string|max:50',
            'id_venta' => 'nullable|exists:ventas,id_venta',
            'notas' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            $producto = Producto::findOrFail($request->id_producto);
            $stockAnterior = $producto->stock_actual;
            $nuevoStock = $stockAnterior;
            
            // Calcular valores según el tipo de movimiento
            $costoPromedio = null;
            $ultimoCosto = null;
            $precioCompra = $request->precio_compra;
            
            switch ($request->tipo_movimiento) {
                case 'entrada':
                    $nuevoStock = $stockAnterior + $request->cantidad;
                    
                    // Calcular costo promedio
                    $costoPromedio = Inventario::calcularCostoPromedio(
                        $request->id_producto, 
                        $request->precio_compra, 
                        $request->cantidad
                    );
                    
                    $ultimoCosto = $request->precio_compra;
                    
                    // Actualizar producto
                    $producto->stock = $nuevoStock;
                    if ($request->precio_compra) {
                       $producto->costo_promedio = $request->precio_compra;
                       $producto->ultimo_costo = $request->precio_compra;
                    }
                    if ($request->precio_venta) {
                        $producto->precio_venta = $request->precio_venta;
                    }
                    $producto->save();
                    break;
                    
                case 'salida':
                    if ($stockAnterior < $request->cantidad) {
                        throw new \Exception('Stock insuficiente para realizar la salida');
                    }
                    $nuevoStock = $stockAnterior - $request->cantidad;
                    
                    // Obtener costo promedio actual
                    $costoPromedio = $producto->costo_promedio;
                    $ultimoCosto = $producto->ultimo_costo;
                    $precioCompra = $producto->costo_promedio;
                    
                    // Actualizar producto
                    $producto->stock = $nuevoStock;
                    $producto->save();
                    break;
                    
                case 'ajuste':
                    $nuevoStock = $request->cantidad; // En ajuste, la cantidad es el nuevo stock
                    $diferencia = $nuevoStock - $stockAnterior;
                    
                    $costoPromedio = $producto->precio_compra;
                    $ultimoCosto = $producto->precio_compra;
                    $precioCompra = $producto->precio_compra;
                    
                    // Actualizar producto
                    $producto->stock = $nuevoStock;
                    $producto->save();
                    break;
                    
                case 'devolucion':
                    $nuevoStock = $stockAnterior + $request->cantidad;
                    
                    $costoPromedio = $producto->precio_compra;
                    $ultimoCosto = $producto->precio_compra;
                    $precioCompra = $producto->precio_compra;
                    
                    // Actualizar producto
                    $producto->stock_actual = $nuevoStock;
                    $producto->save();
                    break;
            }
            
            // Registrar en inventario
            $inventario = Inventario::create([
                'id_producto' => $request->id_producto,
                'tipo_movimiento' => $request->tipo_movimiento,
                'cantidad' => $request->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $nuevoStock,
                'precio_venta' => $request->precio_venta,
                'costo_promedio' => $costoPromedio,
                'ultimo_costo' => $ultimoCosto,
                'precio_compra' => $precioCompra,
                'metodo_pago' => $request->metodo_pago,
                'proveedor' => $request->proveedor,
                'numero_factura' => $request->numero_factura,
                'id_venta' => $request->id_venta,
                'fecha_movimiento' => now(),
                'notas' => $request->notas,
                'userId' => auth()->id()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Movimiento registrado correctamente',
                'data' => $inventario
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el movimiento: ' . $e->getMessage()
            ], 500);
        }
    }

  /**
     * Export to excel
     */

public function exportar(Request $request)
{
    $filename = 'kardex_inventario_' . now()->format('d-m-Y_H-i-s') . '.xlsx';
    
    return Excel::download(new InventarioExport(), $filename);
}

    /**
     * Print movement
     */
    public function imprimir($id)
    {
        $inventario = Inventario::with(['producto', 'usuario', 'venta'])
            ->findOrFail($id);
            
        return view('inventario.print', compact('inventario'));
    }
}