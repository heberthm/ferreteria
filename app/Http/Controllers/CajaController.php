<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function index()
    {
        return view('caja');
    }

    public function estado()
    {
        $caja = Caja::where('usuario_id', Auth::id())
                    ->where('estado', 'abierta')
                    ->first();

        if ($caja) {
            $totalVentas = Venta::where('caja_id', $caja->id)
                              ->where('estado', 'completada')
                              ->sum('total');
            
            $totalEfectivo = Venta::where('caja_id', $caja->id)
                                 ->where('estado', 'completada')
                                 ->where('metodo_pago', 'efectivo')
                                 ->sum('total') + $caja->monto_inicial;

            return response()->json([
                'caja_abierta' => true,
                'fecha_apertura' => $caja->created_at->format('d/m/Y'),
                'hora_apertura' => $caja->created_at->format('H:i:s'),
                'monto_inicial' => number_format($caja->monto_inicial, 2),
                'total_ventas' => number_format($totalVentas, 2),
                'total_caja' => number_format($totalEfectivo, 2)
            ]);
        }

        return response()->json(['caja_abierta' => false]);
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255'
        ]);

        // Verificar si ya hay una caja abierta
        $cajaAbierta = Caja::where('usuario_id', Auth::id())
                          ->where('estado', 'abierta')
                          ->exists();

        if ($cajaAbierta) {
            return response()->json(['message' => 'Ya tienes una caja abierta'], 422);
        }

        $caja = Caja::create([
            'usuario_id' => Auth::id(),
            'monto_inicial' => $request->monto_inicial,
            'observaciones' => $request->observaciones,
            'estado' => 'abierta',
            'fecha_apertura' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Caja abierta correctamente']);
    }

    public function cerrar(Request $request)
    {
        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|string|max:255'
        ]);

        $caja = Caja::where('usuario_id', Auth::id())
                   ->where('estado', 'abierta')
                   ->first();

        if (!$caja) {
            return response()->json(['message' => 'No tienes una caja abierta'], 422);
        }

        $totalVentas = Venta::where('caja_id', $caja->id)
                          ->where('estado', 'completada')
                          ->sum('total');

        $caja->update([
            'monto_final' => $request->monto_cierre,
            'observaciones_cierre' => $request->observaciones_cierre,
            'estado' => 'cerrada',
            'fecha_cierre' => Carbon::now(),
            'total_ventas' => $totalVentas
        ]);

        return response()->json(['message' => 'Caja cerrada correctamente']);
    }

    public function historial()
    {
        $cajas = Caja::with('usuario')
                    ->where('usuario_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return response()->json([
            'data' => $cajas->items(),
            'total' => $cajas->total()
        ]);
    }

    public function detalles($id)
    {
        $caja = Caja::with(['usuario', 'ventas' => function($query) {
            $query->where('estado', 'completada');
        }])->findOrFail($id);

        if ($caja->usuario_id != Auth::id()) {
            abort(403);
        }

        $html = view('caja.partials.detalles', compact('caja'))->render();
        
        return $html;
    }
}