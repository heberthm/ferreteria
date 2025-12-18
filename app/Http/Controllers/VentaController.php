<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validar datos con nombres flexibles
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
                'productos.*.id_producto' => 'required', // Por si usas diferente nombre
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0',
            ]);
            
            // 1. Crear la venta - Usa los nombres exactos de tus columnas
            $venta = Venta::create([
                'numero_factura' => $request->numero_factura,
                'id_cliente' => $request->cliente_id, // Cambia según tu columna
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
                'id_usuario' => auth()->id(), // Cambia según tu columna
            ]);
            
            // 2. Crear detalles de venta y actualizar inventario
            foreach ($request->productos as $productoData) {
                // Obtener el ID del producto
                $productoId = $productoData['producto_id'] ?? $productoData['id_producto'] ?? null;
                
                if (!$productoId) {
                    throw new \Exception("ID de producto no especificado");
                }
                
                // Buscar el producto - usando el nombre correcto de la columna
                $producto = Producto::where('id_producto', $productoId)
                                  ->orWhere('id', $productoId)
                                  ->first();
                
                if (!$producto) {
                    throw new \Exception("Producto con ID $productoId no encontrado");
                }
                
                // Crear detalle de venta
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta ?? $venta->id, // Ajusta según tu columna
                    'id_producto' => $producto->id_producto ?? $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario'],
                    'subtotal' => $productoData['subtotal'],
                ]);
                
                // Actualizar inventario
                $inventario = Inventario::where('id_producto', $producto->id_producto ?? $producto->id)->first();
                
                if ($inventario) {
                    // Verificar stock
                    if ($inventario->stock_actual < $productoData['cantidad']) {
                        throw new \Exception("Stock insuficiente para " . ($producto->nombre ?? 'producto'));
                    }
                    
                    // Reducir stock
                    $stockAnterior = $inventario->stock_actual;
                    $inventario->stock_actual -= $productoData['cantidad'];
                    $inventario->save();
                    
                    // Registrar movimiento (si tienes tabla de movimientos)
                    // MovimientoInventario::create([...]);
                }
                
                // Actualizar stock en producto
                $producto->stock -= $productoData['cantidad'];
                $producto->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta_id' => $venta->id_venta ?? $venta->id,
                'numero_factura' => $venta->numero_factura,
                'venta' => $venta->load(['detalles.producto', 'cliente'])
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log del error para debugging
            \Log::error('Error al procesar venta: ' . $e->getMessage(), [
                'request' => $request->all(),
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