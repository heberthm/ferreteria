<?php

namespace App\Http\Controllers;

use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DetalleFacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalles = DetalleFactura::with(['factura', 'producto'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return response()->json($detalles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_detalle_factura' => 'required|exists:facturas,id',
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'impuesto' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            // Verificar stock disponible (si aplica)
            $producto = Producto::find($request->producto_id);
            if ($producto->stock < $request->cantidad) {
                return response()->json([
                    'error' => 'Stock insuficiente. Disponible: ' . $producto->stock
                ], 400);
            }

            // Calcular valores
            $subtotal = $request->cantidad * $request->precio_unitario;
            $descuentoMonto = $subtotal * ($request->descuento / 100);
            $impuestoMonto = ($subtotal - $descuentoMonto) * ($request->impuesto / 100);
            $totalLinea = $subtotal - $descuentoMonto + $impuestoMonto;

            $detalle = DetalleFactura::create([
                'id_detalle_factura' => $request->factura_id,
                'id_producto' => $request->producto_id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'subtotal' => $subtotal,
                'descuento' => $request->descuento ?? 0,
                'impuesto' => $request->impuesto ?? 0,
                'total_linea' => $totalLinea,
                'observaciones' => $request->observaciones
            ]);

            // Actualizar stock del producto
            $producto->decrement('stock', $request->cantidad);

            // Cargar relaciones para la respuesta
            $detalle->load(['factura', 'producto']);

            return response()->json([
                'message' => 'Detalle de factura creado exitosamente',
                'data' => $detalle
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el detalle de factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detalle = DetalleFactura::with(['factura', 'producto'])->find($id);

        if (!$detalle) {
            return response()->json([
                'error' => 'Detalle de factura no encontrado'
            ], 404);
        }

        return response()->json($detalle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $detalle = DetalleFactura::find($id);

        if (!$detalle) {
            return response()->json([
                'error' => 'Detalle de factura no encontrado'
            ], 404);
        }

        $request->validate([
            'cantidad' => 'sometimes|numeric|min:0.01',
            'precio_unitario' => 'sometimes|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'impuesto' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            // Si cambia la cantidad, ajustar stock
            if ($request->has('cantidad') && $request->cantidad != $detalle->cantidad) {
                $diferencia = $request->cantidad - $detalle->cantidad;
                $producto = Producto::find($detalle->id_producto);
                
                if ($producto->stock < $diferencia) {
                    return response()->json([
                        'error' => 'Stock insuficiente para actualizar. Disponible: ' . $producto->stock
                    ], 400);
                }
                
                $producto->decrement('stock', $diferencia);
            }

            // Recalcular si cambian algunos valores
            $subtotal = $request->cantidad ?? $detalle->cantidad * 
                       ($request->precio_unitario ?? $detalle->precio_unitario);
            
            $descuento = $request->descuento ?? $detalle->descuento;
            $impuesto = $request->impuesto ?? $detalle->impuesto;
            
            $descuentoMonto = $subtotal * ($descuento / 100);
            $impuestoMonto = ($subtotal - $descuentoMonto) * ($impuesto / 100);
            $totalLinea = $subtotal - $descuentoMonto + $impuestoMonto;

            $detalle->update([
                'cantidad' => $request->cantidad ?? $detalle->cantidad,
                'precio_unitario' => $request->precio_unitario ?? $detalle->precio_unitario,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'total_linea' => $totalLinea,
                'observaciones' => $request->observaciones ?? $detalle->observaciones
            ]);

            $detalle->load(['factura', 'producto']);

            return response()->json([
                'message' => 'Detalle de factura actualizado exitosamente',
                'data' => $detalle
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $detalle = DetalleFactura::find($id);

        if (!$detalle) {
            return response()->json([
                'error' => 'Detalle de factura no encontrado'
            ], 404);
        }

        try {
            // Devolver stock al eliminar
            $producto = Producto::find($detalle->producto_id);
            $producto->increment('stock', $detalle->cantidad);

            $detalle->delete();

            return response()->json([
                'message' => 'Detalle de factura eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles por factura
     */
    public function porFactura($facturaId)
    {
        $detalles = DetalleFactura::with('producto')
                    ->where('id_detalle_factura', $facturaId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return response()->json($detalles);
    }

    /**
     * Resumen de ventas por producto
     */
    public function resumenVentasProductos(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->subMonth()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? now()->format('Y-m-d');

        $resumen = DetalleFactura::join('facturas', 'detalle_factura.id_detalle_factura', '=', 'facturas.id')
                    ->join('productos', 'detalle_factura.id_producto', '=', 'productos.id')
                    ->whereBetween('facturas.fecha_emision', [$fechaInicio, $fechaFin])
                    ->selectRaw('
                        productos.nombre as producto,
                        SUM(detalle_factura.cantidad) as total_vendido,
                        SUM(detalle_factura.total_linea) as total_ventas,
                        AVG(detalle_factura.precio_unitario) as precio_promedio
                    ')
                    ->groupBy('productos.id', 'productos.nombre')
                    ->orderBy('total_ventas', 'desc')
                    ->get();

        return response()->json($resumen);
    }
}