<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
      


    if(request()->ajax()) {
                  
            $id = Categoria::select('id_categoria', 'nombre', 'descripcion')->get();
             return datatables()->of($id)        
                                                                                                         
              ->addColumn('action', 'atencion')
              ->rawColumns(['action'])
              ->addColumn('action', function($data) {  
  
                  $actionBtn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id_categoria.'" data-target="#modalVerCategoria"  title="Ver datos de categoria" class="fa fa-eye verCategoria"></a>                  
                  <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id_categoria.'" data-target="#modalEditarCategoria"  title="Editar datos de categoria" class="fa fa-edit editarCategoria"></a>
                  <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.' title="Eliminar datos del Categoria" class="fa fa-trash eliminarCategoria" style="color: #c47215ff;"></a>';                
                   
                  return $actionBtn;
                 
              })
                        
              ->make(true);
          } 
  
          $categoria = Categoria::select('id_categoria','nombre')->get(); 
         
          return view('categorias');

    }

    public function create()
    {
       
    }


// =============================
// CREAR DATOS DE CATEGORIA
// =============================

   public function store(Request $request)
    {
        $validatedData = $request->validate([
          
            'nombre'              =>    'required|max:90',
            'descripcion'         =>    'required|max:250',
            ]);
   
          try {
          $data = new Categoria;
   
          $data->userId   = $request->userId;
          $data->nombre    = $request->nombre; 
          $data->descripcion  = $request->descripcion;     
         
              
          } catch (\Exception  $exception) {
              return back()->withError($exception->getMessage())->withInput();
          }
          
   
          $data->save();
  
         // $id =$data->id;
       
      //  return response()->json(['success'=>'Successfully']);
          return redirect()->route('categorias');              
    }

// =============================
// MOSTRAR DATOS DE CATEGORIA
// =============================

 public function show($id)
{
     $categoria = Categoria::where('id_categoria', $id)->first();    
    if(!$categoria) {
        return response()->json(['error' => 'Categoría no encontrada'], 404);
    }    
    return response()->json($categoria);
}
   

// =============================
// EDTIAR DATOS DE CATEGORIA
// =============================
   
public function edit($id)
{
    try {
        $categoria = Categoria::findOrFail($id);
        return response()->json($categoria);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }
}

 public function update(Request $request, $id_categoria)
{
    try {
        $categoria = Categoria::find($id_categoria);
        
        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada' // Cambiado de 'Producto' a 'Categoría'
            ], 404);
        }        
        
        // ELIMINAR ESTE BLOQUE DUPLICADO
        // if (!$categoria) {          
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Producto no encontrado'
        //     ], 404);
        // }       

        // Validar campos básicos
        $validated = $request->validate([
            'nombre'        => 'required|string|max:50',
            'descripcion'   => 'required|string|max:255',               
        ]);

        \Log::info('Validación pasada, actualizando categoría...');

        // Preparar datos para actualizar
        $datosActualizar = [
            'nombre'         => $validated['nombre'],
            'descripcion'    => $validated['descripcion'],
        ];

        // Actualizar la categoría
        $categoria->update($datosActualizar);     

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente', // Cambiado el mensaje
            'data' => $categoria
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Error de validación:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        \Log::error('Error al actualizar categoría: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ], 500);
    }
}

// =============================
// ELIMINAR DATOS DE CATEGORIA
// =============================


    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}