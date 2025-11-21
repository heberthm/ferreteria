<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'items' => 'required|array|min:1',
                'items.*.producto_id' => 'required|exists:productos,id',
                'items.*.cantidad' => 'required|integer|min:1',
                'subtotal' => 'required|numeric|min:0',
                'iva' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'metodo_pago' => 'required|string',
                'tipo_comprobante' => 'required|string'
            ]);

            // Generar número de factura automático
            $ultimaVenta = Venta::latest()->first();
            $numeroFactura = $ultimaVenta ? 'FAC-' . str_pad((intval(substr($ultimaVenta->numero_factura, 4)) + 1), 6, '0', STR_PAD_LEFT) : 'FAC-000001';

            // Crear la venta
            $venta = Venta::create([
                'numero_factura' => $numeroFactura,
                'id_cliente' => $request->cliente_id,
                'subtotal' => $request->subtotal,
                'iva' => $request->iva,
                'total' => $request->total,
                'metodo_pago' => $request->metodo_pago,
                'tipo_comprobante' => $request->tipo_comprobante,
                'userId' =>  $request->userId,
                'vendedor' => auth()->check() ? auth()->user()->name : 'Sistema'
            ]);

            // Crear detalles de venta y actualizar stock
            foreach ($request->items as $item) {
                $producto = Producto::find($item['producto_id']);
                
                // Verificar stock
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                }

                // Crear detalle
                DetalleVenta::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'total' => $item['cantidad'] * $producto->precio
                ]);

                // Actualizar stock
                $producto->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta' => $venta,
                'numero_factura' => $numeroFactura
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
}