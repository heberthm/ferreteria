<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PuntoVentaController extends Controller
{
    public function index()
    {
        return view('venta');
    }

    // =============================================
    // 1. FUNCIONES PARA PRODUCTOS
    // =============================================
    
    public function buscarProductos(Request $request)
    {
        $termino = $request->input('termino');
        $categoria = $request->input('categoria', 'todas');
        
        try {
            $query = Producto::query();
            
            // Filtrar por término de búsqueda
            if ($termino) {
                $query->where(function($q) use ($termino) {
                    $q->where('codigo', 'LIKE', "%{$termino}%")
                      ->orWhere('nombre', 'LIKE', "%{$termino}%")
                      ->orWhere('descripcion', 'LIKE', "%{$termino}%");
                });
            }
            
            // Filtrar por categoría
            if ($categoria !== 'todas') {
                $query->where('categoria', $categoria);
            }
            
            // Obtener solo productos con stock disponible
            $query->where('stock', '>', 0);
            
            // Ordenar por nombre
            $query->orderBy('nombre', 'asc');
            
            // Limitar resultados
            $productos = $query->limit(50)->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos,
                'total' => $productos->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar productos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener todos los productos
     */
    public function todosLosProductos()
    {
        try {
            $productos = Producto::orderBy('nombre', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener productos frecuentes
     */
   /**
 * Obtener productos frecuentes (VERSIÓN SIMPLIFICADA)
 */
 public function productosFrecuentes()
    {
        try {
            \Log::info('=== PRODUCTOS FRECUENTES - INICIO ===');
            
            // INTENTO 1: Productos más vendidos (historial de ventas)
            $productos = $this->obtenerProductosMasVendidos(12);
            
            // INTENTO 2: Si no hay productos más vendidos, buscar por stock
            if (empty($productos)) {
                \Log::info('No hay productos vendidos. Intentando productos con stock...');
                $productos = $this->obtenerProductosConStock(12);
            }
            
            // INTENTO 3: Si aún no hay, productos activos
            if (empty($productos)) {
                \Log::info('No hay productos con stock. Intentando productos activos...');
                $productos = $this->obtenerProductosActivos(12);
            }
            
            // INTENTO 4: Último recurso - cualquier producto
            if (empty($productos)) {
                \Log::info('Intentando cualquier producto...');
                $productos = $this->obtenerCualquierProducto(12);
            }
            
            \Log::info('Productos obtenidos: ' . count($productos));
            
            return response()->json([
                'success' => true,
                'productos' => $productos,
                'total' => count($productos),
                'message' => count($productos) > 0 ? 'Productos cargados correctamente' : 'No hay productos disponibles',
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('ERROR en productosFrecuentes: ' . $e->getMessage());
            
            return response()->json([
                'success' => true, // Mantener success: true para que no falle el frontend
                'productos' => [],
                'message' => 'Error al cargar productos: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);
        }
    }
    
    /**
     * INTENTO 1: Productos más vendidos
     */
    private function obtenerProductosMasVendidos($limite = 12)
    {
        try {
            \Log::info('Buscando productos más vendidos...');
            
            // Primero verificamos si hay ventas en la base de datos
            $totalVentas = Venta::where('estado', 'completada')->count();
            
            if ($totalVentas === 0) {
                \Log::info('No hay ventas registradas en la base de datos');
                return [];
            }
            
            $productosMasVendidos = DetalleVenta::select(
                    'productos.id_producto',
                    'productos.codigo',
                    'productos.nombre',
                    'productos.precio',
                    'productos.stock',
                    'productos.categoria',
                    'productos.descripcion',
                    'productos.unidad',
                    'productos.stock_minimo',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_vendido')
                )
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->where('ventas.estado', 'completada')
                ->where('productos.estado', 'activo')
                ->groupBy(
                    'productos.id_producto',
                    'productos.codigo',
                    'productos.nombre',
                    'productos.precio',
                    'productos.stock',
                    'productos.categoria',
                    'productos.descripcion',
                    'productos.unidad',
                    'productos.stock_minimo'
                )
                ->orderBy('total_vendido', 'DESC')
                ->limit($limite)
                ->get();
            
            \Log::info('Productos más vendidos encontrados: ' . $productosMasVendidos->count());
            
            if ($productosMasVendidos->isNotEmpty()) {
                return $productosMasVendidos->map(function($item) {
                    return [
                        'id' => $item->id_producto,
                        'codigo' => $item->codigo,
                        'nombre' => $item->nombre,
                        'precio' => (float) $item->precio,
                        'stock' => (int) $item->stock,
                        'categoria' => $item->categoria,
                        'descripcion' => $item->descripcion,
                        'unidad' => $item->unidad ?? 'unidad',
                        'stock_minimo' => $item->stock_minimo ?? 5,
                        'total_vendido' => (int) $item->total_vendido,
                        'tipo' => 'mas_vendido'
                    ];
                })->toArray();
            }
            
            return [];
            
        } catch (\Exception $e) {
            \Log::error('Error en obtenerProductosMasVendidos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * INTENTO 2: Productos con stock
     */
    private function obtenerProductosConStock($limite = 12)
    {
        try {
            \Log::info('Buscando productos con stock...');
            
            $productos = Producto::where('stock', '>', 0)
                ->where('estado', 'activo')
                ->orderBy('stock', 'DESC') // Los con más stock primero
                ->limit($limite)
                ->get();
            
            \Log::info('Productos con stock encontrados: ' . $productos->count());
            
            if ($productos->isNotEmpty()) {
                return $productos->map(function($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'codigo' => $producto->codigo,
                        'nombre' => $producto->nombre,
                        'precio' => (float) $producto->precio,
                        'stock' => (int) $producto->stock,
                        'categoria' => $producto->categoria,
                        'descripcion' => $producto->descripcion,
                        'unidad' => $producto->unidad ?? 'unidad',
                        'stock_minimo' => $producto->stock_minimo ?? 5,
                        'total_vendido' => 0,
                        'tipo' => 'con_stock'
                    ];
                })->toArray();
            }
            
            return [];
            
        } catch (\Exception $e) {
            \Log::error('Error en obtenerProductosConStock: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * INTENTO 3: Productos activos (sin importar stock)
     */
    private function obtenerProductosActivos($limite = 12)
    {
        try {
            \Log::info('Buscando productos activos...');
            
            $productos = Producto::where('estado', 'activo')
                ->orderBy('nombre', 'ASC')
                ->limit($limite)
                ->get();
            
            \Log::info('Productos activos encontrados: ' . $productos->count());
            
            if ($productos->isNotEmpty()) {
                return $productos->map(function($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'codigo' => $producto->codigo,
                        'nombre' => $producto->nombre,
                        'precio' => (float) $producto->precio,
                        'stock' => (int) $producto->stock,
                        'categoria' => $producto->categoria,
                        'descripcion' => $producto->descripcion,
                        'unidad' => $producto->unidad ?? 'unidad',
                        'stock_minimo' => $producto->stock_minimo ?? 5,
                        'total_vendido' => 0,
                        'tipo' => 'activo'
                    ];
                })->toArray();
            }
            
            return [];
            
        } catch (\Exception $e) {
            \Log::error('Error en obtenerProductosActivos: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * INTENTO 4: Cualquier producto (último recurso)
     */
    private function obtenerCualquierProducto($limite = 12)
    {
        try {
            \Log::info('Buscando cualquier producto...');
            
            $productos = Producto::orderBy('id_producto', 'ASC')
                ->limit($limite)
                ->get();
            
            \Log::info('Productos encontrados: ' . $productos->count());
            
            if ($productos->isNotEmpty()) {
                return $productos->map(function($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'codigo' => $producto->codigo,
                        'nombre' => $producto->nombre,
                        'precio' => (float) $producto->precio,
                        'stock' => (int) $producto->stock,
                        'categoria' => $producto->categoria,
                        'descripcion' => $producto->descripcion,
                        'unidad' => $producto->unidad ?? 'unidad',
                        'stock_minimo' => $producto->stock_minimo ?? 5,
                        'total_vendido' => 0,
                        'tipo' => 'general'
                    ];
                })->toArray();
            }
            
            return [];
            
        } catch (\Exception $e) {
            \Log::error('Error en obtenerCualquierProducto: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * MÉTODO DE DEPURACIÓN: Verificar estado de la base de datos
     */
    public function verificarEstadoBD()
    {
        try {
            $totalProductos = Producto::count();
            $productosActivos = Producto::where('estado', 'activo')->count();
            $productosConStock = Producto::where('stock', '>', 0)->count();
            $totalVentas = Venta::where('estado', 'completada')->count();
            
            return response()->json([
                'success' => true,
                'estadisticas' => [
                    'total_productos' => $totalProductos,
                    'productos_activos' => $productosActivos,
                    'productos_con_stock' => $productosConStock,
                    'ventas_completadas' => $totalVentas,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // =============================================
    // 2. FUNCIONES PARA VENTAS (MEJORADAS)
    // =============================================
    
    /**
     * Generar número de factura automático
     */
  private function generarNumeroFactura()
{
    try {
        // Intentar obtener el último número de factura
        $ultimaFactura = DB::table('ventas')
            ->select('numero_factura')
            ->where('numero_factura', 'LIKE', 'F-%')
            ->orderByRaw('CAST(SUBSTRING(numero_factura, 3) AS UNSIGNED) DESC')
            ->first();
        
        if ($ultimaFactura) {
            // Extraer solo la parte numérica
            $partes = explode('-', $ultimaFactura->numero_factura);
            $ultimoNumero = end($partes);
            $nuevoNumero = (int) $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }
        
        // Formatear con ceros a la izquierda
        return 'F-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
        
    } catch (\Exception $e) {
        // Si hay error, generar número basado en timestamp
        return 'F-' . date('Ymd') . '-' . rand(100, 999);
    }
}
    
    /**
     * Procesar venta (VERSIÓN COMPLETA Y CORREGIDA)
     */
   
public function procesarVenta(Request $request)
{
    \Log::info('📥 Datos recibidos en procesarVenta:', $request->all());
    
    // Validar datos de entrada
    $validator = Validator::make($request->all(), [
        'cliente_id' => 'nullable|exists:clientes,id_cliente',
        'subtotal' => 'required|numeric|min:0',
        'iva' => 'required|numeric|min:0',
        'total' => 'required|numeric|min:0',
        'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,mixto,credito,cheque',
        'tipo_comprobante' => 'required|string|in:ticket,factura,factura_fiscal',
        'referencia_pago' => 'nullable|string|max:100',
        'efectivo_recibido' => 'nullable|numeric|min:0',
        'cambio' => 'nullable|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.producto_id' => 'required|exists:productos,id_producto',
        'items.*.cantidad' => 'required|integer|min:1',
        'items.*.precio' => 'required|numeric|min:0',
        'items.*.subtotal' => 'required|numeric|min:0'
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors' => $validator->errors()
        ], 422);
    }
    
    DB::beginTransaction();
    
    try {
        // 1. VERIFICAR STOCK
        foreach ($request->items as $item) {
            $producto = Producto::find($item['producto_id']);
            
            if (!$producto) {
                throw new \Exception("Producto no encontrado");
            }
            
            if ($producto->stock < $item['cantidad']) {
                throw new \Exception("Stock insuficiente para {$producto->nombre}");
            }
        }
        
        // 2. GENERAR NÚMERO DE FACTURA
        $numeroFactura = $this->generarNumeroFactura();
        
        // 3. PREPARAR DATOS DE PRODUCTOS PARA JSON
        $productosJson = [];
        foreach ($request->items as $item) {
            $producto = Producto::find($item['producto_id']);
            $productosJson[] = [
                'id_producto' => $producto->id_producto,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $item['subtotal']
            ];
        }
        
        // 4. CREAR LA VENTA (CON PRODUCTOS EN JSON)
        $venta = new Venta();
        $venta->numero_factura = $numeroFactura;
        $venta->id_cliente = $request->cliente_id;
        $venta->subtotal = $request->subtotal;
        $venta->iva = $request->iva;
        $venta->total = $request->total;
        $venta->metodo_pago = $request->metodo_pago;
        $venta->tipo_comprobante = $request->tipo_comprobante;
        $venta->referencia_pago = $request->referencia_pago;
        $venta->efectivo_recibido = $request->efectivo_recibido;
        $venta->cambio = $request->cambio;
        $venta->userId = auth()->id() ?? 1;
        $venta->fecha_venta = Carbon::now();
        $venta->estado = 'completada';
        $venta->productos = $productosJson; // ← GUARDAR PRODUCTOS EN JSON
        
        $venta->save();
        
        // 5. CREAR DETALLES DE VENTA
        foreach ($request->items as $item) {
            $detalle = new DetalleVenta();
            $detalle->id_venta = $venta->id_venta;
            $detalle->id_producto = $item['producto_id'];
            $detalle->cantidad = $item['cantidad'];
            $detalle->precio_unitario = $item['precio'];
            $detalle->subtotal = $item['subtotal'];
            $detalle->save();
            
            // Actualizar stock
            $producto = Producto::find($item['producto_id']);
            $producto->stock -= $item['cantidad'];
            $producto->save();
        }
        
        // 6. CREAR PAGO
        $pago = new Pago();
        $pago->id_venta = $venta->id_venta;
        $pago->metodo_pago = $request->metodo_pago;
        $pago->monto = $request->total;
        $pago->referencia = $request->referencia_pago;
        $pago->estado = 'completado';
        $pago->save();
        
        DB::commit();
        
        // 7. CARGAR DATOS PARA LA RESPUESTA
        $venta->load(['cliente', 'detalles.producto', 'pago']);
        
        \Log::info('✅ Venta guardada con productos JSON:', [
            'venta_id' => $venta->id_venta,
            'productos_json' => $venta->productos
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Venta procesada exitosamente',
            'venta_id' => $venta->id_venta,
            'numero_factura' => $venta->numero_factura,
            'venta_completa' => $venta,
            'timestamp' => Carbon::now()->toDateTimeString()
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('❌ Error al procesar venta:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la venta: ' . $e->getMessage()
        ], 500);
    }
}
   
    
    /**
     * Obtener datos de venta para comprobante
     */
  /**
 * Obtener datos de venta para comprobante
 */
public function obtenerVenta($id)
{
    try {
        // Especificar explícitamente las claves foráneas en la carga
        $venta = Venta::with([
            'cliente',
            'detalles' => function($query) {
                $query->with(['producto' => function($q) {
                    $q->select('id_producto', 'codigo', 'nombre', 'descripcion', 'precio');
                }]);
            },
            'pago',
            'usuario' => function($query) {
                $query->select('id', 'name', 'email');
            }
        ])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'venta' => $venta
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Venta no encontrada: ' . $e->getMessage()
        ], 404);
    }
}
    
    /**
     * Actualizar stock individual (para uso en tiempo real)
     */
    public function actualizarStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $producto = Producto::find($request->producto_id);
            
            if ($producto->stock < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $producto->stock
                ], 400);
            }
            
            $producto->stock -= $request->cantidad;
            $producto->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'nuevo_stock' => $producto->stock,
                'producto' => $producto
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar stock: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Revertir venta (cancelación)
     */
    public function revertirVenta($id)
    {
        DB::beginTransaction();
        
        try {
            $venta = Venta::with('detalles')->findOrFail($id);
            
            // Revertir stock de productos
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->id_producto);
                if ($producto) {
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }
            }
            
            // Actualizar estado de la venta
            $venta->estado = 'cancelada';
            $venta->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta revertida exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir venta: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generar reporte de ventas del día
     */
    public function ventasDelDia()
    {
        try {
            $fecha = Carbon::today();
            
            $ventas = Venta::whereDate('fecha_venta', $fecha)
                ->with(['cliente', 'detalles'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $totalVentas = $ventas->sum('total');
            $totalProductos = $ventas->sum(function($venta) {
                return $venta->detalles->sum('cantidad');
            });
            
            return response()->json([
                'success' => true,
                'ventas' => $ventas,
                'estadisticas' => [
                    'total_ventas' => $ventas->count(),
                    'total_monto' => $totalVentas,
                    'total_productos' => $totalProductos,
                    'fecha' => $fecha->format('Y-m-d')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener ventas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // =============================================
    // 3. FUNCIONES PARA COMPROBANTES
    // =============================================
    
    public function generarTicket($ventaId)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'pago'])->find($ventaId);
        
        return view('punto-venta.ticket', compact('venta'));
    }

    public function generarFactura($ventaId)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'pago'])->find($ventaId);
        
        return view('punto-venta.factura', compact('venta'));
    }
    
    /**
     * Generar comprobante PDF
     */
    public function generarPDF($ventaId, $tipo = 'ticket')
    {
        try {
            $venta = Venta::with(['cliente', 'detalles.producto', 'pago', 'usuario'])
                ->findOrFail($ventaId);
            
            $data = [
                'venta' => $venta,
                'empresa' => [
                    'nombre' => 'FERRETERÍA EL MARTILLO',
                    'direccion' => 'Av. Principal #123',
                    'telefono' => '(555) 123-4567',
                    'email' => 'ventas@ferreteria.com',
                    'nit' => 'FME850301XYZ'
                ]
            ];
            
            $pdf = \PDF::loadView("comprobantes.{$tipo}", $data);
            
            return $pdf->download("comprobante-{$venta->numero_factura}.pdf");
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}