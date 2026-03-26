<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            $categorias = Categoria::select('id_categoria', 'nombre', 'descripcion')->get();
            
            return datatables()->of($categorias)
                ->addColumn('acciones', function($data) {
                    // Botones con el mismo estilo que cotizaciones
                    return '
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm verCategoria" 
                                    data-id="'.$data->id_categoria.'" 
                                    title="Ver categoría">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm editarCategoria" 
                                    data-id="'.$data->id_categoria.'" 
                                    title="Editar categoría">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm eliminarCategoria" 
                                    data-id="'.$data->id_categoria.'" 
                                    title="Eliminar categoría">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('nombre', function($data) {
                    return '<strong>' . $data->nombre . '</strong>';
                })
                ->editColumn('descripcion', function($data) {
                    return $data->descripcion ?: '<span class="text-muted">Sin descripción</span>';
                })
                ->rawColumns(['nombre', 'descripcion', 'acciones'])
                ->make(true);
        } 
        
        $categorias = Categoria::select('id_categoria', 'nombre')->get(); 
        
        return view('categorias', compact('categorias'));
    }

    // =============================
    // CREAR DATOS DE CATEGORIA
    // =============================
public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'nombre'      => 'required|max:90',
            'descripcion' => 'required|max:250',
        ]);

        $data = new Categoria;
        $data->nombre      = $request->nombre; 
        $data->descripcion = $request->descripcion;
        
        // Solo asignar userId si existe y no es null
        if ($request->has('userId') && $request->userId) {
            $data->userId = $request->userId;
        } else {
            // Opcional: asignar un valor por defecto (0, null, o el ID de un usuario por defecto)
            $data->userId = null; // Esto funcionará después de la migración
        }
        
        $data->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente.'
        ]);
        
    } catch (\Exception  $exception) {
        \Log::error('Error al guardar categoría: ' . $exception->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al crear la categoría: ' . $exception->getMessage()
        ], 500);
    }
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
    // EDITAR DATOS DE CATEGORIA
    // =============================
    public function edit($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            return response()->json($categoria);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Categoría no encontrada'], 404);
        }
    }

    public function update(Request $request, $id_categoria)
    {
        try {
            $categoria = Categoria::find($id_categoria);
            
            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }        

            // Validar campos
            $validated = $request->validate([
                'nombre'      => 'required|string|max:50',
                'descripcion' => 'required|string|max:255',               
            ]);

            // Actualizar la categoría
            $categoria->update([
                'nombre'      => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
            ]);     

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data'    => $categoria
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // =============================
    // ELIMINAR DATOS DE CATEGORIA
    // =============================
   // =============================
// ELIMINAR DATOS DE CATEGORIA
// =============================
public function destroy($id)
{
    try {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente.'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar la categoría: ' . $e->getMessage()
        ], 500);
    }
}
}