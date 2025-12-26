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
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Log para debugging
            Log::info('Datos recibidos en venta:', $request->all());
            
            // Validar datos - Ajusta según tus columnas
            $validated = $request->validate([
                'numero_factura' => 'required|unique:ventas,numero_factura',
                'cliente_id' => 'nullable',
                'subtotal' => 'required|numeric|min:0',
                'iva' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque,mixto',
                'tipo_comprobante' => 'required|in:ticket,factura,boleta',
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required',
                'productos.*.id_producto' => 'nullable',
                'productos.*.id' => 'nullable',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
                'productos.*.precio' => 'nullable|numeric|min:0',
            ]);
            
            // 1. Crear la venta
            $venta = Venta::create([
                'numero_factura' => $request->numero_factura,
                'cliente_id' => $request->cliente_id,
                'fecha' => now(),
                'subtotal' => $request->subtotal,
                'iva' => $request->iva,
                'total' => $request->total,
                'metodo_pago' => $request->metodo_pago,
                'tipo_comprobante' => $request->tipo_comprobante,
                'referencia_pago' => $request->referencia_pago,
                'efectivo_recibido' => $request->efectivo_recibido,
                'cambio' => $request->cambio,
                'estado' => 'completada',
                'usuario_id' => auth()->id(),
            ]);
            
            // 2. Procesar cada producto
            foreach ($request->productos as $productoData) {
                // Determinar el ID del producto (múltiples nombres posibles)
                $productoId = $productoData['producto_id'] 
                    ?? $productoData['id_producto'] 
                    ?? $productoData['id'] 
                    ?? null;
                
                if (!$productoId) {
                    throw new \Exception("ID de producto no especificado");
                }
                
                // Buscar producto
                $producto = Producto::where('id', $productoId)
                    ->orWhere('id_producto', $productoId)
                    ->first();
                
                if (!$producto) {
                    throw new \Exception("Producto con ID $productoId no encontrado");
                }
                
                // Verificar stock
                if ($producto->stock < $productoData['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}");
                }
                
                // Crear detalle de venta
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario'] ?? $productoData['precio'],
                    'subtotal' => $productoData['subtotal'] ?? ($productoData['precio'] * $productoData['cantidad']),
                ]);
                
                // Actualizar stock del producto
                $producto->stock -= $productoData['cantidad'];
                $producto->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta_id' => $venta->id,
                'numero_factura' => $venta->numero_factura
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
            
            Log::error('Error al procesar venta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}