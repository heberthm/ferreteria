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
    public function productosFrecuentes()
    {
        try {
            // Obtener los productos más vendidos (últimos 30 días)
            $fechaInicio = Carbon::now()->subDays(30);
            
            $productos = DB::table('productos as p')
                ->leftJoin('detalle_ventas as dv', 'p.id_producto', '=', 'dv.id_producto')
                ->leftJoin('ventas as v', 'dv.id_venta', '=', 'v.id_venta')
                ->select('p.*', DB::raw('COALESCE(SUM(dv.cantidad), 0) as total_vendido'))
                ->where(function($query) use ($fechaInicio) {
                    $query->where('v.fecha_venta', '>=', $fechaInicio)
                          ->orWhereNull('v.fecha_venta');
                })
                ->where('p.stock', '>', 0)
                ->groupBy('p.id_producto')
                ->orderBy('total_vendido', 'desc')
                ->orderBy('p.nombre', 'asc')
                ->limit(12)
                ->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos frecuentes: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar stock de producto
     */
    public function verificarStock(Request $request)
    {
        $productoId = $request->input('id_producto');
        $cantidadSolicitada = $request->input('cantidad', 1);
        
        try {
            $producto = Producto::findOrFail($productoId);
            
            $stockDisponible = $producto->stock >= $cantidadSolicitada;
            
            return response()->json([
                'success' => true,
                'disponible' => $stockDisponible,
                'stock_actual' => $producto->stock,
                'producto' => $producto
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
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