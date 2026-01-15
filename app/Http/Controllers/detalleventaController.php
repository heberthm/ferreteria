<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DetalleVentasController extends Controller
{
    /**
     * Mostrar detalles de una venta específica
     */
    public function show($ventaId)
    {
        try {
            // Verificar que la venta existe
            $venta = Venta::findOrFail($ventaId);
            
            // Obtener detalles de la venta
            $detalles = DetalleVenta::where('id_venta', $ventaId)
                ->with('producto')
                ->get();
            
            // Si no hay detalles en la tabla separada, buscar en el JSON de la venta
            if ($detalles->isEmpty() && !empty($venta->productos)) {
                $productosJson = json_decode($venta->productos, true);
                
                // Convertir JSON a formato similar a detalles
                $detalles = collect($productosJson)->map(function($producto) {
                    return (object)[
                        'id_producto' => $producto['id'],
                        'cantidad' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio_unitario'],
                        'subtotal' => $producto['subtotal'],
                        'producto' => (object)[
                            'codigo' => $producto['codigo'],
                            'nombre' => $producto['nombre'],
                            'categoria' => $producto['categoria']
                        ]
                    ];
                });
            }
            
            return response()->json([
                'success' => true,
                'venta' => $venta,
                'detalles' => $detalles
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalles de venta: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Agregar producto a una venta existente
     */
    public function agregarProducto(Request $request, $ventaId)
    {
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id_producto',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Verificar que la venta existe y no está cancelada
            $venta = Venta::where('id_venta', $ventaId)
                ->where('estado', '!=', 'cancelada')
                ->firstOrFail();
            
            // Verificar stock del producto
            $producto = Producto::findOrFail($request->producto_id);
            
            if ($producto->stock < $request->cantidad) {
                throw new \Exception("Stock insuficiente. Disponible: {$producto->stock}");
            }
            
            // Verificar si el producto ya está en la venta
            $detalleExistente = DetalleVenta::where('id_venta', $ventaId)
                ->where('id_producto', $request->producto_id)
                ->first();
            
            if ($detalleExistente) {
                // Actualizar cantidad si ya existe
                $detalleExistente->cantidad += $request->cantidad;
                $detalleExistente->subtotal = $detalleExistente->cantidad * $detalleExistente->precio_unitario;
                $detalleExistente->save();
            } else {
                // Crear nuevo detalle
                $detalle = new DetalleVenta();
                $detalle->id_venta = $ventaId;
                $detalle->id_producto = $request->producto_id;
                $detalle->cantidad = $request->cantidad;
                $detalle->precio_unitario = $request->precio_unitario;
                $detalle->subtotal = $request->cantidad * $request->precio_unitario;
                $detalle->save();
            }
            
            // Actualizar stock del producto
            $producto->stock -= $request->cantidad;
            $producto->save();
            
            // Recalcular totales de la venta
            $this->recalcularTotalesVenta($ventaId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado a la venta exitosamente',
                'detalle' => $detalleExistente ?? $detalle
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar cantidad de un producto en la venta
     */
    public function actualizarCantidad(Request $request, $ventaId, $detalleId)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Verificar que la venta existe y no está cancelada
            $venta = Venta::where('id_venta', $ventaId)
                ->where('estado', '!=', 'cancelada')
                ->firstOrFail();
            
            // Obtener el detalle
            $detalle = DetalleVenta::where('id_detalle_venta', $detalleId)
                ->where('id_venta', $ventaId)
                ->firstOrFail();
            
            // Obtener el producto
            $producto = Producto::findOrFail($detalle->id_producto);
            
            // Calcular diferencia de cantidad
            $diferencia = $request->cantidad - $detalle->cantidad;
            
            // Verificar stock si se aumenta la cantidad
            if ($diferencia > 0 && $producto->stock < $diferencia) {
                throw new \Exception("Stock insuficiente. Disponible: {$producto->stock}");
            }
            
            // Actualizar detalle
            $detalle->cantidad = $request->cantidad;
            $detalle->subtotal = $request->cantidad * $detalle->precio_unitario;
            $detalle->save();
            
            // Actualizar stock del producto
            $producto->stock -= $diferencia;
            $producto->save();
            
            // Recalcular totales de la venta
            $this->recalcularTotalesVenta($ventaId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada exitosamente',
                'detalle' => $detalle
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cantidad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar producto de una venta
     */
    public function eliminarProducto($ventaId, $detalleId)
    {
        DB::beginTransaction();
        
        try {
            // Verificar que la venta existe y no está cancelada
            $venta = Venta::where('id_venta', $ventaId)
                ->where('estado', '!=', 'cancelada')
                ->firstOrFail();
            
            // Obtener el detalle
            $detalle = DetalleVenta::where('id_detalle_venta', $detalleId)
                ->where('id_venta', $ventaId)
                ->firstOrFail();
            
            // Obtener el producto para revertir stock
            $producto = Producto::find($detalle->id_producto);
            
            if ($producto) {
                $producto->stock += $detalle->cantidad;
                $producto->save();
            }
            
            // Eliminar el detalle
            $detalle->delete();
            
            // Recalcular totales de la venta
            $this->recalcularTotalesVenta($ventaId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado de la venta exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalcular totales de una venta
     */
    private function recalcularTotalesVenta($ventaId)
    {
        try {
            $venta = Venta::findOrFail($ventaId);
            
            // Calcular subtotal, iva y total
            $detalles = DetalleVenta::where('id_venta', $ventaId)->get();
            
            $subtotal = $detalles->sum('subtotal');
            $iva = $subtotal * 0.16; // Suponiendo 16% de IVA
            $total = $subtotal + $iva;
            
            // Actualizar venta
            $venta->subtotal = $subtotal;
            $venta->iva = $iva;
            $venta->total = $total;
            $venta->save();
            
            return true;
            
        } catch (\Exception $e) {
            throw new \Exception("Error al recalcular totales: " . $e->getMessage());
        }
    }

    /**
     * Obtener productos más vendidos (reporte)
     */
    public function productosMasVendidos(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            $limit = $request->input('limit', 10);
            
            $productos = DetalleVenta::select(
                    'productos.id_producto',
                    'productos.codigo',
                    'productos.nombre',
                    'productos.categoria',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                    DB::raw('SUM(detalle_ventas.subtotal) as total_ventas')
                )
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->whereBetween('ventas.fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('ventas.estado', 'completada')
                ->groupBy('productos.id_producto', 'productos.codigo', 'productos.nombre', 'productos.categoria')
                ->orderByDesc('total_vendido')
                ->limit($limit)
                ->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos,
                'periodo' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener ventas por categoría (reporte)
     */
    public function ventasPorCategoria(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            
            $categorias = DetalleVenta::select(
                    'productos.categoria',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_unidades'),
                    DB::raw('SUM(detalle_ventas.subtotal) as total_ventas'),
                    DB::raw('COUNT(DISTINCT detalle_ventas.id_venta) as total_ventas_count')
                )
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->whereBetween('ventas.fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('ventas.estado', 'completada')
                ->groupBy('productos.categoria')
                ->orderByDesc('total_ventas')
                ->get();
            
            return response()->json([
                'success' => true,
                'categorias' => $categorias,
                'periodo' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de ventas de un producto
     */
    public function historialProducto($productoId)
    {
        try {
            $producto = Producto::findOrFail($productoId);
            
            $historial = DetalleVenta::select(
                    'detalle_ventas.*',
                    'ventas.fecha_venta',
                    'ventas.numero_factura',
                    'clientes.nombre as cliente_nombre'
                )
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->leftJoin('clientes', 'ventas.id_cliente', '=', 'clientes.id_cliente')
                ->where('detalle_ventas.id_producto', $productoId)
                ->where('ventas.estado', 'completada')
                ->orderBy('ventas.fecha_venta', 'desc')
                ->limit(50)
                ->get();
            
            // Estadísticas del producto
            $estadisticas = [
                'total_vendido' => $historial->sum('cantidad'),
                'total_ventas' => $historial->sum('subtotal'),
                'promedio_venta' => $historial->avg('cantidad'),
                'primera_venta' => $historial->min('fecha_venta'),
                'ultima_venta' => $historial->max('fecha_venta')
            ];
            
            return response()->json([
                'success' => true,
                'producto' => $producto,
                'historial' => $historial,
                'estadisticas' => $estadisticas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar reporte detallado de ventas por periodo
     */
    public function reporteDetallado(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(7)->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            
            // Obtener ventas del periodo
            $ventas = Venta::with(['cliente', 'detalles.producto'])
                ->whereBetween('fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'completada')
                ->orderBy('fecha_venta', 'desc')
                ->get();
            
            // Calcular resumen
            $resumen = [
                'total_ventas' => $ventas->count(),
                'total_monto' => $ventas->sum('total'),
                'total_productos' => $ventas->sum(function($venta) {
                    return $venta->detalles->sum('cantidad');
                }),
                'promedio_venta' => $ventas->avg('total'),
                'venta_maxima' => $ventas->max('total'),
                'venta_minima' => $ventas->min('total')
            ];
            
            // Ventas por método de pago
            $ventasPorMetodo = $ventas->groupBy('metodo_pago')->map(function($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'monto_total' => $grupo->sum('total')
                ];
            });
            
            return response()->json([
                'success' => true,
                'ventas' => $ventas,
                'resumen' => $resumen,
                'ventas_por_metodo' => $ventasPorMetodo,
                'periodo' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar reporte a CSV
     */
    public function exportarCSV(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            
            // Obtener detalles de ventas
            $detalles = DetalleVenta::select(
                    'ventas.numero_factura',
                    'ventas.fecha_venta',
                    'clientes.nombre as cliente',
                    'productos.codigo',
                    'productos.nombre as producto',
                    'productos.categoria',
                    'detalle_ventas.cantidad',
                    'detalle_ventas.precio_unitario',
                    'detalle_ventas.subtotal',
                    'ventas.metodo_pago'
                )
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->leftJoin('clientes', 'ventas.id_cliente', '=', 'clientes.id_cliente')
                ->whereBetween('ventas.fecha_venta', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('ventas.estado', 'completada')
                ->orderBy('ventas.fecha_venta', 'desc')
                ->get();
            
            // Crear archivo CSV
            $filename = "reporte_ventas_{$fechaInicio}_al_{$fechaFin}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\""
            ];
            
            $callback = function() use ($detalles) {
                $file = fopen('php://output', 'w');
                
                // Encabezados
                fputcsv($file, [
                    'Número Factura',
                    'Fecha Venta',
                    'Cliente',
                    'Código Producto',
                    'Producto',
                    'Categoría',
                    'Cantidad',
                    'Precio Unitario',
                    'Subtotal',
                    'Método de Pago'
                ]);
                
                // Datos
                foreach ($detalles as $detalle) {
                    fputcsv($file, [
                        $detalle->numero_factura,
                        $detalle->fecha_venta,
                        $detalle->cliente ?: 'Consumidor Final',
                        $detalle->codigo,
                        $detalle->producto,
                        $detalle->categoria,
                        $detalle->cantidad,
                        number_format($detalle->precio_unitario, 2),
                        number_format($detalle->subtotal, 2),
                        $detalle->metodo_pago
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas diarias de ventas
     */
    public function estadisticasDiarias()
    {
        try {
            $fechaHoy = Carbon::today();
            $fechaAyer = Carbon::yesterday();
            
            // Ventas de hoy
            $ventasHoy = Venta::whereDate('fecha_venta', $fechaHoy)
                ->where('estado', 'completada')
                ->get();
            
            // Ventas de ayer
            $ventasAyer = Venta::whereDate('fecha_venta', $fechaAyer)
                ->where('estado', 'completada')
                ->get();
            
            // Calcular estadísticas
            $estadisticas = [
                'hoy' => [
                    'total_ventas' => $ventasHoy->count(),
                    'total_monto' => $ventasHoy->sum('total'),
                    'promedio_venta' => $ventasHoy->avg('total') ?: 0
                ],
                'ayer' => [
                    'total_ventas' => $ventasAyer->count(),
                    'total_monto' => $ventasAyer->sum('total'),
                    'promedio_venta' => $ventasAyer->avg('total') ?: 0
                ],
                'comparacion' => [
                    'ventas' => $ventasHoy->count() - $ventasAyer->count(),
                    'monto' => $ventasHoy->sum('total') - $ventasAyer->sum('total')
                ]
            ];
            
            // Productos más vendidos hoy
            $productosMasVendidos = DetalleVenta::select(
                    'productos.nombre',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_vendido')
                )
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                ->whereDate('ventas.fecha_venta', $fechaHoy)
                ->where('ventas.estado', 'completada')
                ->groupBy('productos.id_producto', 'productos.nombre')
                ->orderByDesc('total_vendido')
                ->limit(5)
                ->get();
            
            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas,
                'productos_mas_vendidos' => $productosMasVendidos,
                'fecha' => $fechaHoy->format('Y-m-d')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}