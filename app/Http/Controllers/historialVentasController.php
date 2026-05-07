<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Venta;
use App\Exports\VentasExport; 
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cliente;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\User;
use App\Models\Producto;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Helpers\NegocioHelper;
use App\Helpers\ConfiguracionHelper;

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
                    $config = ConfiguracionHelper::getGeneralConfig();
                    $simbolo = $config['simbolo_moneda'] ?? '$';
                    return $simbolo . ' ' . number_format($row->total, 0, ',', '.');
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
                    $btnEliminar = '';
                    if ($row->estado !== 'cancelada' && $row->estado !== 'eliminada') {
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
                                <i class="fas fa-receipt"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="imprimirFactura('.$row->id_venta.')" title="Imprimir factura completa">
                                <i class="fas fa-file-invoice"></i>
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
            
            return response()->json([
                'error' => 'Error al cargar los datos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle completo de una venta
     */
public function getDetalleVenta($id)
{
    try {
        $venta = Venta::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('id_venta', $id)
            ->first();
        
        if (!$venta) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada'
            ], 404);
        }

        $configGeneral = ConfiguracionHelper::getGeneralConfig();
        $simbolo = $configGeneral['simbolo_moneda'] ?? '$';
        
        // Obtener datos de la empresa
        $datosEmpresa = NegocioHelper::getDatosEmpresa();

        // Calcular valores correctamente
        $subtotal = floatval($venta->subtotal);
        $iva = floatval($venta->iva);
        $total = floatval($venta->total);
        
        // Determinar si hay descuento (si subtotal + IVA > total)
        $descuento = 0;
        $subtotalConIva = $subtotal + $iva;
        if ($subtotalConIva > $total) {
            $descuento = $subtotalConIva - $total;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'venta' => [
                    'id_venta' => $venta->id_venta,
                    'numero_factura' => $venta->numero_factura,
                    'fecha' => Carbon::parse($venta->fecha_venta)->format('d/m/Y'),
                    'hora' => Carbon::parse($venta->fecha_venta)->format('H:i:s'),
                    'subtotal' => $simbolo . ' ' . number_format($subtotal, 0),
                    'subtotal_numero' => $subtotal,
                    'iva' => $simbolo . ' ' . number_format($iva, 0),
                    'iva_numero' => $iva,
                    'descuento' => $simbolo . ' ' . number_format($descuento, 0),
                    'descuento_numero' => $descuento,
                    'total' => $simbolo . ' ' . number_format($total, 0),
                    'total_numero' => $total,
                    'estado' => $venta->estado,
                    'metodo_pago' => $venta->metodo_pago,
                    'efectivo_recibido' => $venta->efectivo_recibido ? $simbolo . ' ' . number_format($venta->efectivo_recibido, 0) : null,
                    'cambio' => $venta->cambio ? $simbolo . ' ' . number_format($venta->cambio, 0) : null,
                    'observaciones' => $venta->observaciones ?? ''
                ],
                'cliente' => $venta->cliente ? [
                    'nombre' => $venta->cliente->nombre,
                    'cedula' => $venta->cliente->cedula ?? 'N/A',
                    'telefono' => $venta->cliente->telefono ?? 'N/A',
                    'direccion' => $venta->cliente->direccion ?? 'N/A'
                ] : null,
                'usuario' => $venta->usuario ? [
                    'nombre' => $venta->usuario->name
                ] : null,
                'detalles' => $venta->detalles->map(function($detalle) use ($simbolo) {
                    return [
                        'nombre' => $detalle->producto->nombre,
                        'codigo' => $detalle->producto->codigo ?? 'N/A',
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => floatval($detalle->precio_unitario),
                        'precio_formateado' => $simbolo . ' ' . number_format($detalle->precio_unitario, 0),
                        'subtotal' => floatval($detalle->subtotal),
                        'subtotal_formateado' => $simbolo . ' ' . number_format($detalle->subtotal, 0)
                    ];
                }),
                'empresa' => [
                    'nombre' => $datosEmpresa['nombre'] ?? 'SUPERMERCADO XYZ',
                    'nit' => $datosEmpresa['nit'] ?? '123456789-0',
                    'telefono' => $datosEmpresa['telefono'] ?? '(601) 123-4567',
                    'direccion' => $datosEmpresa['direccion'] ?? 'Calle 123 #45-67',
                    'email' => $datosEmpresa['email'] ?? 'info@superxyz.com',
                    'mensaje' => $datosEmpresa['mensaje_factura'] ?? '¡Gracias por su compra!',
                    'logo_url' => $datosEmpresa['logo_url'] ?? null
                ]
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error en getDetalleVenta: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar el detalle: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Imprimir ticket térmico de venta
     */
    public function imprimirTicket($id)
    {
        try {
            $venta = Venta::with(['cliente', 'usuario', 'detalles.producto'])
                ->where('id_venta', $id)
                ->firstOrFail();
            
            // Obtener configuración de facturación
            $configFacturacion = ConfiguracionHelper::getFacturacionConfig();
            
            // Generar HTML del ticket usando NegocioHelper
            $html = NegocioHelper::generarTicketHTML($venta, $configFacturacion);
            
            // Determinar tamaño de papel según configuración
            $papel = $configFacturacion['tamaño_papel'];
            
            if ($papel == 'thermal') {
                // Para impresora térmica
                return view('ventas.ticket_thermal', compact('html'));
            } else {
                // Para impresión normal
                return view('ventas.ticket_normal', compact('html'));
            }
            
        } catch (\Exception $e) {
            \Log::error('Error en imprimirTicket: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar el ticket: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al generar el ticket');
        }
    }

    /**
     * Imprimir factura completa (PDF)
     */
    public function imprimirFactura($id)
    {
        try {
            $venta = Venta::with(['cliente', 'usuario', 'detalles.producto'])
                ->where('id_venta', $id)
                ->firstOrFail();
            
            // Generar HTML de la factura usando NegocioHelper
            $html = NegocioHelper::generarFacturaHTML($venta);
            
            // Retornar vista para imprimir
            return view('ventas.factura_completa', compact('html'));
            
        } catch (\Exception $e) {
            \Log::error('Error en imprimirFactura: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar la factura: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al generar la factura');
        }
    }

    /**
     * Generar y descargar PDF de factura
     */
    public function generarPDF($id)
    {
        try {
            $venta = Venta::with(['cliente', 'usuario', 'detalles.producto'])
                ->where('id_venta', $id)
                ->firstOrFail();
            
            // Generar HTML de la factura
            $html = NegocioHelper::generarFacturaHTML($venta);
            
            // Usar DomPDF para generar el PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('legal', 'portrait');
            
            return $pdf->download('factura_' . $venta->numero_factura . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error en generarPDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
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
                    $producto->stock_actual += $detalle->cantidad;
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
                'message' => 'Error al cancelar la venta: ' . $e->getMessage()
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
            
            $venta = Venta::where('id_venta', $id)->first();
            
            if (!$venta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            if ($venta->estado === 'cancelada') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una venta cancelada'
                ], 400);
            }

            $detalles = DB::table('detalle_ventas')
                ->where('id_venta', $id)
                ->get();

            $productos_actualizados = 0;
            foreach ($detalles as $detalle) {
                $producto = Producto::where('id_producto', $detalle->id_producto)->first();
                if ($producto) {
                    $producto->stock_actual += $detalle->cantidad;
                    $producto->save();
                    $productos_actualizados++;
                }
            }
            
            DB::table('detalle_ventas')->where('id_venta', $id)->delete();
            $venta->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada exitosamente. Stock restablecido para ' . $productos_actualizados . ' productos.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar venta: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar ventas a Excel
     */
    public function exportarExcel(Request $request)
    {
        try {
            if (ob_get_length()) {
                ob_end_clean();
            }
            
            $filters = [
                'fecha_desde' => $request->fecha_desde,
                'fecha_hasta' => $request->fecha_hasta,
                'estado' => $request->estado,
                'metodo_pago' => $request->metodo_pago,
                'cliente' => $request->cliente,
                'factura' => $request->factura,
            ];

            $nombreArchivo = 'ventas_' . date('Y-m-d_His') . '.xlsx';
            
            return Excel::download(new VentasExport($filters), $nombreArchivo);
            
        } catch (\Exception $e) {
            \Log::error('Error exportando a Excel: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al exportar: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    /**
     * Ver todas las ventas (vista alternativa)
     */
    public function ventasTodas()
    {
        try {
            $ventas = Venta::with(['cliente', 'usuario'])
                ->orderBy('fecha_venta', 'desc')
                ->paginate(20);

            return view('ventas.todas', compact('ventas'));

        } catch (\Exception $e) {
            \Log::error('Error en ventasTodas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las ventas');
        }
    }
}