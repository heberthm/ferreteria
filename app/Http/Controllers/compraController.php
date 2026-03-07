<?php
// app/Http/Controllers/CompraController.php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompraController extends Controller
{
    /**
     * Constructor - aplicar middleware si es necesario
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar vista principal de compras
     */
   public function index()
{
    try {
        // Intentar obtener proveedores
        $proveedores = Proveedor::all();
        
        // Si no hay proveedores, usar array vacío
        if ($proveedores->isEmpty()) {
            $proveedores = collect([]);
        }
        
        // Pasar proveedores a la vista
        return view('compras', compact('proveedores'));
        
    } catch (\Exception $e) {
        // Mostrar el error en el log
        \Log::error('Error en CompraController@index: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        // Devolver una respuesta amigable
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar la página: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Guardar nueva compra
 */ 


public function guardar(Request $request)
{
    try {
        DB::beginTransaction();
        
        // Validar datos
        $validator = Validator::make($request->all(), [
            'id_producto' => 'required|exists:productos,id_producto',
             'cantidad_comprada' => 'required|numeric|min:1',
            'precio_compra' => 'required|numeric|min:0',
            'fecha_compra' => 'required|date',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'numero_factura' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $producto = Producto::findOrFail($request->id_producto);
        
        // Usar stock_actual en lugar de stock
        $stockAnterior = $producto->stock_actual;  // ✅ CORRECTO
        $cantidad = $request->cantidad;
        $precioCompra = $request->precio_compra;
        $precioTotal = $cantidad * $precioCompra;
        $stockNuevo = $stockAnterior + $cantidad;
        
        // 1. GUARDAR EN TABLA COMPRAS
        $compra = new Compra();
        $compra->id_producto = $request->id_producto;
        $compra->id_proveedor = $request->id_proveedor;
        $compra->cantidad = $cantidad;
        $compra->precio_compra = $precioCompra;
        $compra->precio_total = $precioTotal;
        $compra->fecha_compra = $request->fecha_compra;
        $compra->numero_factura = $request->numero_factura;
        $compra->notas = $request->notas;
        $compra->user_id = auth()->id();
        $compra->estado = 'completada';
        $compra->save();
        
        // 2. ACTUALIZAR EL PRODUCTO - ¡CORREGIDO!
        $producto->stock_actual = $stockNuevo;           // ✅ CAMBIADO de 'stock' a 'stock_actual'
        $producto->costo_promedio = $this->calcularPromedioPonderado(
            $stockAnterior, 
            $producto->costo_promedio, 
            $cantidad, 
            $precioCompra
        );
        $producto->ultimo_costo = $precioCompra;
        $producto->save();
        
        // 3. VERIFICAR STOCK MÍNIMO
        if ($producto->stock_actual <= $producto->stock_minimo) {
            Log::warning("Producto {$producto->nombre} está por debajo del stock mínimo");
        }
        
        // 4. REGISTRAR EN INVENTARIO
        Inventario::create([
            'id_producto' => $request->id_producto,
            'tipo_movimiento' => 'entrada',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'costo_promedio' => $producto->costo_promedio,
            'ultimo_costo' => $producto->ultimo_costo,
            'precio_compra' => $precioCompra,
            'precio_venta' => $producto->precio_venta,
            'proveedor' => $request->id_proveedor ? Proveedor::find($request->id_proveedor)->nombre : null,
            'numero_factura' => $request->numero_factura,
            'metodo_pago' => $request->metodo_pago ?? 'efectivo',
            'fecha_movimiento' => now(),
            'notas' => $request->notas,
            'userId' => auth()->id(),
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Compra registrada exitosamente',
            'data' => [
                'compra_id' => $compra->id_compra,
                'producto' => $producto->nombre,
                'cantidad' => $cantidad,
                'total' => $precioTotal
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Error en compra: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la compra: ' . $e->getMessage()
        ], 500);
    }
}


 /**
 * Listar inventarios para DataTables
 */

public function listarCompras(Request $request)
{

    try {
        // Total sin filtros
        $totalRegistros = \App\Models\Inventario::where('tipo_movimiento', 'entrada')->count();

        // Query con filtros
        $query = \App\Models\Inventario::with('producto')
            ->where('tipo_movimiento', 'entrada')
            ->select(
                'id_inventario', 'fecha_movimiento', 'id_producto',
                'cantidad', 'precio_compra', 'proveedor',
                'numero_factura', 'metodo_pago', 'notas',
                'stock_nuevo'
            );

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_fin);
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'LIKE', '%' . $request->proveedor . '%');
        }

        $totalFiltrado = $query->count();

        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);

        $compras = $query->orderBy('id_inventario', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $compras->map(fn($c) => [
            'id_compra'     => $c->id_inventario,
            'fecha_compra'  => $c->fecha_movimiento?->format('Y-m-d H:i:s'),
            'producto'      => $c->producto ? [
                'nombre' => $c->producto->nombre,
                'codigo' => $c->producto->codigo,
            ] : null,
            'cantidad'      => (int)   $c->cantidad,
            'precio_compra' => (float) $c->precio_compra,
            'proveedor'     => $c->proveedor ?? 'Sin proveedor',
            'stock_nuevo'   => (int)   $c->stock_nuevo,
        ])->values()->all();

        return response()->json([
            'draw'            => (int) $request->get('draw', 1),
            'recordsTotal'    => $totalRegistros,
            'recordsFiltered' => $totalFiltrado,
            'data'            => $data,
        ]);

    } catch (\Exception $e) {
        \Log::error('Error listar compras: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return response()->json([
            'draw'            => (int) $request->get('draw', 1),
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => [],
            'error'           => $e->getMessage(),
        ], 500);
    }
}

private function calcularPromedioPonderado($stockAnterior, $costoAnterior, $cantidadNueva, $precioCompra)
{
    if ($stockAnterior > 0 && $costoAnterior > 0) {
        return (($stockAnterior * $costoAnterior) + ($cantidadNueva * $precioCompra)) 
               / ($stockAnterior + $cantidadNueva);
    }
    return $precioCompra;
}

/**
 * Obtener una compra específica
 */
public function mostrar($id)
{
    try {
        $compra = Compra::with(['producto', 'proveedor', 'usuario'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $compra
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Compra no encontrada'
        ], 404);
    }
}
    /**
     * Anular una compra (eliminación lógica o física)
     */
    public function anular($id)
    {
        try {
            DB::beginTransaction();

            $compra = Compra::findOrFail($id);
            
            // Verificar que la compra se pueda anular (ej: no sea muy antigua)
            $diasDesdeCompra = now()->diffInDays($compra->fecha_compra);
            if ($diasDesdeCompra > 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede anular una compra con más de 30 días'
                ], 422);
            }

            // Restar stock del producto
            $producto = Producto::findOrFail($compra->id_producto);
            $stockAnterior = $producto->stock;
            
            if ($producto->stock < $compra->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock para anular la compra'
                ], 422);
            }
            
            $producto->stock -= $compra->cantidad;
            $producto->save();

            // Registrar anulación en inventario
            $this->registrarAnulacionInventario($compra, $producto, $stockAnterior);

            // Eliminar compra (o marcar como anulada si usas soft deletes)
            $compra->delete(); // Si usas soft deletes, esto solo marca como eliminado

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra anulada correctamente',
                'data' => [
                    'producto' => $producto->nombre,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->stock
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al anular compra: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al anular la compra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas para dashboard
     */
  /**
 * Obtener estadísticas de compras desde la tabla inventario
 */
public function estadisticasCompras()
{
    try {
        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();
        
        \Log::info('📊 Calculando estadísticas de compras', ['fecha' => $hoy]);
        
        // Compras de hoy (entradas en inventario)
        $comprasHoy = \App\Models\Inventario::whereDate('fecha_movimiento', $hoy)
            ->where('tipo_movimiento', 'entrada')
            ->count();
        
        // Total invertido hoy
        $totalInvertido = \App\Models\Inventario::whereDate('fecha_movimiento', $hoy)
            ->where('tipo_movimiento', 'entrada')
            ->selectRaw('SUM(cantidad * precio_compra) as total')
            ->value('total') ?? 0;
        
        // Productos comprados hoy (suma de cantidades)
        $productosComprados = \App\Models\Inventario::whereDate('fecha_movimiento', $hoy)
            ->where('tipo_movimiento', 'entrada')
            ->sum('cantidad') ?? 0;
        
        // Compras del mes
        $comprasMes = \App\Models\Inventario::whereBetween('fecha_movimiento', [$inicioMes, $finMes])
            ->where('tipo_movimiento', 'entrada')
            ->count();
        
        \Log::info('✅ Estadísticas calculadas:', [
            'compras_hoy' => $comprasHoy,
            'total_invertido' => $totalInvertido,
            'productos_comprados' => $productosComprados,
            'compras_mes' => $comprasMes
        ]);
        
        return response()->json([
            'success' => true,
            'compras_hoy' => $comprasHoy,
            'total_invertido' => (float) $totalInvertido,
            'productos_comprados' => (int) $productosComprados,
            'compras_mes' => $comprasMes
        ]);
        
    } catch (\Exception $e) {
        \Log::error('❌ Error en estadisticasCompras: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'compras_hoy' => 0,
            'total_invertido' => 0,
            'productos_comprados' => 0,
            'compras_mes' => 0,
            'message' => 'Error al cargar estadísticas'
        ], 500);
    }
}
    /**
     * Obtener el proveedor con más compras en el período
     */
    private function proveedorMasCompras($inicio, $fin)
    {
        try {
            $resultado = DB::table('compras')
                ->join('proveedores', 'compras.id_proveedor', '=', 'proveedores.id_proveedor')
                ->whereBetween('fecha_compra', [$inicio, $fin])
                ->select('proveedores.nombre', DB::raw('COUNT(*) as total_compras'))
                ->groupBy('proveedores.nombre')
                ->orderBy('total_compras', 'desc')
                ->first();

            return $resultado ? $resultado->nombre : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Obtener el producto más comprado en el período
     */
    private function productoMasComprado($inicio, $fin)
    {
        try {
            $resultado = DB::table('compras')
                ->join('productos', 'compras.id_producto', '=', 'productos.id_producto')
                ->whereBetween('fecha_compra', [$inicio, $fin])
                ->select('productos.nombre', DB::raw('SUM(cantidad) as total_cantidad'))
                ->groupBy('productos.nombre')
                ->orderBy('total_cantidad', 'desc')
                ->first();

            return $resultado ? $resultado->nombre : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

   
/**
 * Registrar movimiento en inventario
 */
private function registrarEnInventario($compra, $producto, $stockAnterior)
{
    try {
        // Obtener nombre del proveedor si existe
        $nombreProveedor = null;
        if ($compra->id_proveedor) {
            $proveedor = Proveedor::find($compra->id_proveedor);
            $nombreProveedor = $proveedor ? $proveedor->nombre : null;
        }

        // Usar el modelo Inventario con los campos correctos
        $inventario = new Inventario();
        $inventario->id_producto = $producto->id_producto;
        $inventario->tipo_movimiento = 'entrada';
        $inventario->cantidad = $compra->cantidad;
        $inventario->stock_anterior = $stockAnterior;
        $inventario->stock_nuevo = $producto->stock;
        $inventario->precio_compra = $compra->precio_unitario;
        $inventario->proveedor = $nombreProveedor;
        $inventario->numero_factura = $compra->numero_factura;
        $inventario->id_venta = null;
        $inventario->fecha_movimiento = $compra->fecha_compra;
        $inventario->notas = "Compra #{$compra->id_compra}" . ($compra->notas ? ' - ' . $compra->notas : '');
        $inventario->userId = Auth::id();  // 👈 IMPORTANTE: userId con Y mayúscula
        $inventario->save();
        
        Log::info('✅ Inventario actualizado para compra: ' . $compra->id_compra);
        
    } catch (\Exception $e) {
        Log::warning('⚠️ No se pudo registrar en inventario: ' . $e->getMessage());
        // No interrumpir el flujo principal
    }
}
 

/**
 * Buscar productos para autocompletado en compras
 */

public function buscarProductos(Request $request)
{
    try {
        $termino = $request->get('termino', '');
        
        \Log::info('🔍 Buscando productos con término: ' . $termino);
        
        if (strlen($termino) < 2) {
            return response()->json([
                'success' => true,
                'productos' => []
            ]);
        }

        // 👇 CORREGIDO: Cambiar precio_compra por precio_venta
        $productos = Producto::where('activo', true)
            ->where(function($query) use ($termino) {
                $query->where('nombre', 'LIKE', "%{$termino}%")
                      ->orWhere('codigo', 'LIKE', "%{$termino}%");
            })
            ->select(
                'id_producto', 
                'nombre', 
                'codigo', 
                'stock', 
                'precio_venta'  // 👈 CAMBIADO: usar precio_venta
            )
            ->orderBy('nombre')
            ->limit(10)
            ->get();

        \Log::info('✅ Productos encontrados: ' . $productos->count());

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);

    } catch (\Exception $e) {
        \Log::error('❌ Error al buscar productos: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al buscar productos',
            'productos' => []
        ], 500);
    }
}



    /**
     * Obtener compras por proveedor
     */
    public function porProveedor($idProveedor)
    {
        try {
            $compras = Compra::with('producto')
                ->where('id_proveedor', $idProveedor)
                ->orderBy('fecha_compra', 'desc')
                ->limit(20)
                ->get();

            $total = $compras->sum('precio_total');
            $cantidadTotal = $compras->sum('cantidad');

            return response()->json([
                'success' => true,
                'data' => $compras,
                'resumen' => [
                    'total_compras' => $compras->count(),
                    'total_invertido' => $total,
                    'total_productos' => $cantidadTotal
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener compras del proveedor'
            ], 500);
        }
    }
}