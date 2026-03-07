<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes');
    }

    /**
     * Get data for DataTable.
     */
   public function getData()
{
    try {
        $clientes = Cliente::select([
            'id_cliente',
            'userId',
            'cedula',
            'nombre',
            'telefono',
            'email',
            'direccion',
            'estado',
            'created_at',
            'updated_at'
        ]);

        return DataTables::of($clientes)
            ->addColumn('acciones', function($cliente) {
                return '
                    <div class="btn-group" role="group">
                        <button class="btn btn-info btn-sm btn-ver" data-id="'.$cliente->id_cliente.'" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="'.$cliente->id_cliente.'" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="'.$cliente->id_cliente.'" data-nombre="'.$cliente->nombre.'" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('estado', function($cliente) {
                // Para mostrar en la tabla
                if ($cliente->estado == 'activo') {
                    return '<span class="badge badge-success">Activo</span>';
                } else {
                    return '<span class="badge badge-danger">Inactivo</span>';
                }
            })
            ->filterColumn('estado', function($query, $keyword) {
                // Permitir búsqueda por texto del estado
                $query->where('estado', 'like', "%{$keyword}%");
            })
            ->orderColumn('estado', function($query, $order) {
                // Ordenar por el valor real de la columna
                $query->orderBy('estado', $order);
            })
            ->editColumn('created_at', function($cliente) {
                return $cliente->created_at ? $cliente->created_at->format('d/m/Y H:i') : '';
            })
            ->rawColumns(['estado', 'acciones'])
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
                'cedula' => 'required|string|max:255|unique:clientes,cedula',
                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:50',
                'email' => 'nullable|email|unique:clientes,email',
                'direccion' => 'nullable|string',
                'estado' => 'required|in:activo,inactivo'
            ]);

            $cliente = Cliente::create([
                'userId' => $request->userId,
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'direccion' => $request->direccion,
                'estado' => $request->estado
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Cliente creado correctamente',
                'data' => $cliente
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Cliente no encontrado'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Cliente no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            
            $request->validate([
                'cedula' => 'required|string|max:255|unique:clientes,cedula,'.$id.',id_cliente',
                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:50',
                'email' => 'nullable|email|unique:clientes,email,'.$id.',id_cliente',
                'direccion' => 'nullable|string',
                'estado' => 'required|in:activo,inactivo'
            ]);

            $data = [
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'direccion' => $request->direccion,
                'estado' => $request->estado,
                'userId' => Auth::id()
            ];
            
            $cliente->update($data);

            return response()->json([
                'success' => true, 
                'message' => 'Cliente actualizado correctamente'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
  public function destroy($id)
{
    try {
        $cliente = Cliente::findOrFail($id);
        
        // Verificar si tiene ventas asociadas
        $ventasCount = $cliente->ventas()->count();
        
        if ($ventasCount > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'No se puede eliminar porque tiene ' . $ventasCount . ' ventas asociadas'
            ], 400);
        }
        
        $cliente->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Cliente eliminado correctamente'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Error al eliminar cliente: ' . $e->getMessage()
        ], 500);
    }
}
}