<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('proveedores');
    }

    /**
     * Get data for DataTable.
     */
    public function getData()
    {
        try {
            $proveedores = Proveedor::select([
                'id_proveedor',
                'userId',
                'nit',
                'razon_social',
                'nombre_contacto',
                'telefono',
                'email',
                'direccion',
                'created_at',
                'updated_at'
            ]);

            return DataTables::of($proveedores)
                ->addColumn('acciones', function($proveedor) {
                    return '
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm btn-ver" data-id="'.$proveedor->id_proveedor.'" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="'.$proveedor->id_proveedor.'" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="'.$proveedor->id_proveedor.'" data-nombre="'.$proveedor->razon_social.'" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('created_at', function($proveedor) {
                    return $proveedor->created_at ? $proveedor->created_at->format('d/m/Y H:i') : '';
                })
                ->rawColumns(['acciones'])
                ->make(true);
                
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    try {
        $request->validate([
            'userId' => 'required',
            'nit' => 'required|string|max:255',
            'razon_social' => 'required|string|max:255',
            'nombre_contacto' => 'required|string|max:255',
            'telefono' => 'required|string|max:35',
            'email' => 'nullable|email|unique:proveedores,email',
            'direccion' => 'nullable|string'
        ]);

        $proveedor = Proveedor::create([
            'userId' => $request->userId,
            'nit' => $request->nit,
            'razon_social' => $request->razon_social,
            'nombre_contacto' => $request->nombre_contacto,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Proveedor creado correctamente',
            'data' => $proveedor
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Error al crear proveedor: ' . $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
}
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            return response()->json($proveedor);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Proveedor no encontrado'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            return response()->json($proveedor);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Proveedor no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            
            $request->validate([
                'nit' => 'required|string|max:255',
                'razon_social' => 'required|string|max:255',
                'nombre_contacto' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'email' => 'nullable|email|unique:proveedores,email,'.$id.',id_proveedor',
                'direccion' => 'nullable|string'
            ]);

            $data = $request->all();
            $data['userId'] = Auth::id(); // Actualiza el userId
            
            $proveedor->update($data);

            return response()->json([
                'success' => true, 
                'message' => 'Proveedor actualizado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Proveedor eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar proveedor: ' . $e->getMessage()
            ], 500);
        }
    }
}