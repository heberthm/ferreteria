<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\CajaMenor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MovimientoCajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $movimientos = MovimientoCaja::with(['cajaMenor', 'usuario'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

            return response()->json([
                'success' => true,
                'movimientos' => $movimientos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar movimientos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    try {
        DB::beginTransaction();

        $validated = $request->validate([
            'caja_menor_id' => 'required|exists:caja_menor,id',
            'tipo_movimiento' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255'
        ]);

        $caja = CajaMenor::findOrFail($request->caja_menor_id);
        $monto = floatval($request->monto);

        // Calcular nuevo saldo
        if ($request->tipo_movimiento === 'ingreso') {
            $nuevoSaldo = $caja->monto_actual + $monto;
        } else {
            if ($caja->monto_actual < $monto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente'
                ], 400);
            }
            $nuevoSaldo = $caja->monto_actual - $monto;
        }

        // Crear movimiento
        $movimiento = MovimientoCajaMenor::create([
            'caja_menor_id' => $caja->id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'monto' => $monto,
            'descripcion' => $request->descripcion,
            'fecha_movimiento' => now(),
            'usuario_id' => auth()->id()
        ]);

        // Actualizar caja
        $caja->update(['monto_actual' => $nuevoSaldo]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Movimiento agregado exitosamente',
            'nuevoSaldo' => $nuevoSaldo,
            'movimiento' => $movimiento
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $movimiento = MovimientoCaja::with(['cajaMenor', 'usuario'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'movimiento' => $movimiento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Movimiento no encontrado: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Obtener movimientos por caja
     */
    public function obtenerPorCaja($cajaId)
    {
        try {
            $movimientos = MovimientoCaja::with('usuario')
                        ->where('caja_menor_id', $cajaId)
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'movimientos' => $movimientos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar movimientos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener movimientos por fecha
     */
    public function obtenerPorFecha(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $movimientos = MovimientoCaja::with(['cajaMenor', 'usuario'])
                        ->whereBetween('fecha_movimiento', [
                            $request->fecha_inicio, 
                            $request->fecha_fin
                        ])
                        ->orderBy('fecha_movimiento', 'desc')
                        ->get();

            $resumen = [
                'total_ingresos' => $movimientos->where('tipo', 'ingreso')->sum('monto'),
                'total_egresos' => $movimientos->where('tipo', 'egreso')->sum('monto'),
                'cantidad_movimientos' => $movimientos->count()
            ];

            return response()->json([
                'success' => true,
                'movimientos' => $movimientos,
                'resumen' => $resumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener movimientos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resumen de movimientos por caja
     */
    public function resumenCaja($cajaId)
    {
        try {
            $movimientos = MovimientoCaja::where('caja_menor_id', $cajaId)->get();

            $resumen = [
                'total_ingresos' => $movimientos->where('tipo', 'ingreso')->sum('monto'),
                'total_egresos' => $movimientos->where('tipo', 'egreso')->sum('monto'),
                'cantidad_movimientos' => $movimientos->count(),
                'saldo_teorico' => $movimientos->sum(function($mov) {
                    return $mov->tipo === 'ingreso' ? $mov->monto : -$mov->monto;
                })
            ];

            return response()->json([
                'success' => true,
                'resumen' => $resumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al generar resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Anular un movimiento
     */
    public function anular($id)
    {
        DB::beginTransaction();
        
        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            $caja = $movimiento->cajaMenor;

            // Verificar que la caja esté abierta
            if ($caja->estado !== 'abierta') {
                return response()->json([
                    'success' => false,
                    'error' => 'No se puede anular movimiento de una caja cerrada'
                ], 400);
            }

            // Revertir el movimiento en la caja
            if ($movimiento->tipo === 'ingreso') {
                // Si era ingreso, restar del saldo
                if ($caja->monto_actual < $movimiento->monto) {
                    return response()->json([
                        'success' => false,
                        'error' => 'No hay suficiente saldo para anular este ingreso'
                    ], 400);
                }
                $caja->monto_actual -= $movimiento->monto;
            } else {
                // Si era egreso, sumar al saldo
                $caja->monto_actual += $movimiento->monto;
            }

            // Marcar movimiento como anulado
            $movimiento->update([
                'anulado' => true,
                'fecha_anulacion' => now(),
                'usuario_anulacion_id' => auth()->id()
            ]);

            $caja->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Movimiento anulado correctamente',
                'nuevo_saldo' => $caja->monto_actual
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => 'Error al anular movimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}