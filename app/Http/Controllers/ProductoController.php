<?php

namespace App\Http\Controllers;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Categoria;
use App\Models\inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class ProductoController extends Controller
{
    public function index()
    {
                  
        if(request()->ajax()) {
                  
            $id = Producto::select('id_producto', 'codigo', 'nombre', 'descripcion','precio_compra', 'stock', 'stock_minimo', 'ubicacion')->get();
             return datatables()->of($id)        
                                                                                                         
              ->addColumn('action', 'atencion')
              ->rawColumns(['action'])
              ->addColumn('action', function($data) {  
  
                  $actionBtn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_producto.'" title="Ver datos del producto" class="fa fa-eye verProducto" style="margin-right: 5px;"></a>                  
                                <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_producto.'" title="Editar datos del producto" class="fa fa-edit editarProducto" style="margin-right: 5px;"></a>
                                <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_producto.'" title="Eliminar datos del producto" class="fa fa-trash eliminarProducto" style="color: #c47215ff;"></a>';        
                  return $actionBtn;
                 
              })
                        
              ->make(true);
          } 
  
          $productos = Producto::select('id_producto','nombre')->get(); 
          $categorias = Categoria::select('nombre')->get();

          return view('productos', compact('categorias'));
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
        'unidad_medida' => 'required|string|max:50',
        'ubicacion'     => 'nullable|string|max:100',
        'proveedor'     => 'nullable|string|max:100',
        'imagen'        => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',   
        'cantidad'      => 'required|integer|min:0',
        'stock_minimo'  => 'required|integer|min:0',
        'stock'         => 'nullable|integer|min:0',
    ]);
     
   

// Calcular margen de ganancia
      $margenGanancia = (($request->precio_venta - $request->precio_compra) / $request->precio_compra) * 100;

    try {

         $data = new Producto;

         
            // Validar la imagen
            $request->validate([
                'imagen' => 'required|image|mimes:webp,jpeg,png,jpg,gif,svg|max:2048',
                'id_prodcto' => 'nullable|integer'
            ]);

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                
                // Generar nombre único para la imagen
                $nombreImagen = Str::random(20) . '_' . time() . '.' . $imagen->getClientOriginalExtension();
                
                // Guardar imagen en storage/app/public/images
                $ruta = $imagen->storeAs('public/images', $nombreImagen);
                
                // Ruta pública para la base de datos
                $rutaPublica = 'storage/images/' . $nombreImagen;
                
                // Aquí guardarías en la base de datos (ejemplo con Eloquent)
                // $producto = Producto::find($request->producto_id);
                // $producto->imagen = $rutaPublica;
                // $producto->save();
              /*  
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen guardada correctamente',
                    'ruta_imagen' => $rutaPublica,
                    'nombre_imagen' => $nombreImagen
                ]);
                */
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
        $data->marca = $request->marca;
        $data->proveedor = $request->proveedor;
        $data->categoria = $request->categoria ;
        $data->id_categoria = $request->id_categoria;
        $data->id_proveedor = $request->id_proveedor;
        $data->cantidad = $request->cantidad; 
        $data->stock = $request->cantidad; // Asumiendo que stock es igual a cantidad inicial
        $data->stock_minimo = $request->stock_minimo; 
        $data->stock = $request->stock;
        $data->imagen = $rutaPublica;
          
               
          } catch (\Exception  $exception) {
              return back()->withError($exception->getMessage())->withInput();
          }
          
        
          $data->save();

            // Respuesta de éxito con el mensaje deseado
           return response()->json(['success'=>'Successfully']);
          
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

  public function update(Request $request, $id_producto)
{
    try {
       
         $producto = Producto::find($id_producto);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }        
        
        if (!$producto) {
          
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
       

        // Validar campos básicos
        $validated = $request->validate([
            'codigo'        => 'required|string|max:50',
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'ubicacion'     => 'nullable|string|max:100',
            'marca'         => 'nullable|string|max:80',
            'cantidad'      => 'required|integer|min:0',
            'stock'         => 'nullable|integer|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'proveedor'     => 'nullable|string|max:80',
            'categoria'     => 'nullable|string|max:80',
            'imagen'        => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',         
        ]);

        \Log::info('Validación pasada, actualizando producto...');

        // Preparar datos para actualizar
        $datosActualizar = [
            'codigo'         => $validated['codigo'],
            'nombre'         => $validated['nombre'],
            'descripcion'    => $validated['descripcion'] ?? null,
            'cantidad'       => $validated['cantidad'],
            'precio_compra'  => $validated['precio_compra'],
            'precio_venta'   => $validated['precio_venta'],
            'unidad_medida'  => $validated['unidad_medida'],
            'ubicacion'      => $validated['ubicacion'] ?? null,
            'marca'          => $validated['marca'] ?? null,
            'categoria'      => $validated['categoria'] ?? null,
            'stock'          => $validated['stock'] ?? 0,
            'stock_minimo'   => $validated['stock_minimo'],
            'proveedor'      => $validated['proveedor'] ?? null,
        ];

     if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            \Log::info('Nueva imagen válida detectada, procesando...');
            
            // Validar tipo de archivo
            $imagen = $request->file('imagen');
            $extensionesPermitidas = ['webp', 'jpeg', 'png', 'jpg', 'gif'];
            $extension = $imagen->getClientOriginalExtension();
            
            if (!in_array(strtolower($extension), $extensionesPermitidas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de archivo no permitido. Formatos aceptados: WEBP, JPEG, PNG, JPG, GIF',
                    'errors' => ['imagen' => ['Tipo de archivo no permitido']]
                ], 422);
            }
            
            // Validar tamaño
            if ($imagen->getSize() > 2 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'La imagen es demasiado grande. Máximo permitido: 2MB',
                    'errors' => ['imagen' => ['La imagen es demasiado grande']]
                ], 422);
            }
            
            // Eliminar imagen anterior si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
                \Log::info('Imagen anterior eliminada: ' . $producto->imagen);
            }
            
            // Guardar nueva imagen
            $nombreImagen = time() . '_' . uniqid() . '.' . $extension;
            $rutaImagen = $imagen->storeAs('productos', $nombreImagen, 'public');
            
            $datosActualizar['imagen'] = $rutaImagen;
            \Log::info('Nueva imagen guardada: ' . $rutaImagen);
            
        } else {
            \Log::info('No se subió nueva imagen o es inválida, manteniendo la actual');
            // Mantener la imagen actual
            $datosActualizar['imagen'] = $producto->imagen;
        }
       
        // Actualizar el producto
        $producto->update($datosActualizar);     

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $producto
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Error de validación:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        \Log::error('Error al actualizar producto: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ], 500);
    }
}

    public function destroy($id)
{
    try {
        $producto = Producto::find($id);
        
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
        
        $producto->delete();
     
        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el producto: ' . $e->getMessage()
        ], 500);
    }
}

}