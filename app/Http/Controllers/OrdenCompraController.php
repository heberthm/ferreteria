<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrdenCompraController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // Vista principal
    // ──────────────────────────────────────────────────────────────
    public function index()
    {
         $numero_orden = 'OC-' . date('Ymd') . '-00001';
    return view('ordenes-compra', compact('numero_orden'));
    }

    // ──────────────────────────────────────────────────────────────
    // DataTable server-side
    // ──────────────────────────────────────────────────────────────
    public function getData(Request $request)
    {
        $query = OrdenCompra::with(['usuario'])
            ->select('ordenes_compra.*');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_orden', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_orden', '<=', $request->fecha_hasta);
        }
        if ($request->filled('proveedor')) {
            $query->where('proveedor_nombre', 'like', '%' . $request->proveedor . '%');
        }

        return DataTables::of($query)
            ->addColumn('fecha_orden_fmt', fn($r) =>
                $r->fecha_orden ? $r->fecha_orden->format('d/m/Y') : '—')
            ->addColumn('fecha_entrega_fmt', fn($r) =>
                $r->fecha_entrega_esperada ? $r->fecha_entrega_esperada->format('d/m/Y') : '—')
            ->addColumn('proveedor_display', fn($r) =>
                $r->proveedor_nombre ?? '—')
            ->addColumn('responsable', fn($r) =>
                $r->usuario ? $r->usuario->name : 'N/A')
            ->addColumn('total_fmt', fn($r) =>
                '$' . number_format($r->total, 0, ',', '.'))
            ->addColumn('total_raw', fn($r) => $r->total)
            ->addColumn('estado_badge', fn($r) => $r->estado_badge)
            ->addColumn('acciones', function ($r) {
                return '
                <div class="btn-group" role="group">
                    <button class="btn btn-info btn-sm btn-ver" data-id="' . $r->id_orden . '" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm btn-editar" data-id="' . $r->id_orden . '" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-success btn-sm btn-pdf" data-id="' . $r->id_orden . '" title="PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-eliminar"
                            data-id="' . $r->id_orden . '"
                            data-numero="' . $r->numero_orden . '" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

    // ──────────────────────────────────────────────────────────────
    // Siguiente número (AJAX)
    // ──────────────────────────────────────────────────────────────
    public function numeroSiguiente()
    {
        return response()->json(['numero' => OrdenCompra::siguienteNumero()]);
    }

    // ──────────────────────────────────────────────────────────────
    // Mostrar una orden (AJAX)
    // ──────────────────────────────────────────────────────────────
    public function show($id)
    {
        $orden = OrdenCompra::with(['detalles.producto', 'usuario'])
            ->findOrFail($id);

        return response()->json($orden);
    }

    // ──────────────────────────────────────────────────────────────
    // Guardar nueva orden
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'numero_orden'           => 'required|unique:ordenes_compra,numero_orden',
            'fecha_orden'            => 'required|date',
            'proveedor_nombre'       => 'required|string|max:150',
            'productos'              => 'required|array|min:1',
            'productos.*.id_producto'   => 'required',
            'productos.*.cantidad'      => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            ['subtotal' => $sub, 'descuento' => $desc, 'iva' => $iva, 'total' => $total]
                = $this->calcularTotales($request);

            $orden = OrdenCompra::create([
                'numero_orden'           => $request->numero_orden,
                'fecha_orden'            => $request->fecha_orden,
                'fecha_entrega_esperada' => $request->fecha_entrega_esperada ?: null,
                'id_proveedor'           => $request->id_proveedor ?: null,
                'proveedor_nombre'       => $request->proveedor_nombre,
                'proveedor_nit'          => $request->proveedor_nit,
                'proveedor_telefono'     => $request->proveedor_telefono,
                'proveedor_email'        => $request->proveedor_email,
                'proveedor_direccion'    => $request->proveedor_direccion,
                'metodo_pago'            => $request->metodo_pago,
                'dias_credito'           => $request->dias_credito ?? 0,
                'subtotal'               => $sub,
                'descuento_total'        => $desc,
                'impuesto_porcentaje'    => $request->tipo_iva ?? 0,
                'impuesto_valor'         => $iva,
                'total'                  => $total,
                'estado'                 => 'borrador',
                'observaciones'          => $request->observaciones,
                'terminos_condiciones'   => $request->terminos_condiciones,
                'userId'                 => auth()->id(),
            ]);

            $this->guardarDetalle($orden, $request->productos);

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Orden de compra creada correctamente.',
                'numero_siguiente' => OrdenCompra::siguienteNumero(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Actualizar orden
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $orden = OrdenCompra::findOrFail($id);

        $request->validate([
            'proveedor_nombre'          => 'required|string|max:150',
            'productos'                 => 'required|array|min:1',
            'productos.*.id_producto'   => 'required',
            'productos.*.cantidad'      => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            ['subtotal' => $sub, 'descuento' => $desc, 'iva' => $iva, 'total' => $total]
                = $this->calcularTotales($request);

            $orden->update([
                'fecha_entrega_esperada' => $request->fecha_entrega_esperada ?: null,
                'id_proveedor'           => $request->id_proveedor ?: null,
                'proveedor_nombre'       => $request->proveedor_nombre,
                'proveedor_nit'          => $request->proveedor_nit,
                'proveedor_telefono'     => $request->proveedor_telefono,
                'proveedor_email'        => $request->proveedor_email,
                'proveedor_direccion'    => $request->proveedor_direccion,
                'metodo_pago'            => $request->metodo_pago,
                'dias_credito'           => $request->dias_credito ?? 0,
                'subtotal'               => $sub,
                'descuento_total'        => $desc,
                'impuesto_porcentaje'    => $request->tipo_iva ?? 0,
                'impuesto_valor'         => $iva,
                'total'                  => $total,
                'observaciones'          => $request->observaciones,
                'terminos_condiciones'   => $request->terminos_condiciones,
            ]);

            $orden->detalles()->delete();
            $this->guardarDetalle($orden, $request->productos);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Orden actualizada correctamente.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Cambiar estado
    // ──────────────────────────────────────────────────────────────
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate(['estado' => 'required|in:borrador,enviada,confirmada,recibida_parcial,recibida,cancelada']);

        $orden = OrdenCompra::findOrFail($id);
        $extra = [];

        if ($request->estado === 'recibida') {
            $extra['fecha_entrega_real'] = now()->toDateString();
        }

        $orden->update(array_merge(['estado' => $request->estado], $extra));

        return response()->json(['success' => true, 'message' => 'Estado actualizado.']);
    }

    // ──────────────────────────────────────────────────────────────
    // Eliminar (soft delete)
    // ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $orden = OrdenCompra::findOrFail($id);

        if (in_array($orden->estado, ['confirmada', 'recibida'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una orden confirmada o recibida.',
            ], 422);
        }

        $orden->delete();

        return response()->json(['success' => true, 'message' => 'Orden eliminada correctamente.']);
    }

    // ──────────────────────────────────────────────────────────────
    // PDF
    // ──────────────────────────────────────────────────────────────
    public function pdf($id)
    {
        $orden = OrdenCompra::with(['detalles.producto', 'usuario'])->findOrFail($id);
        // Usar tu librería preferida (barryvdh/laravel-dompdf, etc.)
        // return PDF::loadView('ordenes_compra.pdf', compact('orden'))->stream();
        return view('ordenes_compra.pdf', compact('orden'));
    }

    // ──────────────────────────────────────────────────────────────
    // Búsqueda de proveedores (Select2 AJAX)
    // ──────────────────────────────────────────────────────────────
    public function buscarProveedores(Request $request)
    {
        $q = $request->get('q', '');

        $proveedores = Proveedor::where('nombre', 'like', "%{$q}%")
            ->orWhere('nit', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id_proveedor,
                'text'     => $p->nombre . ' — ' . ($p->nit ?? 'S/N'),
                'nombre'   => $p->nombre,
                'nit'      => $p->nit,
                'telefono' => $p->telefono,
                'email'    => $p->email,
                'direccion' => $p->direccion,
            ]);

        return response()->json(['results' => $proveedores]);
    }

    // ──────────────────────────────────────────────────────────────
    // Privados
    // ──────────────────────────────────────────────────────────────
    private function calcularTotales(Request $request): array
    {
        $sub  = 0;
        $desc = 0;

        foreach ($request->productos as $p) {
            $sub  += ($p['cantidad'] ?? 0) * ($p['precio_unitario'] ?? 0);
            $desc += $p['descuento'] ?? 0;
        }

        $porcentajeIva = (float) ($request->tipo_iva ?? 0);
        $iva           = $porcentajeIva > 0 ? ($sub - $desc) * ($porcentajeIva / 100) : 0;
        $total         = ($sub - $desc) + $iva;

        return compact('sub', 'desc', 'iva', 'total');
    }

    private function guardarDetalle(OrdenCompra $orden, array $productos): void
    {
        foreach ($productos as $p) {
            $producto   = Producto::find($p['id_producto']);
            $cantidad   = (float) $p['cantidad'];
            $precio     = (float) $p['precio_unitario'];
            $descuento  = (float) ($p['descuento'] ?? 0);
            $totalLinea = ($cantidad * $precio) - $descuento;

            OrdenCompraDetalle::create([
                'id_orden'        => $orden->id_orden,
                'id_producto'     => $p['id_producto'],
                'codigo_producto' => $producto ? $producto->codigo : null,
                'nombre_producto' => $p['nombre_producto'] ?? ($producto ? $producto->nombre : ''),
                'unidad_medida'   => $p['unidad_medida'] ?? null,
                'cantidad'        => $cantidad,
                'precio_unitario' => $precio,
                'descuento'       => $descuento,
                'total_linea'     => $totalLinea,
            ]);
        }
    }
}