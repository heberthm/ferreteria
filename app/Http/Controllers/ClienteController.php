<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Faltaba ;
use Illuminate\Support\Facades\DB; // Faltaba esta importación

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

  // En ClienteController.php - método store
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
        ], [
            
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
            $searchTerm = $request->get('q');
            
            Log::info('Búsqueda de cliente:', ['term' => $searchTerm]);

            // Si el término de búsqueda es muy corto, devolver vacío
            if (strlen($searchTerm) < 2) {
                return response()->json([]);
            }

            // Buscar clientes que coincidan con el término
            $clientes = Cliente::where('nombre', 'LIKE', "%{$searchTerm}%")
                ->orWhere('cedula', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->select('id_cliente as id', 'nombre', 'cedula', 'email', 'telefono') // Cambiado a id_cliente
                ->orderBy('nombre', 'asc')
                ->limit(10)
                ->get();

            Log::info('Clientes encontrados:', ['count' => $clientes->count()]);

            // Devolver en formato array simple para Select2
            return response()->json($clientes->toArray());

        } catch (\Exception $e) {
            Log::error('Error en buscar cliente:', [
                'error' => $e->getMessage(),
                'searchTerm' => $request->get('q')
            ]);
            
            return response()->json([
                'error' => 'Error al buscar clientes'
            ], 500);
        }
    }
}