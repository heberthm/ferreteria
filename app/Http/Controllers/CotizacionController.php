<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use PDF;

class CotizacionController extends Controller
{
    // ── HELPER PRIVADO: genera el siguiente número disponible ────────────────
    private function generarNumeroCotizacion(): string
    {
        $hoy    = date('Ymd');
        $prefijo = 'COT-' . $hoy . '-';

        // Busca el último número del día y extrae el consecutivo
        $ultimo = Cotizacion::where('numero_cotizacion', 'like', $prefijo . '%')
            ->orderByDesc('numero_cotizacion')
            ->value('numero_cotizacion');

        $consecutivo = $ultimo
            ? (int) substr($ultimo, -5) + 1
            : 1;

        return $prefijo . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);
    }

    // ── INDEX ────────────────────────────────────────────────────────────────
    public function index()
    {
        $numero_cotizacion = $this->generarNumeroCotizacion();
        return view('cotizaciones', compact('numero_cotizacion'));
    }

    // ── ENDPOINT AJAX: siguiente número (para el frontend tras guardar) ───────
    // Ruta: GET /cotizaciones/numero-siguiente
    public function numeroSiguiente()
    {
        return response()->json([
            'numero' => $this->generarNumeroCotizacion(),
        ]);
    }

    // ── DATATABLES ───────────────────────────────────────────────────────────
    public function getData(Request $request)
    {
        try {
            $cotizaciones = Cotizacion::with(['cliente', 'vendedor'])
                ->select(['cotizaciones.*'])
                ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
                ->when($request->fecha_desde, fn($q) => $q->whereDate('fecha_cotizacion', '>=', $request->fecha_desde))
                ->when($request->fecha_hasta, fn($q) => $q->whereDate('fecha_cotizacion', '<=', $request->fecha_hasta))
                ->when($request->cliente, fn($q) => $q->where(function($q2) use ($request) {
                    $q2->where('cliente_nombre', 'LIKE', '%'.$request->cliente.'%')
                       ->orWhereHas('cliente', fn($q3) => $q3->where('nombre', 'LIKE', '%'.$request->cliente.'%'));
                }));

            return DataTables::of($cotizaciones)
                ->addColumn('acciones', function($cotizacion) {
                    return '
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm btn-ver" data-id="'.$cotizacion->id_cotizacion.'" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="'.$cotizacion->id_cotizacion.'" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-success btn-sm btn-pdf" data-id="'.$cotizacion->id_cotizacion.'" title="Generar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="'.$cotizacion->id_cotizacion.'" data-numero="'.$cotizacion->numero_cotizacion.'" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('numero_cotizacion', function($cotizacion) {
                    return '<strong>' . $cotizacion->numero_cotizacion . '</strong>';
                })
                ->editColumn('fecha_cotizacion', function($cotizacion) {
                    return $cotizacion->fecha_cotizacion->format('d/m/Y H:i');
                })
                ->editColumn('fecha_validez', function($cotizacion) {
                    return $cotizacion->fecha_validez ? $cotizacion->fecha_validez->format('d/m/Y') : 'N/A';
                })
                ->editColumn('cliente_nombre', function($cotizacion) {
                    if ($cotizacion->cliente) {
                        return '<strong>' . $cotizacion->cliente->nombre . '</strong><br>
                                <small>' . ($cotizacion->cliente->cedula ?? '') . '</small>';
                    }
                    return '<strong>' . ($cotizacion->cliente_nombre ?? 'Cliente General') . '</strong>';
                })
                ->editColumn('vendedor', function($cotizacion) {
                    return $cotizacion->vendedor ? $cotizacion->vendedor->name : 'N/A';
                })
                ->editColumn('total', function($cotizacion) {
                    return '<strong class="text-primary">$' . number_format($cotizacion->total, 0, ',', '.') . '</strong>';
                })
                ->addColumn('total_raw', fn($c) => (float) $c->total)
                ->editColumn('estado', function($cotizacion) {
                    return '<span class="badge badge-' . $cotizacion->estado_color . '">'
                         . $cotizacion->estado_texto . '</span>';
                })
                ->rawColumns(['numero_cotizacion', 'cliente_nombre', 'total', 'estado', 'acciones'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'numero_cotizacion'           => 'required|unique:cotizaciones',
                'fecha_validez'               => 'nullable|date',
                'productos'                   => 'required|array|min:1',
                'productos.*.id_producto'     => 'required|exists:productos,id_producto',
                'productos.*.cantidad'        => 'required|numeric|min:0.01',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
                'productos.*.descuento'       => 'nullable|numeric|min:0',
            ]);

            $cotizacion = new Cotizacion();
            $cotizacion->numero_cotizacion = $request->numero_cotizacion;

            if ($request->boolean('cliente_general')) {
                $cotizacion->id_cliente       = null;
                $cotizacion->cliente_nombre   = $request->cliente_nombre  ?: 'CLIENTE GENERAL';
                $cotizacion->cliente_cedula   = $request->cliente_cedula  ?: '0000000000';
                $cotizacion->cliente_telefono = $request->cliente_telefono;
                $cotizacion->cliente_email    = $request->cliente_email;

            } elseif ($request->id_cliente) {
                $cliente = Cliente::findOrFail($request->id_cliente);
                $cotizacion->id_cliente       = $cliente->id_cliente;
                $cotizacion->cliente_nombre   = $cliente->nombre;
                $cotizacion->cliente_cedula   = $cliente->cedula;
                $cotizacion->cliente_telefono = $cliente->telefono;
                $cotizacion->cliente_email    = $cliente->email;

            } else {
                $cotizacion->id_cliente       = null;
                $cotizacion->cliente_nombre   = $request->cliente_nombre  ?: 'CLIENTE GENERAL';
                $cotizacion->cliente_cedula   = $request->cliente_cedula  ?: '0000000000';
                $cotizacion->cliente_telefono = $request->cliente_telefono;
                $cotizacion->cliente_email    = $request->cliente_email;
            }

            $cotizacion->id_vendedor          = Auth::id();
            $cotizacion->fecha_cotizacion     = now();
            $cotizacion->fecha_validez        = $request->fecha_validez;
            $cotizacion->observaciones        = $request->observaciones;
            $cotizacion->terminos_condiciones = $request->terminos_condiciones;
            $cotizacion->metodo_pago_sugerido = $request->metodo_pago_sugerido;
            $cotizacion->estado               = 'activa';

            $subtotal  = 0;
            $descuento = 0;

            foreach ($request->productos as $item) {
                $subtotal  += $item['cantidad'] * $item['precio_unitario'];
                $descuento += $item['descuento'] ?? 0;
            }
            
            $tipoIva   = (int) $request->tipo_iva; // -1, 0, 5 o 19
            $base      = $subtotal - $descuento;
            $iva       = $tipoIva > 0 ? $base * ($tipoIva / 100) : 0;

           
            $cotizacion->iva       = $iva;
            $cotizacion->tipo_iva  = $tipoIva;
            $cotizacion->total     = $base + $iva;

            $cotizacion->subtotal  = $subtotal;
            $cotizacion->descuento = $descuento;
            $cotizacion->total     = $subtotal - $descuento;
            $cotizacion->save();

            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id_producto']);

                $detalle                  = new CotizacionDetalle();
                $detalle->id_cotizacion   = $cotizacion->id_cotizacion;
                $detalle->id_producto     = $producto->id_producto;
                $detalle->codigo_producto = $producto->codigo;
                $detalle->nombre_producto = $producto->nombre;
                $detalle->unidad_medida   = $producto->unidad_medida;
                $detalle->cantidad        = $item['cantidad'];
                $detalle->precio_unitario = $item['precio_unitario'];
                $detalle->descuento       = $item['descuento'] ?? 0;
                $detalle->subtotal        = $item['cantidad'] * $item['precio_unitario'];
                $detalle->total           = ($item['cantidad'] * $item['precio_unitario']) - ($item['descuento'] ?? 0);
                $detalle->save();
            }

            DB::commit();

            // Generar el número siguiente DESPUÉS de guardar (ya existe la cotización actual)
            $numeroSiguiente = $this->generarNumeroCotizacion();

            return response()->json([
                'success'          => true,
                'message'          => 'Cotización creada correctamente',
                'id'               => $cotizacion->id_cotizacion,
                'numero_siguiente' => $numeroSiguiente,  // ← el frontend actualiza el input
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error cotización: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $cotizacion = Cotizacion::findOrFail($id);

            $request->validate([
                'fecha_validez'               => 'nullable|date',
                'productos'                   => 'required|array|min:1',
                'productos.*.id_producto'     => 'required|exists:productos,id_producto',
                'productos.*.cantidad'        => 'required|numeric|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
                'productos.*.descuento'       => 'nullable|numeric|min:0',
            ]);

            // Actualizar datos del cliente
            if ($request->boolean('cliente_general')) {
                $cotizacion->id_cliente       = null;
                $cotizacion->cliente_nombre   = $request->cliente_nombre  ?: 'CLIENTE GENERAL';
                $cotizacion->cliente_cedula   = $request->cliente_cedula  ?: '0000000000';
                $cotizacion->cliente_telefono = $request->cliente_telefono;
                $cotizacion->cliente_email    = $request->cliente_email;
            } elseif ($request->id_cliente) {
                $cliente = Cliente::findOrFail($request->id_cliente);
                $cotizacion->id_cliente       = $cliente->id_cliente;
                $cotizacion->cliente_nombre   = $cliente->nombre;
                $cotizacion->cliente_cedula   = $cliente->cedula;
                $cotizacion->cliente_telefono = $cliente->telefono;
                $cotizacion->cliente_email    = $cliente->email;
            }

            $cotizacion->fecha_validez        = $request->fecha_validez;
            $cotizacion->observaciones        = $request->observaciones;
            $cotizacion->terminos_condiciones = $request->terminos_condiciones;
            $cotizacion->metodo_pago_sugerido = $request->metodo_pago_sugerido;

            $subtotal  = 0;
            $descuento = 0;
            foreach ($request->productos as $item) {
                $subtotal  += $item['cantidad'] * $item['precio_unitario'];
                $descuento += $item['descuento'] ?? 0;
            }
            $cotizacion->subtotal  = $subtotal;
            $cotizacion->descuento = $descuento;
            $cotizacion->total     = $subtotal - $descuento;
            $cotizacion->save();

            // Reemplazar detalles: borrar los viejos y crear los nuevos
            $cotizacion->detalles()->delete();
            foreach ($request->productos as $item) {
                $producto                     = Producto::findOrFail($item['id_producto']);
                $detalle                      = new CotizacionDetalle();
                $detalle->id_cotizacion       = $cotizacion->id_cotizacion;
                $detalle->id_producto         = $producto->id_producto;
                $detalle->codigo_producto     = $producto->codigo;
                $detalle->nombre_producto     = $producto->nombre;
                $detalle->unidad_medida       = $producto->unidad_medida;
                $detalle->cantidad            = $item['cantidad'];
                $detalle->precio_unitario     = $item['precio_unitario'];
                $detalle->descuento           = $item['descuento'] ?? 0;
                $detalle->subtotal            = $item['cantidad'] * $item['precio_unitario'];
                $detalle->total               = ($item['cantidad'] * $item['precio_unitario']) - ($item['descuento'] ?? 0);
                $detalle->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cotización actualizada correctamente',
                'id'      => $cotizacion->id_cotizacion,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error actualizando cotización: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ── SHOW ─────────────────────────────────────────────────────────────────
 public function show($id)
    {
        $cotizacion = Cotizacion::with(['cliente', 'vendedor', 'detalles.producto'])
            ->where('id_cotizacion', $id)
            ->firstOrFail();
        return response()->json($cotizacion);
    }
    // ── DESTROY ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        try {
            $cotizacion = Cotizacion::findOrFail($id);

            if ($cotizacion->estado === 'aceptada') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una cotización aceptada',
                ], 400);
            }

            $cotizacion->detalles()->delete();
            $cotizacion->delete();

            return response()->json(['success' => true, 'message' => 'Cotización eliminada']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── CAMBIAR ESTADO ───────────────────────────────────────────────────────
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $cotizacion = Cotizacion::findOrFail($id);
            $request->validate(['estado' => 'required|in:activa,vencida,aceptada,rechazada']);
            $cotizacion->estado = $request->estado;
            $cotizacion->save();
            return response()->json(['success' => true, 'message' => 'Estado actualizado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── GENERAR PDF ──────────────────────────────────────────────────────────
 public function generarPDF($id)
{
    try {
        $cotizacion = Cotizacion::with(['cliente', 'vendedor', 'detalles'])
            ->where('id_cotizacion', $id)
            ->firstOrFail();

        $empresa = [
            'nombre'    => 'Ferretería XYZ',
            'nit'       => '123456789-0',
            'direccion' => 'Calle 123 #45-67',
            'telefono'  => '(601) 123-4567',
            'email'     => 'info@ferreteriaxyz.com',
        ];

        // Si piden descarga real del PDF
        if (request('download') == 1) {
            $pdf = PDF::loadView('pdf-cotizacion', compact('cotizacion', 'empresa'));
            return $pdf->download('cotizacion_' . $cotizacion->numero_cotizacion . '.pdf');
        }

        // Por defecto retorna HTML para el iframe
        return view('pdf-cotizacion', compact('cotizacion', 'empresa'));

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    // ── BÚSQUEDAS AJAX PARA SELECT2 ──────────────────────────────────────────

    public function buscarClientes(Request $request)
    {
        try {
            $term = $request->get('q', '');

            $clientes = Cliente::where('estado', 'activo')
                ->where(function($q) use ($term) {
                    $q->where('nombre', 'LIKE', '%' . $term . '%')
                      ->orWhere('cedula', 'LIKE', '%' . $term . '%');
                })
                ->select('id_cliente', 'nombre', 'cedula', 'telefono', 'email')
                ->limit(20)
                ->get()
                ->map(fn($c) => [
                    'id'       => $c->id_cliente,
                    'text'     => $c->nombre . ' — ' . $c->cedula,
                    'nombre'   => $c->nombre,
                    'cedula'   => $c->cedula,
                    'telefono' => $c->telefono ?? '',
                    'email'    => $c->email    ?? '',
                ]);

            return response()->json(['results' => $clientes]);

        } catch (\Exception $e) {
            return response()->json(['results' => []], 500);
        }
    }

    public function buscarProductos(Request $request)
    {
        try {
            $term = $request->get('q', '');

            $productos = Producto::where(function($q) use ($term) {
                    $q->where('codigo', 'LIKE', '%' . $term . '%')
                      ->orWhere('nombre', 'LIKE', '%' . $term . '%');
                })
                ->select('id_producto', 'codigo', 'nombre', 'precio_venta', 'stock_actual', 'unidad_medida')
                ->limit(20)
                ->get()
                ->map(fn($p) => [
                    'id'     => $p->id_producto,
                    'text'   => $p->codigo . ' - ' . $p->nombre,
                    'precio' => $p->precio_venta,
                    'stock'  => $p->stock_actual,
                    'unidad' => $p->unidad_medida ?? '',
                ]);

            return response()->json(['results' => $productos]);

        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}