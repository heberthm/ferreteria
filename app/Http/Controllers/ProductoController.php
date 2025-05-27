<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Inventario;
use Illuminate\Http\Request;


class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'proveedor', 'inventario'])->get();
        return view('productos', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        return view('productos', compact('categorias', 'proveedores'));
    }


  public function store(Request $request)
    {
        $validatedData = $request->validate([
          
            'codigo'        => 'required|string|max:50|unique:productos',
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'ubicacion'     => 'nullable|string|max:100',
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',   
            'id_categoria'  => 'required|exists:categorias,id',
            'id_proveedor'  => 'nullable|exists:proveedores,id',
            'cantidad'      => 'required|integer|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'stock_maximo'  => 'nullable|integer|min:0',
           ]);
   
          try {
          $data = new Producto();
   
       //   $data ->user_id           = $request->userId;
          $data->codigo               = $request->codigo;
          $data->nombre               = $request->nombre;
          $data->descripcion          = $request->descripcion;
          $data->precio_compra        = $request->precio_compra;
          $data->precio_venta         = $request->precio_venta;    
          $data->unidad_medica        = $request->unidad_medida; 
          $data->ubicacion            = $request->ubicacion; 
          $data->id_categoria         = $request->id_categoria; 
          $data->id_proveedor         = $request->id_proveedor;   
          $data->cantidad             = $request->cantida; 
          $data->stock                = $request->stock; 
         $data->min_stock             = $request->min_stock; 
              
          } catch (\Exception  $exception) {
              return back()->withError($exception->getMessage())->withInput();
          }
         
   
          $data->save();
  
         // $id =$data->id;
       
         return response()->json(['success'=>'Successfully']);
            
  
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