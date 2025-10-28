<?php

namespace App\Http\Controllers;

use App\Models\CajaMenor;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CajaMenorController extends Controller
{
    public function index()
{
    Log::info('=== CAJA MENOR INDEX ===');
    
    // Buscar caja abierta
    $cajaActual = CajaMenor::where('estado', 'abierta')->first();
    
    // Debug completo
    Log::info('Caja encontrada: ' . ($cajaActual ? 'SÍ' : 'NO'));
    if ($cajaActual) {
        Log::info('Detalles caja - ID: ' . $cajaActual->id_caja . ', Estado: ' . $cajaActual->estado . ', Monto: ' . $cajaActual->monto_actual);
    }
    
    // Contar cajas abiertas
    $totalCajasAbiertas = CajaMenor::where('estado', 'abierta')->count();
    Log::info('Total cajas abiertas: ' . $totalCajasAbiertas);

    return view('caja', compact('cajaActual'));
}
  

public function abrirCaja(Request $request)
{
    Log::info('=== ABRIENDO CAJA ===');
    Log::info('Datos recibidos:', $request->all());

    // Verificar si ya existe una caja abierta
    $cajaAbierta = CajaMenor::where('estado', 'abierta')->exists();
    Log::info('¿Ya existe caja abierta?: ' . ($cajaAbierta ? 'SÍ' : 'NO'));

    if ($cajaAbierta) {
        return response()->json([
            'success' => false,
            'message' => 'Ya existe una caja abierta'
        ], 400);
    }

    DB::beginTransaction();
    try {
        $caja = CajaMenor::create([
            'monto_inicial' => $request->monto_inicial,
            'monto_actual' => $request->monto_inicial,
            'estado' => 'abierta',
            'fecha_apertura' => now(),
            'observaciones_apertura' => $request->observaciones,
            'user_id_apertura' => auth()->id()
        ]);

        Log::info('✅ Caja creada - ID: ' . $caja->id);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Caja abierta correctamente',
            'caja' => $caja
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Error al abrir caja: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al abrir la caja: ' . $e->getMessage()
        ], 500);
    }
}
    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'observaciones' => 'nullable|string|max:500'
        ]);

        $caja = CajaMenor::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta para cerrar'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $caja->update([
                'estado' => 'cerrada',
                'fecha_cierre' => now(),
                'observaciones_cierre' => $request->observaciones,
                'user_id_cierre' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Caja cerrada correctamente',
                'caja' => $caja
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la caja: ' . $e->getMessage()
            ], 500);
        }
    }

 public function registrarMovimiento(Request $request)
{
    Log::info('=== REGISTRANDO MOVIMIENTO ===');
    Log::info('Datos recibidos:', $request->all());

    // Validación
    $validated = $request->validate([
        'tipo' => 'required|in:ingreso,egreso',
        'monto' => 'required|numeric|min:0.01',
        'concepto' => 'required|string|max:255',
        'descripcion' => 'nullable|string|max:500'
    ], [
        'tipo.required' => 'El campo tipo es obligatorio.',
        'tipo.in' => 'El tipo debe ser ingreso o egreso.',
        'monto.required' => 'El campo monto es obligatorio.',
        'monto.numeric' => 'El monto debe ser un número.',
        'monto.min' => 'El monto debe ser mayor a 0.',
        'concepto.required' => 'El campo concepto es obligatorio.',
        'concepto.max' => 'El concepto no debe exceder 255 caracteres.',
        'descripcion.max' => 'La descripción no debe exceder 500 caracteres.'
    ]);

    $caja = CajaMenor::where('estado', 'abierta')->first();

    if (!$caja) {
        Log::warning('No hay caja abierta para registrar movimientos');
        return response()->json([
            'success' => false,
            'message' => 'No hay caja abierta para registrar movimientos'
        ], 400);
    }

    Log::info("Registrando movimiento para caja ID: {$caja->id_caja}");

    DB::beginTransaction();
    try {
        // Crear movimiento - CON CAMPOS CORRECTOS
        $movimientoData = [
            'id_caja' => $caja->id_caja, // Campo corregido
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'concepto' => $request->concepto,
            'descripcion' => $request->descripcion,
            'userId' => auth()->id() // Campo corregido
        ];

        Log::info('Datos del movimiento:', $movimientoData);

        $movimiento = MovimientoCaja::create($movimientoData);

        Log::info("✅ Movimiento creado - ID: {$movimiento->id}");

        // Actualizar monto actual de la caja
        if ($request->tipo === 'ingreso') {
            $caja->increment('monto_actual', $request->monto);
        } else {
            if ($caja->monto_actual < $request->monto) {
                throw new \Exception('Fondos insuficientes en caja. Saldo actual: $' . $caja->monto_actual);
            }
            $caja->decrement('monto_actual', $request->monto);
        }

        // Recargar la caja para obtener el valor actualizado
        $caja->refresh();

        Log::info("🔄 Nuevo saldo: {$caja->monto_actual}");

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Movimiento registrado correctamente',
            'movimiento' => $movimiento,
            'nuevo_saldo' => $caja->monto_actual
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Error al registrar movimiento: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al registrar movimiento: ' . $e->getMessage()
        ], 500);
    }
}

public function obtenerMovimientos($id)
{
    try {
        Log::info("🔍 Solicitando movimientos para caja ID: {$id}");

        // Verificar que la caja existe
        $caja = CajaMenor::where('id_caja', $id)->first();
        
        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'Caja no encontrada',
                'movimientos' => [],
                'pagination' => null
            ], 404);
        }

        // Obtener movimientos con paginación Y cargar la relación usuario
        $movimientos = MovimientoCaja::with(['usuario']) // ← CARGAR RELACIÓN USUARIO
            ->where('id_caja', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        Log::info("📊 Movimientos encontrados: " . $movimientos->total());

        // Debug: verificar que se carguen las relaciones
        foreach ($movimientos as $movimiento) {
            Log::info("💰 Movimiento - ID: {$movimiento->id}, Usuario: " . 
                     ($movimiento->usuario ? $movimiento->usuario->name : 'NO CARGADO'));
        }

        return response()->json([
            'success' => true,
            'caja' => [
                'id' => $caja->id_caja,
                'estado' => $caja->estado,
                'monto_actual' => $caja->monto_actual
            ],
            'movimientos' => $movimientos->items(),
            'pagination' => [
                'current_page' => $movimientos->currentPage(),
                'last_page' => $movimientos->lastPage(),
                'per_page' => $movimientos->perPage(),
                'total' => $movimientos->total(),
                'from' => $movimientos->firstItem(),
                'to' => $movimientos->lastItem(),
                'has_more_pages' => $movimientos->hasMorePages(),
                'next_page_url' => $movimientos->nextPageUrl(),
                'prev_page_url' => $movimientos->previousPageUrl()
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Error al obtener movimientos: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar movimientos: ' . $e->getMessage(),
            'movimientos' => [],
            'pagination' => null
        ], 500);
    }
}

    public function generarReporte(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:diario,semanal,mensual',
            'fecha' => 'required|date'
        ]);

        $fecha = Carbon::parse($request->fecha);
        $cajas = CajaMenor::with(['movimientos', 'usuarioApertura', 'usuarioCierre']);

        switch ($request->tipo) {
            case 'diario':
                $cajas->whereDate('fecha_apertura', $fecha);
                break;
            case 'semanal':
                $cajas->whereBetween('fecha_apertura', [
                    $fecha->startOfWeek(),
                    $fecha->endOfWeek()
                ]);
                break;
            case 'mensual':
                $cajas->whereYear('fecha_apertura', $fecha->year)
                      ->whereMonth('fecha_apertura', $fecha->month);
                break;
        }

        $cajas = $cajas->get();

        return response()->json([
            'cajas' => $cajas,
            'resumen' => $this->generarResumen($cajas)
        ]);
    }

    // SOLO UN MÉTODO generarResumen - ELIMINA CUALQUIER DUPLICADO
    private function generarResumen($cajas)
    {
        $totalIngresos = 0;
        $totalEgresos = 0;
        $totalCajas = $cajas->count();

        foreach ($cajas as $caja) {
            foreach ($caja->movimientos as $movimiento) {
                if ($movimiento->tipo === 'ingreso') {
                    $totalIngresos += $movimiento->monto;
                } else {
                    $totalEgresos += $movimiento->monto;
                }
            }
        }

        return [
            'total_cajas' => $totalCajas,
            'total_ingresos' => $totalIngresos,
            'total_egresos' => $totalEgresos,
            'saldo_final' => $totalIngresos - $totalEgresos
        ];
    }
}