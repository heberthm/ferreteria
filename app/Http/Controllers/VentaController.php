<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Mostrar la vista del punto de venta
     */
    public function index()
    {
        return view('venta');
    }

    /**
     * Buscar productos para el punto de venta
     */
    public function buscarProductos(Request $request)
    {
        $termino = $request->get('q');
        
        $productos = Producto::select('id', 'codigo', 'nombre', 'precio', 'stock', 'categoria', 'unidad', 'stock_minimo')
            ->where('activo', true)
            ->where(function($query) use ($termino) {
                $query->where('codigo', 'LIKE', "%$termino%")
                      ->orWhere('nombre', 'LIKE', "%$termino%")
                      ->orWhere('categoria', 'LIKE', "%$termino%");
            })
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->limit(10)
            ->get();
        
        return response()->json(['success' => true, 'productos' => $productos]);
    }

    /**
     * Obtener todos los productos para el punto de venta
     */
    public function todosProductos()
    {
        $productos = Producto::select('id', 'codigo', 'nombre', 'precio', 'stock', 'categoria', 'unidad', 'stock_minimo')
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();
        
        return response()->json(['success' => true, 'productos' => $productos]);
    }

    /**
     * Buscar clientes para Select2
     */
    public function buscarClientes(Request $request)
    {
        $termino = $request->get('q');
        
        $clientes = Cliente::select('id', 'nombre', 'cedula', 'email', 'telefono', 'direccion')
            ->where('activo', true)
            ->where(function($query) use ($termino) {
                $query->where('nombre', 'LIKE', "%$termino%")
                      ->orWhere('cedula', 'LIKE', "%$termino%")
                      ->orWhere('email', 'LIKE', "%$termino%");
            })
            ->orderBy('nombre')
            ->limit(20)
            ->get();
        
        return response()->json($clientes);
    }

    /**
     * Guardar un nuevo cliente
     */
    public function guardarCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:20|unique:clientes,cedula',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
        ]);

        try {
            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'cedula' => $request->cedula,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'activo' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente' => $cliente
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos frecuentes
     */
    public function productosFrecuentes()
    {
        $productosFrecuentes = DetalleVenta::select(
                'producto_id',
                DB::raw('SUM(cantidad) as total_vendido'),
                DB::raw('MAX(productos.nombre) as nombre'),
                DB::raw('MAX(productos.codigo) as codigo'),
                DB::raw('MAX(productos.precio) as precio'),
                DB::raw('MAX(productos.stock) as stock'),
                DB::raw('MAX(productos.categoria) as categoria')
            )
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('productos.activo', true)
            ->where('productos.stock', '>', 0)
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'DESC')
            ->limit(12)
            ->get();
        
        return response()->json([
            'success' => true,
            'productos' => $productosFrecuentes
        ]);
    }

    /**
     * Procesar una venta
     */
    public function procesarVenta(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'cliente_nombre' => 'nullable|string|max:255',
                'cliente_cedula' => 'nullable|string|max:20',
                'subtotal' => 'required|numeric|min:0',
                'iva' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto,credito,cheque',
                'datos_pago' => 'nullable|array',
                'tipo_comprobante' => 'required|in:ticket,factura,factura_fiscal',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio' => 'required|numeric|min:0',
            ]);

            // Generar número de factura
            $ultimaVenta = Venta::latest()->first();
            $numeroFactura = 'F-' . str_pad(($ultimaVenta ? $ultimaVenta->id : 0) + 1, 5, '0', STR_PAD_LEFT);

            // Crear la venta
            $venta = Venta::create([
                'numero_factura' => $numeroFactura,
                'cliente_id' => $validated['cliente_id'],
                'cliente_nombre' => $validated['cliente_nombre'],
                'cliente_cedula' => $validated['cliente_cedula'],
                'usuario_id' => Auth::id(),
                'subtotal' => $validated['subtotal'],
                'iva' => $validated['iva'],
                'total' => $validated['total'],
                'metodo_pago' => $validated['metodo_pago'],
                'datos_pago' => json_encode($validated['datos_pago'] ?? []),
                'tipo_comprobante' => $validated['tipo_comprobante'],
                'fecha_venta' => now(),
                'estado' => 'completada',
            ]);

            // Crear detalles de venta y actualizar stock
            foreach ($validated['productos'] as $item) {
                $producto = Producto::find($item['id']);
                
                if (!$producto) {
                    throw new \Exception("Producto no encontrado: {$item['id']}");
                }
                
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                }

                // Crear detalle
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'producto_nombre' => $producto->nombre,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad'],
                ]);

                // Actualizar stock
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta_id' => $venta->id,
                'numero_factura' => $numeroFactura,
                'total' => $venta->total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categorías únicas de productos
     */
    public function categorias()
    {
        $categorias = Producto::select('categoria')
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->where('activo', true)
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');
        
        return response()->json(['success' => true, 'categorias' => $categorias]);
    }

    /**
     * Obtener un producto por código
     */
    public function productoPorCodigo($codigo)
    {
        $producto = Producto::where('codigo', $codigo)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->first();
        
        if ($producto) {
            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado'
        ], 404);
    }
}