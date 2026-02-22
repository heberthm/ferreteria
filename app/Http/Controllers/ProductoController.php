<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Categoria;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        // Para DataTable - incluir TODOS los campos necesarios
        $productos = Producto::with(['categoria', 'proveedor'])
            ->select(
                'id_producto', 
                'codigo', 
                'nombre', 
                'descripcion', 
                'precio_venta',
                'stock', 
                'stock_minimo', 
                'ubicacion',
                'marca',
                'unidad_medida',
                'imagen', // 👈 ASEGURAR QUE IMAGEN ESTÁ INCLUIDA
                'frecuente',
                'id_categoria',
                'id_proveedor'
            );
        
        return datatables()->of($productos)
            ->addColumn('categoria_nombre', function($producto) {
                return $producto->categoria ? $producto->categoria->nombre : 'Sin categoría';
            })
            ->addColumn('proveedor_nombre', function($producto) {
                return $producto->proveedor ? $producto->proveedor->nombre : 'Sin proveedor';
            })
            ->addColumn('action', function($producto) {
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button class="btn btn-xs btn-info verProducto" data-id="'.$producto->id_producto.'" data-target="#modalVerProducto" title="Ver datos del productos"><i class="fa fa-eye"></i></button>';
                $btn .= '<button class="btn btn-xs btn-warning editarProducto" data-id="'.$producto->id_producto.'" title="Editar datos del productos"><i class="fa fa-check"></i></button>';
                $btn .= '<button class="btn btn-xs btn-danger eliminarProducto" data-id="'.$producto->id_producto.'" data-nombre="'.$producto->nombre.'" title="Eliminar productos"><i class="fa fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action']) 
            ->make(true);
    }
    
    // Para vista normal
    $categorias = Categoria::all();
    $proveedores = Proveedor::all();
    return view('productos', compact('categorias', 'proveedores'));
}

    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $productos = Producto::where('codigo', 'like', "%{$search}%")
                            ->orWhere('nombre', 'like', "%{$search}%")
                            ->orWhere('categoria', 'like', "%{$search}%")
                            ->select('id_producto', 'codigo', 'nombre', 'precio', 'stock', 'stock_minimo', 
                                    'categoria', 'imagen', 'unidad', 'frecuente')
                            ->orderBy('nombre')
                            ->get();
        
        return response()->json($productos);
    }

    public function frecuentes()
    {
        $productos = Producto::where('frecuente', true)
                            ->select('id_producto', 'codigo', 'nombre', 'precio', 'stock', 'stock_minimo', 
                                    'categoria', 'imagen', 'unidad', 'frecuente')
                            ->orderBy('nombre')
                            ->get();
        
        return response()->json($productos);
    }

    public function create()
    {
        // Código original
    }

//Guardar producto

 public function store(Request $request)
{
    try {
        // Validar SOLO los campos que existen en la nueva tabla
        $validatedData = $request->validate([
            'codigo'        => 'required|string|max:50|unique:productos',
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'precio_venta'   => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'stock_minimo'   => 'required|integer|min:0',
            'unidad_medida'  => 'required|string|max:50',
            'ubicacion'      => 'nullable|string|max:100',
            'marca'          => 'nullable|string|max:100',
            'id_categoria'   => 'required|exists:categorias,id_categoria',
            'id_proveedor'   => 'nullable|exists:proveedores,id_proveedor',
            'frecuente'      => 'nullable|boolean',
            'imagen'         => 'nullable|image|max:2048'
        ]);

        // Crear nuevo producto
        $producto = new Producto();
        
        // Asignar campos básicos
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio_venta = $request->precio_venta;
        $producto->stock = $request->stock;
        $producto->stock_minimo = $request->stock_minimo;
        $producto->unidad_medida = $request->unidad_medida;
        $producto->ubicacion = $request->ubicacion;
        $producto->marca = $request->marca;
        $producto->id_categoria = $request->id_categoria;
        $producto->id_proveedor = $request->id_proveedor;
        $producto->frecuente = $request->has('frecuente') ? 1 : 0;
        $producto->activo = 1;

        // Manejo de la imagen
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            $ruta = $imagen->storeAs('public/productos', $nombreImagen);
            $producto->imagen = 'storage/productos/' . $nombreImagen;
        }

        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data' => $producto
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error al guardar producto: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar el producto: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Buscar productos por término y categoría
     */
 
public function buscarProductos(Request $request)
{
    $termino = $request->get('termino');
    
    \Log::info('Buscando productos con término: ' . $termino);
    
    $productos = Producto::where('nombre', 'LIKE', "%{$termino}%")
                        ->orWhere('codigo', 'LIKE', "%{$termino}%")
                        ->limit(10)
                        ->get(['id_producto', 'nombre', 'codigo', 'stock', 'precio_venta']);
    
    \Log::info('Productos encontrados: ' . $productos->count());
    
    return response()->json([
        'success' => true,
        'productos' => $productos
    ]);
}

    /**
     * Filtrar productos por categoría (porCategoria)
     */
    public function porCategoria(Request $request)
    {
        try {
            $categoria = $request->input('categoria', 'todas');

            $query = Producto::where('activo', 1)
                ->where('stock', '>', 0);

            if ($categoria !== 'todas') {
                $query->where('categoria', $categoria);
            }

            $productos = $query->select(['id_producto', 'codigo', 'nombre', 'precio', 'stock', 'categoria'])
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'productos' => $productos,
                'total' => $productos->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'productos' => [],
                'message' => 'Error al filtrar productos'
            ], 500);
        }
    }

public function listarCompras()
{
    $compras = \App\Models\Compra::with('producto')
        ->orderBy('id_compra', 'desc')
        ->get();

    return response()->json(['compras' => $compras]);
}

public function estadisticasCompras()
{
    $hoy = now()->toDateString();

    return response()->json([
        'success'             => true,
        'compras_hoy'         => \App\Models\Compra::whereDate('fecha_compra', $hoy)->count(),
        'total_invertido'     => (float) \App\Models\Compra::whereDate('fecha_compra', $hoy)
                                    ->selectRaw('SUM(cantidad * precio_compra) as total')
                                    ->value('total'),
        'productos_comprados' => (int) \App\Models\Compra::whereDate('fecha_compra', $hoy)->sum('cantidad'),
        'compras_mes'         => \App\Models\Compra::whereMonth('fecha_compra', now()->month)->count(),
    ]);
}

    /**
     * Obtener todos los productos activos
     */
    public function todosLosProductos()
    {
        try {
            $productos = Producto::where('activo', 1) 
                ->where('stock', '>', 0)
                ->select(['id_producto', 'codigo', 'nombre', 'precio_venta as precio', 'stock', 'categoria'])
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'productos' => $productos,
                'total' => $productos->count()
            ]);

        } catch (\Exception $e) {
            // Agrega logging para ver el error
            \Log::error('Error en todosLosProductos: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'productos' => [],
                'message' => 'Error al cargar productos'
            ], 500);
        }
    }

    /**
     * Obtener productos frecuentes
     */
    public function productosFrecuentes()
    {
        try {
            $productos = Producto::where('activo', 1)
                ->where('stock', '>', 0)
                ->select(['id_producto', 'codigo', 'nombre', 'precio', 'stock', 'categoria'])
                ->orderBy('stock', 'desc')
                ->limit(8)
                ->get();

            return response()->json([
                'success' => true,
                'productos' => $productos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'productos' => []
            ], 500);
        }
    }

    public function show($id)
    {
        $producto = Producto::find($id);    
        if(!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }    
        return response()->json($producto);
    }

    public function edit($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            return response()->json($producto);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    }

// Registrar Compra

  public function registrarCompra(Request $request)
{
    Log::info('📥 Recibiendo compra', $request->all());
    
    try {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'id_producto' => 'required|exists:productos,id_producto',
            'cantidad_comprada' => 'required|numeric|min:1',
            'precio_compra' => 'required|numeric|min:0', // Ahora es requerido
            'fecha_compra' => 'required|date',
            'proveedor' => 'nullable|string|max:255',
            'numero_factura' => 'nullable|string|max:50',
            'metodo_pago' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar el producto
        $producto = Producto::findOrFail($request->id_producto);
        
        // Guardar stock anterior y costo promedio anterior
        $stockAnterior = $producto->stock;
        $costoPromedioAnterior = $producto->costo_promedio ?? 0;
        
        // Actualizar stock del producto
        $producto->stock += $request->cantidad_comprada;
        
        // ACTUALIZAR COSTO PROMEDIO PONDERADO
        $producto->actualizarCostoPromedio(
            $request->cantidad_comprada, 
            $request->precio_compra
        );
        
        $producto->save();
        
        // Calcular el nuevo costo promedio para mostrarlo
        $nuevoCostoPromedio = $producto->costo_promedio;
        
        // Registrar en inventario
        $inventario = new Inventario();
        $inventario->id_producto = $producto->id_producto;
        $inventario->tipo_movimiento = 'entrada';
        $inventario->cantidad = $request->cantidad_comprada;
        $inventario->stock_anterior = $stockAnterior;
        $inventario->stock_nuevo = $producto->stock;
        $inventario->costo_unitario = $request->precio_compra; // Costo de esta compra
        $inventario->costo_promedio_anterior = $costoPromedioAnterior;
        $inventario->costo_promedio_nuevo = $nuevoCostoPromedio;
        $inventario->precio_compra = $request->precio_compra;
        $inventario->proveedor = $request->proveedor;
        $inventario->numero_factura = $request->numero_factura;
        $inventario->fecha_movimiento = $request->fecha_compra;
        $inventario->metodo_pago = $request->metodo_pago ?? 'efectivo';
        $inventario->notas = $request->notas;
        $inventario->id_usuario = auth()->id();
        $inventario->save();
        
        Log::info('✅ Compra registrada exitosamente', [
            'inventario_id' => $inventario->id_inventario,
            'producto' => $producto->nombre,
            'costo_promedio_anterior' => $costoPromedioAnterior,
            'costo_nuevo_compra' => $request->precio_compra,
            'costo_promedio_nuevo' => $nuevoCostoPromedio,
            'stock_nuevo' => $producto->stock
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Compra registrada exitosamente',
            'data' => [
                'producto' => [
                    'id' => $producto->id_producto,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo
                ],
                'cantidad_agregada' => $request->cantidad_comprada,
                'costo_compra' => $request->precio_compra,
                'costo_promedio_anterior' => $costoPromedioAnterior,
                'costo_promedio_nuevo' => $nuevoCostoPromedio,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $producto->stock,
                'inventario_id' => $inventario->id_inventario
            ]
        ]);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::error('❌ Producto no encontrado: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado'
        ], 404);
        
    } catch (\Exception $e) {
        Log::error('❌ Error en compra: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la compra: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Obtener historial de costos del producto
 */
public function historialCostos()
{
    return Inventario::where('id_producto', $this->id_producto)
                     ->where('tipo_movimiento', 'entrada')
                     ->orderBy('fecha_movimiento', 'desc')
                     ->get(['fecha_movimiento', 'cantidad', 'costo_unitario', 'costo_promedio_anterior', 'costo_promedio_nuevo', 'proveedor', 'numero_factura']);
}

    //Actualizar producto
public function update(Request $request, $id)
{
    try {
        // Validar SOLO los campos
        // 👇 ELIMINADO: 'unique:productos,codigo,' . $id . ',id_producto'
        $validatedData = $request->validate([
            'codigo'        => 'required|string|max:50|unique:productos',            
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'unidad_medida' => 'required|string|max:50',
            'ubicacion'     => 'nullable|string|max:100',
            'marca'         => 'nullable|string|max:100',
            'id_categoria'  => 'required|exists:categorias,id_categoria',
            'id_proveedor'  => 'nullable|exists:proveedores,id_proveedor',
            'frecuente'     => 'nullable|boolean',
            'imagen'        => 'nullable|image|max:2048'
        ]);

        // Buscar el producto
        $producto = Producto::findOrFail($id);
        
        // Actualizar campos
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio_venta = $request->precio_venta;
        $producto->stock = $request->stock;
        $producto->stock_minimo = $request->stock_minimo;
        $producto->unidad_medida = $request->unidad_medida;
        $producto->ubicacion = $request->ubicacion;
        $producto->marca = $request->marca;
        $producto->id_categoria = $request->id_categoria;
        $producto->id_proveedor = $request->id_proveedor;
        $producto->frecuente = $request->has('frecuente') ? 1 : 0;

        // Manejo de imagen
        if ($request->hasFile('imagen')) {
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }
            
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('storage/productos'), $nombreImagen);
            $producto->imagen = 'storage/productos/' . $nombreImagen;
        }

        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error al actualizar producto: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ], 500);
    }
}


public function destroy($id)
{
    try {
        DB::beginTransaction();

        $producto = Producto::find($id);
        
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar si tiene compras asociadas
        $comprasRelacionadas = \App\Models\Compra::where('id_producto', $id)->count();
        
        if ($comprasRelacionadas > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el producto porque tiene {$comprasRelacionadas} compra(s) asociada(s)"
            ], 422);
        }

        // Verificar si tiene movimientos de inventario
        $inventarioRelacionado = \App\Models\Inventario::where('id_producto', $id)->count();
        
        if ($inventarioRelacionado > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el producto porque tiene {$inventarioRelacionado} movimiento(s) de inventario"
            ], 422);
        }

        // Eliminar imagen física si existe
        if ($producto->imagen) {
            $rutaImagen = public_path(str_replace('storage/', 'storage/app/public/', $producto->imagen));
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        // Eliminar el producto
        $producto->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error al eliminar producto: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el producto: ' . $e->getMessage()
        ], 500);
    }
}

}