<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        try {
            $clientes = Cliente::select('id_cliente', 'nombre', 'cedula', 'telefono', 'email', 'direccion')
                              ->orderBy('nombre', 'asc')
                              ->get();
            
            return response()->json($clientes);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener clientes: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    private function normalizarCedula($cedula)
    {
        // Quitar TODOS los puntos y espacios
        // Ejemplo: "1.234.567.234" → "1234567234"
        // Ejemplo: "34.567.821" → "34567821"
        return preg_replace('/[\.\s]/', '', $cedula);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Normalizar la cédula (quitar puntos y espacios)
            $cedulaNormalizada = preg_replace('/[\.\s]/', '', $request->cedula);
            
            // Validar con cédula normalizada
            $validator = Validator::make(array_merge($request->all(), [
                'cedula_normalizada' => $cedulaNormalizada
            ]), [
                'nombre' => 'required|string|max:255',
                'cedula' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'telefono' => 'nullable|string|max:50',
                'direccion' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el cliente
            $cliente = Cliente::create([
                'userId' => $request->userId,
                'nombre' => $request->nombre,
                'cedula' => $request->cedula, 
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente guardado exitosamente',
                'cliente' => $cliente
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al guardar cliente: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verificarCliente(Request $request)
    {
        if ($request->get('cedula')) {
            $cedula = $request->get('cedula');
            $data = DB::table("clientes")
                ->where('cedula', $cedula)
                ->count();
            if ($data > 0) {
                echo 'unique';
            } else {
                echo 'not_unique';
            }
        }
    }

    /**
     * Búsqueda de clientes para Select2
     */
   public function buscar(Request $request)
 {
    try {
        $termino = $request->input('q', '');

        $clientes = Cliente::where(function($query) use ($termino) {
                $query->where('nombre', 'LIKE', "%{$termino}%")
                      ->orWhere('cedula', 'LIKE', "%{$termino}%")
                      ->orWhere('email', 'LIKE', "%{$termino}%");
            })
            // IMPORTANTE: Usar 'id' en lugar de 'id_cliente' para Select2
            ->select([
                'id_cliente as id',  // Esto es clave - alias para Select2
                'nombre',
                'cedula',
                'email',
                'telefono',
                'direccion'
            ])
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($cliente) {
                // Formato específico para Select2
                return [
                    'id' => $cliente->id,
                    'text' => $cliente->nombre . ($cliente->cedula ? ' - ' . $cliente->cedula : ''),
                    'nombre' => $cliente->nombre,
                    'cedula' => $cliente->cedula,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'direccion' => $cliente->direccion
                ];
            });

        return response()->json($clientes);

    } catch (\Exception $e) {
        \Log::error('Error en buscar clientes: ' . $e->getMessage());
        return response()->json([]);
    }
}
}