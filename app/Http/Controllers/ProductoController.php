<?php

namespace App\Http\Controllers;

use App\Models\Producto;

use App\Models\proveedores;

use Illuminate\Http\Request;


class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria'])->get();
        return view('productos', compact('productos'));
    }

    public function create()
    {
    //    $categorias = Categoria::all();
    //    $proveedores = Proveedor::all();
    //      return view('productos', compact('categorias', 'proveedores'));
    }


  public function store(Request $request)
    {
      //  dd($request->all());
     
        $validatedData = $request->validate([
        'codigo'        => 'required|string|max:50|unique:productos',
        'nombre'        => 'required|string|max:255',
        'descripcion'   => 'nullable|string',
        'precio_compra' => 'required|numeric|min:0',
        'precio_venta'  => 'required|numeric|min:0',
        'unidad_medida' => 'required|string|max:50',
        'ubicacion'     => 'nullable|string|max:100',
        'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',   
        'cantidad'      => 'required|integer|min:0',
        'stock_minimo'  => 'required|integer|min:0',
        'stock'         => 'nullable|integer|min:0',
    ]);
     
   

// Calcular margen de ganancia
      $margenGanancia = (($request->precio_venta - $request->precio_compra) / $request->precio_compra) * 100;


    try {
        $data = new Producto;
        
        // Procesar imagen si existe
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('productos', 'public');
            $data->imagen = $imagePath;
        }

        // Asignar campos correctamente
        $data->userId = $request->userId;
        $data->codigo = $request->codigo;
        $data->nombre = $request->nombre;
        $data->descripcion = $request->descripcion;
        $data->precio_compra = $request->precio_compra;
        $data->precio_venta = $request->precio_venta;
        $data->unidad_medida = $request->unidad_medida; 
        $data->ubicacion = $request->ubicacion;
        $data->id_categoria = $request->id_categoria;
        $data->id_proveedor = $request->id_proveedor;
        $data->cantidad = $request->cantidad; 
        $data->stock = $request->cantidad; // Asumiendo que stock es igual a cantidad inicial
        $data->stock_minimo = $request->stock_minimo; 
        $data->stock = $request->stock;
       

        $data->save();

         return redirect()->route('productos');

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $data
        ], 201);

    } catch (\Exception $exception) {
        return response()->json([
            'success' => false,
            'message' => 'Error al crear el producto: ' . $exception->getMessage()
        ], 500);
    }
   
}

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'proveedor', 'inventario']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $producto->load('inventario');
        return view('editar_productos', compact('producto', 'categorias', 'proveedores'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo'        => 'required|string|max:50|unique:productos,codigo,' . $producto->id,
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'ubicacion'     => 'nullable|string|max:100',
            'categoria_id'  => 'required|exists:categorias,id',
            'proveedor_id'  => 'nullable|exists:proveedores,id',
            'cantidad'      => 'required|integer|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'stock_maximo'  => 'nullable|integer|min:0',
        ]);

        $producto->update([
            'codigo'         => $validated['codigo'],
            'nombre'         => $validated['nombre'],
            'descripcion'    => $validated['descripcion'],
            'precio_compra'  => $validated['precio_compra'],
            'precio_venta'   => $validated['precio_venta'],
            'unidad_medida'  => $validated['unidad_medida'],
            'ubicacion'      => $validated['ubicacion'],
            'categoria_id'   => $validated['categoria_id'],
            'proveedor_id'   => $validated['proveedor_id'],
        ]);

        $producto->inventario->update([
            'cantidad'     => $validated['cantidad'],
            'stock_minimo' => $validated['stock_minimo'],
            'stock_maximo' => $validated['stock_maximo'],
        ]);

        return redirect()->route('productos')
            ->with('success', 'Producto actualizado exitosamente.');
   
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos');
    }

}