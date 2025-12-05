<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class PuntoVentaController extends Controller
{
    public function index()
    {
        return view('venta');
    }

    
    public function buscarProductos(Request $request)
    {
        $termino = $request->input('termino');
        $categoria = $request->input('categoria', 'todas');
        
        try {
            $query = Producto::query();
            
            // Filtrar por término de búsqueda
            if ($termino) {
                $query->where(function($q) use ($termino) {
                    $q->where('codigo', 'LIKE', "%{$termino}%")
                      ->orWhere('nombre', 'LIKE', "%{$termino}%")
                      ->orWhere('descripcion', 'LIKE', "%{$termino}%");
                });
            }
            
            // Filtrar por categoría
            if ($categoria !== 'todas') {
                $query->where('categoria', $categoria);
            }
            
            // Obtener solo productos con stock disponible
            $query->where('stock', '>', 0);
            
            // Ordenar por nombre
            $query->orderBy('nombre', 'asc');
            
            // Limitar resultados
            $productos = $query->limit(50)->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos,
                'total' => $productos->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar productos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Filtrar productos por categoría
     */
    public function filtrarProductos(Request $request)
    {
        $categoria = $request->input('categoria');
        
        try {
            $query = Producto::where('stock', '>', 0);
            
            if ($categoria && $categoria !== 'todas') {
                $query->where('categoria', $categoria);
            }
            
            $productos = $query->orderBy('nombre', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al filtrar productos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener todos los productos
     */
    public function todosLosProductos()
    {
        try {
            $productos = Producto::where('stock', '>', 0)
                                ->orderBy('nombre', 'asc')
                                ->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener productos frecuentes
     */
    public function productosFrecuentes()
    {
        try {
            // Obtener los productos más vendidos
            $productos = DB::table('productos as p')
                ->join('detalle_ventas as dv', 'p.id', '=', 'dv.producto_id')
                ->select('p.*', DB::raw('SUM(dv.cantidad) as total_vendido'))
                ->where('p.stock', '>', 0)
                ->groupBy('p.id')
                ->orderBy('total_vendido', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos frecuentes: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar stock de producto
     */
    public function verificarStock(Request $request)
    {
        $productoId = $request->input('producto_id');
        $cantidadSolicitada = $request->input('cantidad', 1);
        
        try {
            $producto = Producto::findOrFail($productoId);
            
            $stockDisponible = $producto->stock >= $cantidadSolicitada;
            
            return response()->json([
                'success' => true,
                'disponible' => $stockDisponible,
                'stock_actual' => $producto->stock,
                'producto' => $producto
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }
    
    /**
     * Procesar venta
     */
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'subtotal' => 'required|numeric',
            'iva' => 'required|numeric',
            'total' => 'required|numeric',
            'metodo_pago' => 'required|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear la venta
            $venta = DB::table('ventas')->insertGetId([
                'numero_factura' => $request->input('numero_factura'),
                'cliente_id' => $request->input('cliente_id'),
                'subtotal' => $request->input('subtotal'),
                'iva' => $request->input('iva'),
                'total' => $request->input('total'),
                'metodo_pago' => $request->input('metodo_pago'),
                'usuario_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Procesar cada item
            foreach ($request->input('items') as $item) {
                // Verificar stock
                $producto = Producto::findOrFail($item['producto_id']);
                
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }
                
                // Insertar detalle de venta
                DB::table('detalle_ventas')->insert([
                    'venta_id' => $venta,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $producto->precio * $item['cantidad'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Actualizar stock
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta_id' => $venta
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar venta: ' . $e->getMessage()
            ], 500);
        }
    }

    
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
}