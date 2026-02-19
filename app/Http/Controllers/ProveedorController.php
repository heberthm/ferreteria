<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();
        return view('proveedores', compact('proveedores'));
    }

    public function create()
    {
        return view('crear_proveedores');
    }

     /**
     * Listar proveedores
     */
    public function listar()
    {
        try {
            $proveedores = Proveedor::select('id_proveedor', 'nombre')
                ->orderBy('nombre')
                ->get();
            
            return response()->json([
                'success' => true,
                'proveedores' => $proveedores
            ]);
            
        } catch (\Exception $e) {
            // Si la tabla no existe, retornar array vacío
            return response()->json([
                'success' => true,
                'proveedores' => []
            ]);
        }
    }
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('editar_proveedores', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedores')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}
