<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DevolucionController extends Controller
{
    public function index()
    {
        $siguienteNumero = Devolucion::generarNumeroDevolucion();
        return view('devoluciones', compact('siguienteNumero'));
    }

    public function getData(Request $request)
{
    try {
        $query = Devolucion::with(['cliente'])
            ->select('devoluciones.*')
            ->orderBy('created_at', 'desc'); // Ordenar por fecha descendente

        // Aplicar filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_devolucion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_devolucion', '<=', $request->fecha_hasta);
        }

        if ($request->filled('cliente')) {
            $query->where(function($q) use ($request) {
                $q->where('cliente_nombre', 'like', '%' . $request->cliente . '%')
                  ->orWhere('cliente_cedula', 'like', '%' . $request->cliente . '%');
            });
        }

        return DataTables::of($query)
            ->addColumn('fecha', function($row) {
                return date('d/m/Y', strtotime($row->fecha_devolucion));
            })
            ->addColumn('cliente_nombre', function($row) {
                return $row->cliente ? $row->cliente->nombre : ($row->cliente_nombre ?? 'N/A');
            })
            ->addColumn('cliente_cedula', function($row) {
                return $row->cliente ? $row->cliente->cedula : ($row->cliente_cedula ?? 'N/A');
            })
            ->addColumn('total_formateado', function($row) {
                return '$' . number_format($row->total ?? 0, 0, ',', '.');
            })
            ->addColumn('estado_color', function($row) {
                $colors = [
                    'pendiente' => 'warning',
                    'aprobada' => 'success',
                    'rechazada' => 'danger',
                    'completada' => 'info',
                    'cancelada' => 'secondary'
                ];
                return '<span class="badge badge-' . ($colors[$row->estado] ?? 'secondary') . '">' . 
                       ($row->estado_texto ?? ucfirst($row->estado)) . '</span>';
            })
            ->addColumn('acciones', function($row) {
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button type="button" class="btn btn-info btn-sm btn-ver" data-id="' . $row->id_devolucion . '"><i class="fas fa-eye"></i></button>';
                
                if ($row->estado == 'pendiente') {
                    $btn .= '<button type="button" class="btn btn-success btn-sm btn-aprobar" data-id="' . $row->id_devolucion . '"><i class="fas fa-check"></i></button>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-rechazar" data-id="' . $row->id_devolucion . '"><i class="fas fa-times"></i></button>';
                    $btn .= '<button type="button" class="btn btn-warning btn-sm btn-editar" data-id="' . $row->id_devolucion . '"><i class="fas fa-edit"></i></button>';
                }
                
                if (in_array($row->estado, ['aprobada', 'completada'])) {
                    $btn .= '<button type="button" class="btn btn-primary btn-sm btn-pdf" data-id="' . $row->id_devolucion . '"><i class="fas fa-file-pdf"></i></button>';
                }
                
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['acciones', 'estado_color'])
            ->make(true);
            
    } catch (\Exception $e) {
        Log::error('Error en getData: ' . $e->getMessage());
        return response()->json([
            'error' => true,
            'message' => 'Error al cargar los datos: ' . $e->getMessage()
        ], 500);
    }
}


    public function siguienteNumero()
    {
        $devolucion = new Devolucion();
        return $devolucion->numeroSiguiente();
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validar datos
            $request->validate([
                'fecha_devolucion' => 'required|date',
                'id_venta' => 'nullable|exists:ventas,id_venta',
                'tipo_devolucion' => 'required|in:total,parcial',
                'motivo' => 'required|in:producto_danado,producto_equivocado,cambio_de_producto,insatisfaccion,otro',
                'motivo_descripcion' => 'required_if:motivo,otro|nullable|string',
                'metodo_reembolso' => 'required|in:efectivo,tarjeta,transferencia,credito_en_cuenta,no_aplica',
                'reingresar_inventario' => 'boolean',
                'observaciones' => 'nullable|string',
                'productos' => 'required|array|min:1',
                'productos.*.id_producto' => 'required|exists:productos,id_producto',
                'productos.*.cantidad_devuelta' => 'required|integer|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
                'productos.*.condicion_producto' => 'required|in:nuevo_sin_uso,abierto_sin_uso,usado_buen_estado,danado,incompleto'
            ]);

            // Crear devolución
            $devolucion = new Devolucion();
            $devolucion->numero_devolucion = Devolucion::generarNumeroDevolucion();
            $devolucion->fecha_devolucion = $request->fecha_devolucion;
            $devolucion->id_venta = $request->id_venta;
            
            // Datos del cliente
            if ($request->filled('id_cliente')) {
                $devolucion->id_cliente = $request->id_cliente;
                $cliente = Cliente::find($request->id_cliente);
                $devolucion->cliente_nombre = $cliente->nombre;
                $devolucion->cliente_cedula = $cliente->cedula;
                $devolucion->cliente_telefono = $cliente->telefono;
                $devolucion->cliente_email = $cliente->email;
            } else {
                $devolucion->cliente_nombre = $request->cliente_nombre ?? 'CLIENTE GENERAL';
                $devolucion->cliente_cedula = $request->cliente_cedula ?? '0000000000';
                $devolucion->cliente_telefono = $request->cliente_telefono;
                $devolucion->cliente_email = $request->cliente_email;
            }

            $devolucion->tipo_devolucion = $request->tipo_devolucion;
            $devolucion->motivo = $request->motivo;
            $devolucion->motivo_descripcion = $request->motivo_descripcion;
            $devolucion->metodo_reembolso = $request->metodo_reembolso;
            $devolucion->reingresar_inventario = $request->boolean('reingresar_inventario', true);
            $devolucion->observaciones = $request->observaciones;
            $devolucion->creado_por = Auth::id();
            $devolucion->estado = 'pendiente';
            
            // Calcular totales
            $subtotal = 0;
            $descuento_total = 0;
            
            foreach ($request->productos as $item) {
                $subtotal += $item['cantidad_devuelta'] * $item['precio_unitario'];
                $descuento_total += $item['descuento'] ?? 0;
            }
            
            $base_imponible = $subtotal - $descuento_total;
            $iva_total = $base_imponible * 0.19; // 19% IVA
            $total = $base_imponible + $iva_total;
            
            $devolucion->subtotal = $subtotal;
            $devolucion->descuento_total = $descuento_total;
            $devolucion->iva_total = $iva_total;
            $devolucion->total = $total;
            
            $devolucion->save();

            // Guardar detalles
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['id_producto']);
                $subtotal_item = $item['cantidad_devuelta'] * $item['precio_unitario'];
                $total_item = $subtotal_item - ($item['descuento'] ?? 0);
                
                $detalle = new DevolucionDetalle();
                $detalle->id_devolucion = $devolucion->id_devolucion;
                $detalle->id_producto = $item['id_producto'];
                $detalle->nombre_producto = $item['nombre_producto'] ?? $producto->nombre;
                $detalle->codigo_producto = $producto->codigo;
                $detalle->cantidad_devuelta = $item['cantidad_devuelta'];
                $detalle->cantidad_original = $item['cantidad_original'] ?? null;
                $detalle->precio_unitario = $item['precio_unitario'];
                $detalle->descuento = $item['descuento'] ?? 0;
                $detalle->subtotal = $subtotal_item;
                $detalle->iva = 19;
                $detalle->total = $total_item;
                $detalle->condicion_producto = $item['condicion_producto'];
                $detalle->observaciones = $item['observaciones'] ?? null;
                $detalle->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución creada exitosamente',
                'data' => $devolucion,
                'numero_siguiente' => Devolucion::generarNumeroDevolucion()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear devolución: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $devolucion = new Devolucion();
        $siguienteNumero = $devolucion->generarNumeroDevolucion();
        
        return view('devoluciones.create', compact('siguienteNumero'));
    }

    // Método específico para el modal
   public function numeroSiguiente()
{
    try {
        $numero = Devolucion::generarNumeroDevolucion();
        return response()->json([
            'success' => true,
            'numero' => $numero
        ]);
    } catch (\Exception $e) {
        Log::error('Error al generar número de devolución: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al generar el número'
        ], 500);
    }
}

    public function show($id)
    {
        $devolucion = Devolucion::with([
            'detalles.producto',
            'cliente',
            'venta',
            'creador',
            'aprobador'
        ])->findOrFail($id);

        return response()->json($devolucion);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $devolucion = Devolucion::findOrFail($id);

            // Solo se puede editar si está pendiente
            if ($devolucion->estado != 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden editar devoluciones pendientes'
                ], 400);
            }

            // Validar datos
            $request->validate([
                'fecha_devolucion' => 'required|date',
                'tipo_devolucion' => 'required|in:total,parcial',
                'motivo' => 'required|in:producto_danado,producto_equivocado,cambio_de_producto,insatisfaccion,otro',
                'motivo_descripcion' => 'required_if:motivo,otro|nullable|string',
                'metodo_reembolso' => 'required|in:efectivo,tarjeta,transferencia,credito_en_cuenta,no_aplica',
                'reingresar_inventario' => 'boolean',
                'observaciones' => 'nullable|string',
                'productos' => 'required|array|min:1',
                'productos.*.id_producto' => 'required|exists:productos,id_producto',
                'productos.*.cantidad_devuelta' => 'required|integer|min:1'
            ]);

            // Actualizar datos
            $devolucion->fecha_devolucion = $request->fecha_devolucion;
            $devolucion->tipo_devolucion = $request->tipo_devolucion;
            $devolucion->motivo = $request->motivo;
            $devolucion->motivo_descripcion = $request->motivo_descripcion;
            $devolucion->metodo_reembolso = $request->metodo_reembolso;
            $devolucion->reingresar_inventario = $request->boolean('reingresar_inventario', true);
            $devolucion->observaciones = $request->observaciones;

            // Eliminar detalles antiguos
            DevolucionDetalle::where('id_devolucion', $id)->delete();

            // Calcular totales
            $subtotal = 0;
            $descuento_total = 0;
            
            foreach ($request->productos as $item) {
                $subtotal += $item['cantidad_devuelta'] * $item['precio_unitario'];
                $descuento_total += $item['descuento'] ?? 0;
            }
            
            $base_imponible = $subtotal - $descuento_total;
            $iva_total = $base_imponible * 0.19;
            $total = $base_imponible + $iva_total;
            
            $devolucion->subtotal = $subtotal;
            $devolucion->descuento_total = $descuento_total;
            $devolucion->iva_total = $iva_total;
            $devolucion->total = $total;
            
            $devolucion->save();

            // Guardar nuevos detalles
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['id_producto']);
                $subtotal_item = $item['cantidad_devuelta'] * $item['precio_unitario'];
                $total_item = $subtotal_item - ($item['descuento'] ?? 0);
                
                $detalle = new DevolucionDetalle();
                $detalle->id_devolucion = $devolucion->id_devolucion;
                $detalle->id_producto = $item['id_producto'];
                $detalle->nombre_producto = $item['nombre_producto'] ?? $producto->nombre;
                $detalle->codigo_producto = $producto->codigo;
                $detalle->cantidad_devuelta = $item['cantidad_devuelta'];
                $detalle->cantidad_original = $item['cantidad_original'] ?? null;
                $detalle->precio_unitario = $item['precio_unitario'];
                $detalle->descuento = $item['descuento'] ?? 0;
                $detalle->subtotal = $subtotal_item;
                $detalle->iva = 19;
                $detalle->total = $total_item;
                $detalle->condicion_producto = $item['condicion_producto'] ?? 'nuevo_sin_uso';
                $detalle->observaciones = $item['observaciones'] ?? null;
                $detalle->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución actualizada exitosamente',
                'data' => $devolucion
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $devolucion = Devolucion::findOrFail($id);

            // Solo se puede eliminar si está pendiente o cancelada
            if (!in_array($devolucion->estado, ['pendiente', 'cancelada'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una devolución ' . $devolucion->estado
                ], 400);
            }

            $devolucion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Devolución eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    public function aprobar(Request $request, $id)
    {
        try {
            $devolucion = Devolucion::findOrFail($id);
            
            if ($devolucion->estado != 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden aprobar devoluciones pendientes'
                ], 400);
            }

            $devolucion->aprobar(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Devolución aprobada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rechazar(Request $request, $id)
    {
        try {
            $devolucion = Devolucion::findOrFail($id);
            
            if ($devolucion->estado != 'pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden rechazar devoluciones pendientes'
                ], 400);
            }

            $request->validate([
                'motivo_rechazo' => 'required|string|max:500'
            ]);

            $devolucion->rechazar(Auth::id(), $request->motivo_rechazo);

            return response()->json([
                'success' => true,
                'message' => 'Devolución rechazada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completar($id)
    {
        try {
            $devolucion = Devolucion::findOrFail($id);
            
            if ($devolucion->estado != 'aprobada') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden completar devoluciones aprobadas'
                ], 400);
            }

            $devolucion->completar();

            return response()->json([
                'success' => true,
                'message' => 'Devolución completada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al completar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelar($id)
    {
        try {
            $devolucion = Devolucion::findOrFail($id);
            
            if (!in_array($devolucion->estado, ['pendiente', 'aprobada'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar una devolución ' . $devolucion->estado
                ], 400);
            }

            $devolucion->cancelar();

            return response()->json([
                'success' => true,
                'message' => 'Devolución cancelada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buscarVentasCliente(Request $request)
    {
        $term = $request->get('q');
        
        $ventas = Venta::with('cliente')
            ->whereHas('cliente', function($q) use ($term) {
                $q->where('nombre', 'like', '%' . $term . '%')
                  ->orWhere('cedula', 'like', '%' . $term . '%');
            })
            ->orWhere('numero_factura', 'like', '%' . $term . '%')
            ->where('estado', 'completada')
            ->limit(10)
            ->get();

        $results = [];
        foreach ($ventas as $venta) {
            $results[] = [
                'id' => $venta->id_venta,
                'text' => $venta->numero_factura . ' - ' . $venta->cliente->nombre,
                'cliente_nombre' => $venta->cliente->nombre,
                'cliente_cedula' => $venta->cliente->cedula,
                'cliente_telefono' => $venta->cliente->telefono,
                'cliente_email' => $venta->cliente->email,
                'fecha' => $venta->fecha_venta->format('d/m/Y'),
                'total' => $venta->total
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function getDetallesVenta($id)
    {
        $venta = Venta::with(['detalles.producto'])->findOrFail($id);
        
        $productos = [];
        foreach ($venta->detalles as $detalle) {
            $productos[] = [
                'id_producto' => $detalle->id_producto,
                'nombre' => $detalle->producto->nombre,
                'codigo' => $detalle->producto->codigo,
                'cantidad_original' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'descuento' => $detalle->descuento,
                'stock_actual' => $detalle->producto->stock_actual
            ];
        }

        return response()->json([
            'cliente' => $venta->cliente,
            'productos' => $productos,
            'fecha' => $venta->fecha_venta->format('Y-m-d'),
            'total' => $venta->total
        ]);
    }

    public function pdf($id)
    {
        $devolucion = Devolucion::with([
            'detalles.producto',
            'cliente',
            'creador',
            'aprobador'
        ])->findOrFail($id);

        // Aquí implementarías la generación del PDF
        // Por ahora retornamos un JSON
        return response()->json([
            'success' => true,
            'message' => 'PDF generado',
            'url' => '#'
        ]);
    }
}