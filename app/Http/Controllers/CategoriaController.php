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
  
                  $actionBtn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-target="#modalMostrarCategoria"  title="Ver datos de categoria" class="fa fa-eye verProfesional"></a>                  
                  <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-target="#modalEditarCategoria"  title="Editar datos de categoria" class="fa fa-edit editarProfesional"></a>
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
        return view('crear_categoria');
    }

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


    public function show(Categoria $categoria)
    {
        return view('categorias', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('editar_categorias', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validated);

        return redirect()->route('categoria')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}