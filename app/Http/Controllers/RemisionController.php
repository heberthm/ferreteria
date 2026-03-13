<?php

namespace App\Http\Controllers;

use App\Models\Remision;
use App\Models\RemisionDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RemisionController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $numero_remision = $this->generarNumeroRemision();
        return view('remisiones', compact('numero_remision'));
    }

    // ── DATATABLE ─────────────────────────────────────────────────────────────
    public function data(Request $request)
    {
        $query = Remision::with(['vendedor'])
            ->select('remisiones.*');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_remision', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_remision', '<=', $request->fecha_hasta);
        }
        if ($request->filled('cliente')) {
            $query->where('cliente_nombre', 'like', '%' . $request->cliente . '%');
        }

        return DataTables::of($query)
            ->addColumn('vendedor', fn($r) => $r->vendedor ? $r->vendedor->name : 'N/A')
            ->addColumn('total_fmt', fn($r) => '$' . number_format($r->total, 0, ',', '.'))
            ->addColumn('total_raw', fn($r) => $r->total)
            ->addColumn('estado', function ($r) {
                return '<span class="badge badge-' . $r->estado_color . '">'
                    . $r->estado_texto . '</span>';
            })
            ->addColumn('fecha_remision', fn($r) => $r->fecha_remision
                ? $r->fecha_remision->format('d/m/Y') : '—')
            ->addColumn('fecha_entrega_estimada', fn($r) => $r->fecha_entrega_estimada
                ? $r->fecha_entrega_estimada->format('d/m/Y') : '—')
            ->addColumn('acciones', function ($r) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info btn-ver" data-id="' . $r->id_remision . '" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-editar" data-id="' . $r->id_remision . '" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-success btn-pdf" data-id="' . $r->id_remision . '" title="PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <button class="btn btn-danger btn-eliminar"
                            data-id="' . $r->id_remision . '"
                            data-numero="' . $r->numero_remision . '" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'numero_remision'             => 'required|unique:remisiones',
                'fecha_entrega_estimada'      => 'nullable|date',
                'productos'                   => 'required|array|min:1',
                'productos.*.id_producto'     => 'required|exists:productos,id_producto',
                'productos.*.cantidad'        => 'required|numeric|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
                'productos.*.descuento'       => 'nullable|numeric|min:0',
            ]);

            $remision = new Remision();
            $remision->numero_remision = $request->numero_remision;
            $remision->fecha_remision  = now()->toDateString();
            $remision->fecha_entrega_estimada = $request->fecha_entrega_estimada;
            $remision->conductor       = $request->conductor;
            $remision->vehiculo_placa  = $request->vehiculo_placa;
            $remision->direccion_entrega = $request->direccion_entrega;
            $remision->observaciones   = $request->observaciones;
            $remision->estado          = 'pendiente';
            $remision->id_vendedor     = Auth::id();

            // Cliente
            if ($request->boolean('cliente_general')) {
                $remision->id_cliente       = null;
                $remision->cliente_nombre   = $request->cliente_nombre   ?: 'CLIENTE GENERAL';
                $remision->cliente_cedula   = $request->cliente_cedula   ?: '0000000000';
                $remision->cliente_telefono = $request->cliente_telefono;
                $remision->cliente_email    = $request->cliente_email;
            } elseif ($request->id_cliente) {
                $cliente = Cliente::findOrFail($request->id_cliente);
                $remision->id_cliente       = $cliente->id_cliente;
                $remision->cliente_nombre   = $cliente->nombre;
                $remision->cliente_cedula   = $cliente->cedula;
                $remision->cliente_telefono = $cliente->telefono;
                $remision->cliente_email    = $cliente->email;
            } else {
                $remision->id_cliente       = null;
                $remision->cliente_nombre   = $request->cliente_nombre   ?: 'CLIENTE GENERAL';
                $remision->cliente_cedula   = $request->cliente_cedula   ?: '0000000000';
                $remision->cliente_telefono = $request->cliente_telefono;
                $remision->cliente_email    = $request->cliente_email;
            }

            // Totales
            $subtotal = 0;
            $descuento = 0;
            foreach ($request->productos as $item) {
                $subtotal  += $item['cantidad'] * $item['precio_unitario'];
                $descuento += $item['descuento'] ?? 0;
            }
            $remision->subtotal  = $subtotal;
            $remision->descuento = $descuento;
            $remision->total     = $subtotal - $descuento;
            $remision->save();

            // Detalles
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id_producto']);
                $detalle = new RemisionDetalle();
                $detalle->id_remision    = $remision->id_remision;
                $detalle->id_producto    = $producto->id_producto;
                $detalle->codigo_producto = $producto->codigo;
                $detalle->nombre_producto = $producto->nombre;
                $detalle->unidad_medida  = $producto->unidad_medida;
                $detalle->cantidad       = $item['cantidad'];
                $detalle->precio_unitario = $item['precio_unitario'];
                $detalle->descuento      = $item['descuento'] ?? 0;
                $detalle->subtotal       = $item['cantidad'] * $item['precio_unitario'];
                $detalle->total          = ($item['cantidad'] * $item['precio_unitario']) - ($item['descuento'] ?? 0);
                $detalle->save();
            }

            DB::commit();

            return response()->json([
                'success'          => true,
                'message'          => 'Remisión creada correctamente',
                'id'               => $remision->id_remision,
                'numero_siguiente' => $this->generarNumeroRemision(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error remisión: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ── SHOW ──────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $remision = Remision::with(['cliente', 'vendedor', 'detalles.producto'])
            ->where('id_remision', $id)
            ->firstOrFail();

        return response()->json($remision);
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $remision = Remision::findOrFail($id);

            $request->validate([
                'numero_remision'             => 'required|unique:remisiones,numero_remision,' . $id . ',id_remision',
                'fecha_entrega_estimada'      => 'nullable|date',
                'productos'                   => 'required|array|min:1',
                'productos.*.id_producto'     => 'required|exists:productos,id_producto',
                'productos.*.cantidad'        => 'required|numeric|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
                'productos.*.descuento'       => 'nullable|numeric|min:0',
            ]);

            $remision->conductor          = $request->conductor;
            $remision->vehiculo_placa     = $request->vehiculo_placa;
            $remision->direccion_entrega  = $request->direccion_entrega;
            $remision->fecha_entrega_estimada = $request->fecha_entrega_estimada;
            $remision->observaciones      = $request->observaciones;

            // Cliente
            if ($request->boolean('cliente_general')) {
                $remision->id_cliente       = null;
                $remision->cliente_nombre   = $request->cliente_nombre   ?: 'CLIENTE GENERAL';
                $remision->cliente_cedula   = $request->cliente_cedula   ?: '0000000000';
                $remision->cliente_telefono = $request->cliente_telefono;
                $remision->cliente_email    = $request->cliente_email;
            } elseif ($request->id_cliente) {
                $cliente = Cliente::findOrFail($request->id_cliente);
                $remision->id_cliente       = $cliente->id_cliente;
                $remision->cliente_nombre   = $cliente->nombre;
                $remision->cliente_cedula   = $cliente->cedula;
                $remision->cliente_telefono = $cliente->telefono;
                $remision->cliente_email    = $cliente->email;
            }

            // Recalcular totales
            $subtotal = 0; $descuento = 0;
            foreach ($request->productos as $item) {
                $subtotal  += $item['cantidad'] * $item['precio_unitario'];
                $descuento += $item['descuento'] ?? 0;
            }
            $remision->subtotal  = $subtotal;
            $remision->descuento = $descuento;
            $remision->total     = $subtotal - $descuento;
            $remision->save();

            // Reemplazar detalles
            RemisionDetalle::where('id_remision', $id)->delete();
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id_producto']);
                $detalle = new RemisionDetalle();
                $detalle->id_remision     = $remision->id_remision;
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

            return response()->json([
                'success' => true,
                'message' => 'Remisión actualizada correctamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ── CAMBIAR ESTADO ────────────────────────────────────────────────────────
    public function cambiarEstado(Request $request, $id)
    {
        $remision = Remision::findOrFail($id);
        $remision->estado = $request->estado;

        if ($request->estado === 'entregada') {
            $remision->fecha_entrega_real = now()->toDateString();
        }

        $remision->save();

        return response()->json(['success' => true, 'message' => 'Estado actualizado']);
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $remision = Remision::findOrFail($id);

        if (in_array($remision->estado, ['entregada'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una remisión entregada.',
            ], 422);
        }

        $remision->delete();

        return response()->json([
            'success' => true,
            'message' => 'Remisión eliminada correctamente',
        ]);
    }

    // ── NÚMERO SIGUIENTE ──────────────────────────────────────────────────────
    public function numeroSiguiente()
    {
        return response()->json(['numero' => $this->generarNumeroRemision()]);
    }

    private function generarNumeroRemision(): string
    {
        $prefijo = 'REM-' . date('Ymd') . '-';
        $ultimo  = Remision::where('numero_remision', 'like', $prefijo . '%')
            ->orderBy('numero_remision', 'desc')
            ->value('numero_remision');

        $siguiente = $ultimo
            ? (int) substr($ultimo, -5) + 1
            : 1;

        return $prefijo . str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }

    // ── BUSCAR PRODUCTOS ──────────────────────────────────────────────────────
    public function buscarProductos(Request $request)
    {
        $q = $request->get('q', '');
        $productos = Producto::where(function ($query) use ($q) {
            $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('codigo', 'like', "%{$q}%");
        })
        ->where('activo', 1)
        ->limit(20)
        ->get(['id_producto', 'codigo', 'nombre', 'stock_actual', 'unidad_medida', 'precio_venta']);

        return response()->json([
            'results' => $productos->map(fn($p) => [
                'id'     => $p->id_producto,
                'text'   => $p->nombre,
                'codigo' => $p->codigo,
                'precio' => $p->precio_venta,
                'stock'  => $p->stock_actual,
            ])
        ]);
    }
}