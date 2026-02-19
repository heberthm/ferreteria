<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class HistorialVentasController extends Controller
{
    /**
     * Mostrar la vista principal del historial de ventas
     */
    public function index()
    {
        return view('historial_ventas');
    }

    /**
     * Obtener datos para DataTables
     */
    public function getVentasData(Request $request)
    {
        try {
            \Log::info('Iniciando getVentasData', $request->all());

            // Subconsulta para contar productos
            $subQuery = DB::table('detalle_ventas')
                ->select('id_venta', DB::raw('SUM(cantidad) as total_productos'))
                ->groupBy('id_venta');

            // Construir query base
            $query = DB::table('ventas')
                ->select([
                    'ventas.id_venta',
                    'ventas.numero_factura',
                    'ventas.fecha_venta',
                    'ventas.total',
                    'ventas.estado',
                    'ventas.metodo_pago',
                    'ventas.id_cliente',
                    'ventas.userId',
                    'clientes.nombre as cliente_nombre',
                    'clientes.cedula as cliente_cedula',
                    'users.name as vendedor_nombre',
                    DB::raw('COALESCE(productos_count.total_productos, 0) as total_productos')
                ])
                ->leftJoin('clientes', 'ventas.id_cliente', '=', 'clientes.id_cliente')
                ->leftJoin('users', 'ventas.userId', '=', 'users.id')
                ->leftJoinSub($subQuery, 'productos_count', function($join) {
                    $join->on('ventas.id_venta', '=', 'productos_count.id_venta');
                });

            // Aplicar filtros
            if ($request->filled('fecha_desde')) {
                $query->whereDate('ventas.fecha_venta', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('ventas.fecha_venta', '<=', $request->fecha_hasta);
            }

            if ($request->filled('estado')) {
                $query->where('ventas.estado', $request->estado);
            }

            if ($request->filled('metodo_pago')) {
                $query->where('ventas.metodo_pago', $request->metodo_pago);
            }

            if ($request->filled('cliente')) {
                $query->where(function($q) use ($request) {
                    $q->where('clientes.nombre', 'like', '%' . $request->cliente . '%')
                      ->orWhere('clientes.cedula', 'like', '%' . $request->cliente . '%');
                });
            }

            if ($request->filled('factura')) {
                $query->where('ventas.numero_factura', 'like', '%' . $request->factura . '%');
            }

            // Usar DataTables
            return DataTables::of($query)
                ->addColumn('fecha_formateada', function($row) {
                    return Carbon::parse($row->fecha_venta)->format('d/m/Y');
                })
                ->addColumn('hora_formateada', function($row) {
                    return Carbon::parse($row->fecha_venta)->format('H:i');
                })
                ->editColumn('cliente_nombre', function($row) {
                    return $row->cliente_nombre ?? 'Cliente General';
                })
                ->editColumn('vendedor_nombre', function($row) {
                    return $row->vendedor_nombre ?? 'N/A';
                })
                ->editColumn('total', function($row) {
                    return '$' . number_format($row->total, 0, ',', '.');
                })
                ->editColumn('estado', function($row) {
                    $badgeClass = 'secondary';
                    $estadoText = $row->estado ?? 'desconocido';
                    
                    switch($row->estado) {
                        case 'completada':
                            $badgeClass = 'success';
                            $estadoText = 'Completada';
                            break;
                        case 'pendiente':
                            $badgeClass = 'warning';
                            $estadoText = 'Pendiente';
                            break;
                        case 'cancelada':
                            $badgeClass = 'danger';
                            $estadoText = 'Cancelada';
                            break;
                    }
                    
                    return '<span class="badge badge-' . $badgeClass . '">' . $estadoText . '</span>';
                })
                ->editColumn('metodo_pago', function($row) {
                    if (!$row->metodo_pago) return 'N/A';
                    
                    $icon = '';
                    switch(strtolower($row->metodo_pago)) {
                        case 'efectivo':
                            $icon = '💵 ';
                            break;
                        case 'tarjeta':
                            $icon = '💳 ';
                            break;
                        case 'transferencia':
                            $icon = '🏦 ';
                            break;
                    }
                    return $icon . ucfirst($row->metodo_pago);
                })
                ->addColumn('acciones', function($row) {
                    // Botón eliminar solo visible para ventas no canceladas
                    $btnEliminar = '';
                    if ($row->estado !== 'cancelada') {
                        $btnEliminar = '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarVenta('.$row->id_venta.')" title="Eliminar factura y restablecer stock">
                                            <i class="fas fa-trash"></i>
                                        </button>';
                    }
                    
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-info btn-sm" onclick="verDetalleVenta('.$row->id_venta.')" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="imprimirTicket('.$row->id_venta.')" title="Imprimir ticket">
                                <i class="fas fa-print"></i>
                            </button>
                            ' . $btnEliminar . '
                        </div>
                    ';
                })
                ->filterColumn('cliente_nombre', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('clientes.nombre', 'like', '%' . $keyword . '%')
                          ->orWhere('clientes.cedula', 'like', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('vendedor_nombre', function($query, $keyword) {
                    $query->where('users.name', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('total_productos', function($query, $order) {
                    $query->orderBy('total_productos', $order);
                })
                ->rawColumns(['estado', 'metodo_pago', 'acciones'])
                ->make(true);

        } catch (\Exception $e) {
            \Log::error('Error en getVentasData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error al cargar los datos',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Obtener detalle completo de una venta
     */
    public function getDetalleVenta($id)
    {
        try {
            \Log::info('=== INICIO getDetalleVenta ===');
            \Log::info('ID recibido: ' . $id);
            
            // Verificar que el ID es válido
            if (!$id || !is_numeric($id)) {
                \Log::error('ID inválido: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'ID de venta inválido'
                ], 400);
            }

            // Buscar la venta
            $venta = DB::table('ventas')->where('id_venta', $id)->first();
            
            if (!$venta) {
                \Log::error('Venta no encontrada: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            // Obtener cliente
            $cliente = null;
            if ($venta->id_cliente) {
                $cliente = DB::table('clientes')->where('id_cliente', $venta->id_cliente)->first();
            }
            
            // Obtener vendedor
            $vendedor = null;
            if ($venta->userId) {
                $vendedor = DB::table('users')->where('id', $venta->userId)->first();
            }
            
            // Obtener detalles
            $detalles = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->where('detalle_ventas.id_venta', $id)
                ->select(
                    'productos.nombre',
                    'productos.codigo',
                    'detalle_ventas.cantidad',
                    'detalle_ventas.precio_unitario',
                    'detalle_ventas.subtotal'
                )
                ->get();

            // Calcular el total sumando los subtotales
            $totalCalculadoDetalles = $detalles->sum(function($detalle) {
                return floatval($detalle->subtotal);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'venta' => [
                        'id_venta' => $venta->id_venta,
                        'numero_factura' => $venta->numero_factura,
                        'fecha' => Carbon::parse($venta->fecha_venta)->format('d/m/Y'),
                        'hora' => Carbon::parse($venta->fecha_venta)->format('H:i:s'),
                        'total' => floatval($venta->total),
                        'estado' => $venta->estado,
                        'metodo_pago' => $venta->metodo_pago,
                        'observaciones' => $venta->observaciones ?? ''
                    ],
                    'cliente' => $cliente ? [
                        'nombre' => $cliente->nombre,
                        'cedula' => $cliente->cedula ?? 'N/A'
                    ] : null,
                    'vendedor' => $vendedor ? [
                        'nombre' => $vendedor->name
                    ] : null,
                    'detalles' => $detalles,
                    'total_detalles' => $totalCalculadoDetalles
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('=== ERROR en getDetalleVenta ===');
            \Log::error('Mensaje: ' . $e->getMessage());
            \Log::error('Archivo: ' . $e->getFile());
            \Log::error('Línea: ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Imprimir ticket de venta
     */
    public function imprimirTicket($id)
    {
        try {
            // Obtener datos de la venta igual que getDetalleVenta
            $venta = DB::table('ventas')->where('id_venta', $id)->first();
            
            if (!$venta) {
                abort(404, 'Venta no encontrada');
            }

            $cliente = null;
            if ($venta->id_cliente) {
                $cliente = DB::table('clientes')->where('id_cliente', $venta->id_cliente)->first();
            }
            
            $vendedor = null;
            if ($venta->userId) {
                $vendedor = DB::table('users')->where('id', $venta->userId)->first();
            }
            
            $detalles = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->where('detalle_ventas.id_venta', $id)
                ->select(
                    'productos.nombre',
                    'productos.codigo',
                    'detalle_ventas.cantidad',
                    'detalle_ventas.precio_unitario',
                    'detalle_ventas.subtotal'
                )
                ->get();

            // Retornar vista de ticket (crea esta vista o redirige al detalle)
            return view('ventas.ticket', compact('venta', 'cliente', 'vendedor', 'detalles'));

        } catch (\Exception $e) {
            \Log::error('Error en imprimirTicket: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el ticket');
        }
    }

    /**
     * Cancelar una venta
     */
    public function cancelarVenta(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::where('id_venta', $id)->firstOrFail();

            if ($venta->estado !== 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden cancelar ventas en estado pendiente'
                ]);
            }

            $venta->estado = 'cancelada';
            $venta->observaciones = ($venta->observaciones ? $venta->observaciones . "\n\n" : '') . 
                '[CANCELADA] - ' . Carbon::now()->format('d/m/Y H:i') . ' por ' . (Auth::check() ? Auth::user()->name : 'Sistema');
            $venta->save();

            // Devolver productos al stock
            $detalles = DB::table('detalle_ventas')->where('id_venta', $id)->get();
            foreach ($detalles as $detalle) {
                $producto = Producto::where('id_producto', $detalle->id_producto)->first();
                if ($producto) {
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta cancelada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en cancelarVenta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la venta'
            ], 500);
        }
    }
    
    /**
     * Eliminar una venta y restablecer el stock de los productos
     */
    public function eliminarVenta($id)
    {
        try {
            DB::beginTransaction();
            
            \Log::info('Intentando eliminar venta ID: ' . $id);
            
            // Buscar la venta
            $venta = Venta::where('id_venta', $id)->first();
            
            if (!$venta) {
                \Log::error('Venta no encontrada: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            // Verificar que la venta no esté cancelada
            if ($venta->estado === 'cancelada') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una venta cancelada'
                ], 400);
            }

            // Obtener los detalles de la venta
            $detalles = DB::table('detalle_ventas')
                ->where('id_venta', $id)
                ->get();

            // Restablecer el stock de cada producto
            $productos_actualizados = 0;
            foreach ($detalles as $detalle) {
                $producto = Producto::where('id_producto', $detalle->id_producto)->first();
                if ($producto) {
                    // Incrementar el stock en la cantidad vendida
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                    $productos_actualizados++;
                    
                    \Log::info('Stock restablecido - Producto ID: ' . $producto->id_producto . 
                              ', Nuevo stock: ' . $producto->stock . 
                              ', Cantidad devuelta: ' . $detalle->cantidad);
                }
            }
            
            // Registrar la eliminación
            $observacion = '[ELIMINADA] - ' . Carbon::now()->format('d/m/Y H:i:s') . 
                           ' por ' . (Auth::check() ? Auth::user()->name : 'Sistema') . 
                           ' - Stock restablecido: ' . $productos_actualizados . ' productos';
            
            \Log::info('Venta eliminada: ' . $venta->numero_factura . ' - ' . $observacion);
            
            // Eliminar los detalles primero
            DB::table('detalle_ventas')->where('id_venta', $id)->delete();
            
            // Eliminar la venta
            $venta->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada exitosamente. Stock restablecido para ' . $productos_actualizados . ' productos.',
                'productos_restablecidos' => $productos_actualizados
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al eliminar venta: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar reporte de ventas
     */
    public function exportarReporte(Request $request)
    {
        try {
            $query = Venta::select('ventas.*')
                ->leftJoin('clientes', 'ventas.id_cliente', '=', 'clientes.id_cliente')
                ->leftJoin('users', 'ventas.userId', '=', 'users.id');

            if ($request->filled('fecha_inicio')) {
                $query->whereDate('ventas.fecha_venta', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $query->whereDate('ventas.fecha_venta', '<=', $request->fecha_fin);
            }

            $ventas = $query->orderBy('ventas.fecha_venta', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Función de exportación lista',
                'count' => $ventas->count(),
                'total' => $ventas->sum('total')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en exportarReporte: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte'
            ], 500);
        }
    }

    /**
     * Ver todas las ventas (vista alternativa)
     */
    public function ventasTodas()
    {
        try {
            $ventas = Venta::with(['cliente', 'user'])
                ->orderBy('fecha_venta', 'desc')
                ->paginate(20);

            return view('ventas.todas', compact('ventas'));

        } catch (\Exception $e) {
            \Log::error('Error en ventasTodas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las ventas');
        }
    }
}