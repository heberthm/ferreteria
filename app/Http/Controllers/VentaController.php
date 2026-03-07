<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = \App\Models\Venta::with('cliente')->latest()->paginate(10);
        return view('ventas', compact('ventas'));
    }

    public function show($id)
    {
        $venta = \App\Models\Venta::with(['cliente', 'detalles.producto'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Datos recibidos en venta:', $request->all());

            $validated = $request->validate([
                'cliente_id'                    => 'nullable',
                'subtotal'                      => 'required|numeric|min:0',
                'iva'                           => 'required|numeric|min:0',
                'total'                         => 'required|numeric|min:0',
                'metodo_pago'                   => 'required|in:efectivo,tarjeta,transferencia,cheque,mixto,credito',
                'tipo_comprobante'              => 'required|in:ticket,factura,boleta',
                'items'                         => 'required|array|min:1',
                'items.*.producto_id'           => 'required',
                'items.*.cantidad'              => 'required|integer|min:1',
                'items.*.precio'                => 'required|numeric|min:0',
            ]);

            // Generar número de factura único
            $ultimaVenta = Venta::latest('id')->first();
            $numero      = $ultimaVenta ? ($ultimaVenta->id + 1) : 1;
            $numeroFactura = 'F-' . str_pad($numero, 5, '0', STR_PAD_LEFT);

            // 1. Crear la venta
            $venta = Venta::create([
                'numero_factura'   => $numeroFactura,
                'cliente_id'       => $request->cliente_id,
                'fecha'            => now(),
                'subtotal'         => $request->subtotal,
                'iva'              => $request->iva,
                'total'            => $request->total,
                'metodo_pago'      => $request->metodo_pago,
                'tipo_comprobante' => $request->tipo_comprobante,
                'referencia_pago'  => $request->referencia_pago,
                'efectivo_recibido'=> $request->efectivo_recibido ?? 0,
                'cambio'           => $request->cambio ?? 0,
                'estado'           => 'completada',
                'usuario_id'       => auth()->id(),
            ]);

            // 2. Procesar cada producto
            $idsProductosVendidos = [];

            foreach ($request->items as $itemData) {
                $productoId = $itemData['producto_id'];

                // Buscar producto
                $producto = Producto::where('id', $productoId)
                    ->orWhere('id_producto', $productoId)
                    ->first();

                if (!$producto) {
                    throw new \Exception("Producto con ID $productoId no encontrado");
                }

                // Verificar stock
                $stockDisponible = $producto->stock_actual ?? $producto->stock ?? 0;
                if ($stockDisponible < $itemData['cantidad']) {
                    throw new \Exception(
                        "Stock insuficiente para {$producto->nombre}. Disponible: {$stockDisponible}"
                    );
                }

                // Crear detalle de venta
                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio'],
                    'subtotal'        => $itemData['subtotal'] ?? ($itemData['precio'] * $itemData['cantidad']),
                ]);

                // Actualizar stock del producto
                if (isset($producto->stock_actual)) {
                    $producto->stock_actual -= $itemData['cantidad'];
                } else {
                    $producto->stock -= $itemData['cantidad'];
                }
                $producto->save();

                // Guardar ID para devolver stock actualizado
                $idsProductosVendidos[] = $producto->id;
            }

            DB::commit();

            // 3. Obtener los productos actualizados para devolver al frontend
            $productosActualizados = Producto::whereIn('id', $idsProductosVendidos)
                ->get()
                ->map(function ($p) {
                    return [
                        'id'           => $p->id,
                        'id_producto'  => $p->id_producto ?? $p->id,
                        'nombre'       => $p->nombre,
                        'codigo'       => $p->codigo,
                        'precio'       => $p->precio,
                        'stock'        => $p->stock_actual ?? $p->stock ?? 0,
                        'categoria'    => $p->categoria,
                        'stock_minimo' => $p->stock_minimo ?? 5,
                    ];
                });

            // 4. Cargar venta completa para el ticket
            $ventaCompleta = Venta::with(['cliente', 'detalles.producto'])
                ->find($venta->id);

            return response()->json([
                'success'               => true,
                'message'               => 'Venta procesada exitosamente',
                'venta_id'              => $venta->id,
                'numero_factura'        => $venta->numero_factura,
                'productos_actualizados'=> $productosActualizados,
                'venta_completa'        => $ventaCompleta,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar venta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}