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
        // Log de todos los datos recibidos
        \Log::info('📥 Datos recibidos en guardar:', $request->all());
        
        // Validar datos
        $validator = Validator::make($request->all(), [
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
            'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
            'numero_factura' => 'nullable|string|max:100',
            'fecha_compra' => 'required|date',
            'metodo_pago' => 'nullable|string|max:50',
            'notas' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            // Log de errores de validación
            \Log::error('❌ Errores de validación:', $validator->errors()->toArray());
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Resto del código...
        
    } catch (\Exception $e) {
        \Log::error('❌ Error al guardar compra: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la compra: ' . $e->getMessage()
        ], 500);
    }
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
    public function estadisticas()
    {
        try {
            $hoy = now()->toDateString();
            $inicioMes = now()->startOfMonth()->toDateString();
            $finMes = now()->endOfMonth()->toDateString();
            $inicioSemana = now()->startOfWeek()->toDateString();
            $finSemana = now()->endOfWeek()->toDateString();

            $estadisticas = [
                'success' => true,
                'compras_hoy' => Compra::whereDate('fecha_compra', $hoy)->count(),
                'compras_semana' => Compra::whereBetween('fecha_compra', [$inicioSemana, $finSemana])->count(),
                'compras_mes' => Compra::whereBetween('fecha_compra', [$inicioMes, $finMes])->count(),
                'total_invertido_hoy' => Compra::whereDate('fecha_compra', $hoy)->sum('precio_total'),
                'total_invertido_semana' => Compra::whereBetween('fecha_compra', [$inicioSemana, $finSemana])->sum('precio_total'),
                'total_invertido_mes' => Compra::whereBetween('fecha_compra', [$inicioMes, $finMes])->sum('precio_total'),
                'productos_comprados_hoy' => Compra::whereDate('fecha_compra', $hoy)->sum('cantidad'),
                'productos_comprados_semana' => Compra::whereBetween('fecha_compra', [$inicioSemana, $finSemana])->sum('cantidad'),
                'productos_comprados_mes' => Compra::whereBetween('fecha_compra', [$inicioMes, $finMes])->sum('cantidad'),
                'proveedor_mas_compras' => $this->proveedorMasCompras($inicioMes, $finMes),
                'producto_mas_comprado' => $this->productoMasComprado($inicioMes, $finMes)
            ];

            return response()->json($estadisticas);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
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
        $inventario = new Inventario();
        $inventario->id_producto = $producto->id_producto;
        $inventario->tipo_movimiento = 'entrada_compra';
        $inventario->cantidad = $compra->cantidad;
        $inventario->stock_anterior = $stockAnterior;
        $inventario->stock_nuevo = $producto->stock;
        $inventario->precio_unitario = $compra->precio_unitario;
        $inventario->precio_total = $compra->precio_total;
        $inventario->id_proveedor = $compra->id_proveedor;
        $inventario->numero_factura = $compra->numero_factura;
        $inventario->fecha_movimiento = $compra->fecha_compra;
        $inventario->id_usuario = Auth::id();
        $inventario->notas = "Compra #{$compra->id_compra}";
        $inventario->save();
        
        \Log::info('Inventario actualizado para compra: ' . $compra->id_compra);
        
    } catch (\Exception $e) {
        \Log::warning('No se pudo registrar en inventario: ' . $e->getMessage());
        // No lanzamos excepción para no interrumpir el flujo principal
    }
}
    /**
     * Registrar anulación en inventario
     */
    private function registrarAnulacionInventario($compra, $producto, $stockAnterior)
    {
        try {
            $inventario = new Inventario();
            $inventario->id_producto = $producto->id_producto;
            $inventario->tipo_movimiento = 'anulacion_compra';
            $inventario->cantidad = $compra->cantidad;
            $inventario->stock_anterior = $stockAnterior;
            $inventario->stock_nuevo = $producto->stock;
            $inventario->precio_unitario = $compra->precio_unitario;
            $inventario->precio_total = $compra->precio_total;
            $inventario->id_proveedor = $compra->id_proveedor;
            $inventario->numero_factura = $compra->numero_factura;
            $inventario->fecha_movimiento = now();
            $inventario->id_usuario = Auth::id();
            $inventario->notas = "Anulación compra #{$compra->id_compra}";
            $inventario->save();
        } catch (\Exception $e) {
            Log::warning('No se pudo registrar anulación en inventario: ' . $e->getMessage());
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