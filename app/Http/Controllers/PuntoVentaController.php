<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\Inventario;
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
    // 1. PRODUCTOS
    // =============================================

    public function buscarProductos(Request $request)
    {
        $termino   = $request->input('termino');
        $categoria = $request->input('categoria', 'todas');

        try {
            $query = Producto::query();

            if ($termino) {
                $query->where(function ($q) use ($termino) {
                    $q->where('codigo',      'LIKE', "%{$termino}%")
                      ->orWhere('nombre',    'LIKE', "%{$termino}%")
                      ->orWhere('descripcion','LIKE', "%{$termino}%");
                });
            }

            if ($categoria !== 'todas') {
                $query->where('categoria', $categoria);
            }

            $productos = $query->where('stock_actual', '>', 0)
                               ->orderBy('nombre')
                               ->limit(50)
                               ->get();

            return response()->json([
                'success'   => true,
                'productos' => $productos,
                'total'     => $productos->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar productos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function buscarClientes(Request $request)
    {
        $term = $request->get('term', '');
        
        $clientes = Cliente::where('nombre', 'LIKE', "%{$term}%")
                          ->orWhere('email', 'LIKE', "%{$term}%")
                          ->orWhere('documento', 'LIKE', "%{$term}%")
                          ->limit(10)
                          ->get();
        
        $results = [];
        foreach ($clientes as $cliente) {
            $results[] = [
                'id' => $cliente->id,
                'text' => $cliente->nombre . ' - ' . $cliente->documento
            ];
        }
        
        return response()->json($results);
    }

    /**
     * Todos los productos.
     * IMPORTANTE: el JS espera los campos: id, codigo, nombre, precio, stock,
     * categoria, unidad, stock_minimo.
     * Por eso mapeamos precio_venta → precio  y  stock_actual → stock.
     */
    public function todosLosProductos()
    {
        try {
            $productos = Producto::orderBy('nombre')->get()->map(fn($p) => [
                'id'           => $p->id_producto,
                'id_producto'  => $p->id_producto,
                'codigo'       => $p->codigo        ?? '',
                'nombre'       => $p->nombre         ?? 'Sin nombre',
                'precio'       => $p->precio_venta   ?? 0,   // ← alias
                'stock'        => $p->stock_actual   ?? 0,   // ← alias
                'categoria'    => $p->categoria      ?? 'Sin categoría',
                'unidad'       => $p->unidad         ?? 'unidad',
                'stock_minimo' => $p->stock_minimo   ?? 5,
            ]);

            return response()->json(['success' => true, 'productos' => $productos]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos: ' . $e->getMessage(),
            ], 500);
        }
    }
  
    /**
     * Productos frecuentes — los 6 más vendidos (por cantidad en detalle_ventas).
     * Si no hay ventas aún, devuelve los 6 con mayor stock_actual.
     * IMPORTANTE: alias precio y stock para que el JS los lea correctamente.
     */
    public function productosFrecuentes()
    {
        try {
            // Intentar obtener los más vendidos por historial de ventas
            $frecuentes = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                ->select(
                    'productos.id_producto  as id',
                    'productos.codigo',
                    'productos.nombre',
                    'productos.precio_venta as precio',   // ← alias "precio"
                    'productos.stock_actual as stock',    // ← alias "stock"
                    'productos.categoria',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_vendido')
                )
                ->where('productos.stock_actual', '>', 0)
                ->groupBy(
                    'productos.id_producto',
                    'productos.codigo',
                    'productos.nombre',
                    'productos.precio_venta',
                    'productos.stock_actual',
                    'productos.categoria'
                )
                ->orderBy('total_vendido', 'desc')
                ->limit(6)
                ->get();

            // Si no hay historial de ventas, usar los de mayor stock
            if ($frecuentes->isEmpty()) {
                $frecuentes = DB::table('productos')
                    ->select(
                        'id_producto        as id',
                        'codigo',
                        'nombre',
                        'precio_venta       as precio',   // ← alias "precio"
                        'stock_actual       as stock',    // ← alias "stock"
                        'categoria'
                    )
                    ->where('stock_actual', '>', 0)
                    ->orderBy('stock_actual', 'desc')
                    ->limit(6)
                    ->get();
            }

            return response()->json([
                'success'   => true,
                'productos' => $frecuentes,
                'count'     => $frecuentes->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => 'Error: ' . $e->getMessage(),
                'productos' => [],
            ]);
        }
    }

    public function verificarProductos()
    {
        try {
            return response()->json([
                'success'           => true,
                'columns_available' => DB::getSchemaBuilder()->getColumnListing('productos'),
                'statistics' => [
                    'total_products' => DB::table('productos')->count(),
                    'with_stock'     => DB::table('productos')->where('stock_actual', '>', 0)->count(),
                ],
                'sample_products' => DB::table('productos')
                    ->select('id_producto', 'codigo', 'nombre', 'precio_venta', 'stock_actual')
                    ->limit(3)->get(),
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // 2. VENTAS
    // =============================================

    private function generarNumeroFactura(): string
    {
        try {
            $ultima = DB::table('ventas')
                ->where('numero_factura', 'LIKE', 'F-%')
                ->orderByRaw('CAST(SUBSTRING(numero_factura, 3) AS UNSIGNED) DESC')
                ->value('numero_factura');

            $siguiente = $ultima
                ? (int) explode('-', $ultima)[1] + 1
                : 1;

            return 'F-' . str_pad($siguiente, 5, '0', STR_PAD_LEFT);

        } catch (\Exception $e) {
            return 'F-' . date('Ymd') . '-' . rand(100, 999);
        }
    }

    /**
     * Procesar venta — UN SOLO try/catch limpio, sin código duplicado.
     * Devuelve productos_actualizados con alias precio/stock para que
     * el JS pueda refrescar la tabla sin recargar la página.
     */
    public function procesarVenta(Request $request)
    {
        \Log::info('📥 procesarVenta - datos recibidos:', $request->all());

        // ── VALIDACIÓN ────────────────────────────────────────────────
        $validator = Validator::make($request->all(), [
            'cliente_id'          => 'nullable|exists:clientes,id_cliente',
            'subtotal'            => 'required|numeric|min:0',
            'iva'                 => 'required|numeric|min:0',
            'total'               => 'required|numeric|min:0',
            'metodo_pago'         => 'required|in:efectivo,tarjeta,transferencia,mixto,credito,cheque',
            'tipo_comprobante'    => 'required|in:ticket,factura,factura_fiscal',
            'referencia_pago'     => 'nullable|string|max:100',
            'efectivo_recibido'   => 'nullable|numeric|min:0',
            'cambio'              => 'nullable|numeric|min:0',
            'items'               => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id_producto',
            'items.*.cantidad'    => 'required|integer|min:1',
            'items.*.precio'      => 'required|numeric|min:0',
            'items.*.subtotal'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // ── 1. VERIFICAR STOCK ─────────────────────────────────────
            $productosVenta = [];

            foreach ($request->items as $item) {
                $producto = Producto::find($item['producto_id']);

                if (!$producto) {
                    throw new \Exception("Producto ID {$item['producto_id']} no encontrado");
                }

                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception(
                        "Stock insuficiente para '{$producto->nombre}'. " .
                        "Disponible: {$producto->stock_actual}, Solicitado: {$item['cantidad']}"
                    );
                }

                $productosVenta[$item['producto_id']] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio'   => $item['precio'],
                    'subtotal' => $item['subtotal'],
                ];
            }

            // ── 2. NÚMERO DE FACTURA ───────────────────────────────────
            $numeroFactura = $this->generarNumeroFactura();

            // ── 3. CREAR VENTA ─────────────────────────────────────────
            $venta = new Venta();
            $venta->numero_factura    = $numeroFactura;
            $venta->id_cliente        = $request->cliente_id;
            $venta->nombre_usuario    = auth()->user()->name ?? 'Usuario Sistema';
            $venta->subtotal          = $request->subtotal;
            $venta->iva               = $request->iva;
            $venta->total             = $request->total;
            $venta->metodo_pago       = $request->metodo_pago;
            $venta->tipo_comprobante  = $request->tipo_comprobante;
            $venta->referencia_pago   = $request->referencia_pago;
            $venta->efectivo_recibido = $request->efectivo_recibido ?? 0;
            $venta->cambio            = $request->cambio ?? 0;
            $venta->userId            = auth()->id() ?? 1;
            $venta->fecha_venta       = Carbon::now();
            $venta->estado            = 'completada';
            $venta->productos         = array_map(fn($d) => [
                'id_producto'     => $d['producto']->id_producto,
                'codigo'          => $d['producto']->codigo,
                'nombre'          => $d['producto']->nombre,
                'cantidad'        => $d['cantidad'],
                'precio_unitario' => $d['precio'],
                'subtotal'        => $d['subtotal'],
            ], array_values($productosVenta));
            $venta->save();

            // ── 4. DETALLES + DESCONTAR STOCK ─────────────────────────
            foreach ($productosVenta as $id => $data) {
                $detalle                  = new DetalleVenta();
                $detalle->id_venta        = $venta->id_venta;
                $detalle->id_producto     = $data['producto']->id_producto;
                $detalle->cantidad        = $data['cantidad'];
                $detalle->precio_unitario = $data['precio'];
                $detalle->subtotal        = $data['subtotal'];
                $detalle->save();

                $stockAnterior = $data['producto']->stock_actual;

                // ← DESCONTAR STOCK
                $data['producto']->stock_actual -= $data['cantidad'];
                $data['producto']->save();

                Inventario::create([
                    'id_producto'      => $data['producto']->id_producto,
                    'tipo_movimiento'  => 'salida',
                    'cantidad'         => $data['cantidad'],
                    'stock_anterior'   => $stockAnterior,
                    'stock_nuevo'      => $data['producto']->stock_actual,
                    'id_venta'         => $venta->id_venta,
                    'fecha_movimiento' => now(),
                    'notas'            => "Venta #{$venta->numero_factura}",
                    'usuario_id'       => auth()->id(),
                ]);

                \Log::info("📦 {$data['producto']->nombre}: {$stockAnterior} → {$data['producto']->stock_actual}");
            }

            // ── 5. PAGO ────────────────────────────────────────────────
            $pago              = new Pago();
            $pago->id_venta    = $venta->id_venta;
            $pago->metodo_pago = $request->metodo_pago;
            $pago->monto       = $request->total;
            $pago->referencia  = $request->referencia_pago;
            $pago->estado      = 'completado';
            $pago->save();

            DB::commit();

            // ── 6. RESPUESTA ───────────────────────────────────────────
            $venta->load(['cliente', 'detalles.producto', 'pago']);

            // Stock ya descontado — con alias precio/stock para el JS
            $productosActualizados = Producto::whereIn('id_producto', array_keys($productosVenta))
                ->get()
                ->map(fn($p) => [
                    'id'           => $p->id_producto,
                    'id_producto'  => $p->id_producto,
                    'codigo'       => $p->codigo        ?? '',
                    'nombre'       => $p->nombre         ?? '',
                    'precio'       => $p->precio_venta   ?? 0,   // ← alias
                    'stock'        => $p->stock_actual   ?? 0,   // ← alias, ya descontado
                    'categoria'    => $p->categoria      ?? '',
                    'unidad'       => $p->unidad         ?? 'unidad',
                    'stock_minimo' => $p->stock_minimo   ?? 5,
                ]);

            \Log::info('✅ Venta OK - productos_actualizados: ' . $productosActualizados->count());

            return response()->json([
                'success'                => true,
                'message'                => 'Venta procesada exitosamente',
                'venta_id'               => $venta->id_venta,
                'numero_factura'         => $venta->numero_factura,
                'venta_completa'         => $venta,
                'productos_actualizados' => $productosActualizados,  // ← JS lo usa para refrescar tabla y frecuentes
                'timestamp'              => Carbon::now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('❌ Error procesarVenta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage(),
            ], 500);
        }
        // ↑ Nada más después de aquí — sin código duplicado
    }

    // =============================================
    // 3. REPORTES
    // =============================================

    public function ventasDelDia()
    {
        try {
            $fecha  = Carbon::today();
            $ventas = Venta::whereDate('fecha_venta', $fecha)
                ->with(['cliente', 'detalles'])
                ->latest()->get();

            return response()->json([
                'success' => true,
                'ventas'  => $ventas,
                'estadisticas' => [
                    'total_ventas'    => $ventas->count(),
                    'total_monto'     => $ventas->sum('total'),
                    'total_productos' => $ventas->sum(fn($v) => $v->detalles->sum('cantidad')),
                    'fecha'           => $fecha->format('Y-m-d'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // 4. COMPROBANTES
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

    public function generarPDF($ventaId, $tipo = 'ticket')
    {
        try {
            $venta = Venta::with(['cliente', 'detalles.producto', 'pago', 'usuario'])
                ->findOrFail($ventaId);

            return \PDF::loadView("comprobantes.{$tipo}", [
                'venta'   => $venta,
                'empresa' => [
                    'nombre'    => 'FERRETERÍA EL MARTILLO',
                    'direccion' => 'Av. Principal #123',
                    'telefono'  => '(555) 123-4567',
                    'email'     => 'ventas@ferreteria.com',
                    'nit'       => 'FME850301XYZ',
                ],
            ])->download("comprobante-{$venta->numero_factura}.pdf");

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}