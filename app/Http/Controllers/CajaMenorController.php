<?php

namespace App\Http\Controllers;

use App\Models\CajaMenor;
use App\Models\MovimientoCaja;
use Yajra\DataTables\DataTables;
use App\Exports\MovimientosCajaExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon;

class CajaMenorController extends Controller
{
    public function index()
    {
      // Buscar caja abierta - forma correcta
        $cajaActual = CajaMenor::where('estado', 'abierta')->first();
        
        // Pasar a la vista
        return view('caja', compact('cajaActual'));
    }

     public function datatable(Request $request)
    {
        try {
                      
            $movimientos = MovimientoCaja::with(['usuario'])
                ->select('movimiento_caja.*')
                ->orderBy('created_at', 'desc');

            return DataTables::of($movimientos)
                ->addColumn('fecha_formateada', function($movimiento) {
                    return $movimiento->created_at ? $movimiento->created_at->format('d/m/Y H:i') : 'N/A';
                })
              ->addColumn('tipo_badge', function($movimiento) {
                $color = $movimiento->tipo === 'ingreso' ? 'success' : 'danger';
                $icono = $movimiento->tipo === 'ingreso' ? '↑' : '↓';
                return "<span class='badge badge-{$color}'>{$icono} " . ucfirst($movimiento->tipo) . "</span>";
            })
                ->addColumn('monto_formateado', function($movimiento) {
                    return '$ ' . number_format($movimiento->monto, 2);
                })
                ->addColumn('estado_badge', function($movimiento) {
                    $estados = [
                        'completado' => ['color' => 'primary', 'texto' => 'COMPLETADO'],
                        'pendiente' => ['color' => 'warning', 'texto' => 'PENDIENTE'],
                        'anulado' => ['color' => 'secondary', 'texto' => 'ANULADO']
                    ];
                    
                    $estado = $estados[$movimiento->estado] ?? $estados['pendiente'];
                    return '<span class="badge bg-' . $estado['color'] . '">' . $estado['texto'] . '</span>';
                })
                ->addColumn('usuario_nombre', function($movimiento) {
                    return $movimiento->usuario->name ?? 'N/A';
                })
                ->rawColumns(['tipo_badge', 'estado_badge'])
                ->make(true);

        } catch (\Exception $e) {
          
            
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error al cargar datos'
            ], 500);
        }
    }


    
     public function abrirCaja(Request $request)
    {
        Log::info('=== ABRIENDO CAJA ===');
        Log::info('Datos recibidos:', $request->all());

        // ✅ Usar el helper validate() de Laravel (RECOMENDADO)
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar si ya existe una caja abierta HOY
        $cajaAbierta = CajaMenor::where('estado', 'abierta')
            ->whereDate('fecha_apertura', today())
            ->exists();

        Log::info('¿Ya existe caja abierta hoy?: ' . ($cajaAbierta ? 'SÍ' : 'NO'));

        if ($cajaAbierta) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una caja abierta hoy'
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
            Log::info('✅ Monto inicial: ' . $request->monto_inicial);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Caja abierta correctamente',
                'caja' => $caja
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error al abrir caja: ' . $e->getMessage());
            Log::error('❌ Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir la caja: ' . $e->getMessage()
            ], 500);
        }
    }
    

 public function cerrarCaja(Request $request)
    {
        DB::beginTransaction();
        
        try {
            Log::info('🔧 DEBUG - Datos recibidos en cerrarCaja:', $request->all());
            
            // VALIDACIÓN MÁS FLEXIBLE
            $validated = $request->validate([
                'observaciones' => 'sometimes|nullable|string|max:500',
                'saldo_final' => 'required|numeric|min:0.01' // mínimo 0.01
            ], [
                'saldo_final.required' => 'El saldo final es obligatorio',
                'saldo_final.numeric' => 'El saldo final debe ser un número',
                'saldo_final.min' => 'El saldo final debe ser mayor a 0'
            ]);

            Log::info('✅ Validación pasada', $validated);

            // Buscar caja abierta
            $cajaAbierta = CajaMenor::where('estado', 'abierta')->first();

            if (!$cajaAbierta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta para cerrar'
                ], 404);
            }

            // Asegurar que observaciones tenga un valor por defecto si es null
            $observaciones = $validated['observaciones'] ?? 'Cierre normal de caja';

            // Actualizar caja
            $cajaAbierta->update([
                'estado' => 'cerrada',
                'fecha_cierre' => now(),
                'monto_cierre' => $validated['saldo_final'],
                'observaciones' => $observaciones,
                'usuario_cierre_id' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Caja cerrada exitosamente',
                'caja' => [
                    'id' => $cajaAbierta->id,
                    'estado' => $cajaAbierta->estado,
                    'monto_cierre' => $cajaAbierta->monto_cierre
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            Log::error('❌ Error de validación:', $e->errors());
            
            // Devolver errores específicos
            return response()->json([
                'success' => false,
                'message' => 'Error en los datos del formulario',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error general: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar a Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $fecha = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "movimientos_caja_{$fecha}.xlsx";

            return Excel::download(new MovimientosCajaExport($request), $filename);

        } catch (\Exception $e) {
            Log::error('Error exportando Excel: ' . $e->getMessage());
            return back()->with('error', 'Error al exportar a Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exportar a PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Obtener datos con los mismos filtros del datatable
            $query = MovimientoCaja::with(['usuario', 'caja'])
                ->select([
                    'movimientos_caja.*',
                    'users.name as usuario_nombre',
                    'caja_menor.monto_inicial'
                ])
                ->leftJoin('users', 'movimientos_caja.usuario_id', '=', 'users.id')
                ->leftJoin('caja_menor', 'movimientos_caja.caja_menor_id', '=', 'caja_menor.id')
                ->orderBy('movimientos_caja.created_at', 'desc');

            // Aplicar filtros
            if ($request->has('fecha_inicio') && $request->fecha_inicio) {
                $query->whereDate('movimientos_caja.created_at', '>=', $request->fecha_inicio);
            }

            if ($request->has('fecha_fin') && $request->fecha_fin) {
                $query->whereDate('movimientos_caja.created_at', '<=', $request->fecha_fin);
            }

            if ($request->has('tipo') && $request->tipo) {
                $query->where('movimientos_caja.tipo', $request->tipo);
            }

            $movimientos = $query->get();

            // Estadísticas
            $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
            $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
            $saldoFinal = $totalIngresos - $totalEgresos;

            $data = [
                'movimientos' => $movimientos,
                'totalIngresos' => $totalIngresos,
                'totalEgresos' => $totalEgresos,
                'saldoFinal' => $saldoFinal,
                'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i'),
                'filtros' => $request->all()
            ];

            $pdf = PDF::loadView('exports.movimientos-pdf', $data)
                     ->setPaper('a4', 'landscape')
                     ->setOption('margin-top', 10)
                     ->setOption('margin-bottom', 10)
                     ->setOption('margin-left', 5)
                     ->setOption('margin-right', 5);

            $fecha = Carbon::now()->format('Y-m-d_H-i-s');
            return $pdf->download("movimientos_caja_{$fecha}.pdf");

        } catch (\Exception $e) {
            log::error('Error exportando PDF: ' . $e->getMessage());
            return back()->with('error', 'Error al exportar a PDF: ' . $e->getMessage());
        }
    }

    /**
     * Vista rápida para PDF (opcional)
     */
    public function viewPdf(Request $request)
    {
        try {
            $query = MovimientoCaja::with(['usuario', 'caja'])
                ->select([
                    'movimientos_caja.*',
                    'users.name as usuario_nombre',
                    'caja_menor.monto_inicial'
                ])
                ->leftJoin('users', 'movimientos_caja.usuario_id', '=', 'users.id')
                ->leftJoin('caja_menor', 'movimientos_caja.caja_menor_id', '=', 'caja_menor.id')
                ->orderBy('movimientos_caja.created_at', 'desc');

            // Aplicar filtros
            if ($request->has('fecha_inicio') && $request->fecha_inicio) {
                $query->whereDate('movimientos_caja.created_at', '>=', $request->fecha_inicio);
            }

            if ($request->has('fecha_fin') && $request->fecha_fin) {
                $query->whereDate('movimientos_caja.created_at', '<=', $request->fecha_fin);
            }

            $movimientos = $query->get();

            $totalIngresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
            $totalEgresos = $movimientos->where('tipo', 'egreso')->sum('monto');
            $saldoFinal = $totalIngresos - $totalEgresos;

            $data = [
                'movimientos' => $movimientos,
                'totalIngresos' => $totalIngresos,
                'totalEgresos' => $totalEgresos,
                'saldoFinal' => $saldoFinal,
                'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i')
            ];

            return view('exports.movimientos-pdf', $data);

        } catch (\Exception $e) {
            log::error('Error generando vista PDF: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
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
        try {
            $tipo = $request->tipo; // diario, semanal, mensual
            $fecha = $request->fecha; // 2025-11-06
            
            // Validar que la fecha esté presente
            if (!$fecha) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha es requerida'
                ], 400);
            }

            $fechaBase = Carbon::parse($fecha);
            
            // Consulta base con relaciones
            $query = CajaMenor::with([
                'usuarioApertura',
                'usuarioCierre', 
                'movimientos'
            ]);
            
            // Filtrar por período
            switch ($tipo) {
                case 'diario':
                    $query->whereDate('fecha_apertura', $fechaBase->toDateString());
                    break;
                    
                case 'semanal':
                    $inicioSemana = $fechaBase->startOfWeek()->toDateString();
                    $finSemana = $fechaBase->endOfWeek()->toDateString();
                    $query->whereBetween('fecha_apertura', [$inicioSemana, $finSemana]);
                    break;
                    
                case 'mensual':
                    $inicioMes = $fechaBase->startOfMonth()->toDateString();
                    $finMes = $fechaBase->endOfMonth()->toDateString();
                    $query->whereBetween('fecha_apertura', [$inicioMes, $finMes]);
                    break;
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipo de reporte no válido'
                    ], 400);
            }
            
            // Obtener las cajas
            $cajas = $query->orderBy('fecha_apertura', 'desc')->get();
            
            // Calcular resumen
            $resumen = [
                'total_cajas' => $cajas->count(),
                'total_ingresos' => 0,
                'total_egresos' => 0,
                'saldo_final' => 0
            ];
            
            foreach ($cajas as $caja) {
                foreach ($caja->movimientos as $movimiento) {
                    if ($movimiento->tipo === 'ingreso') {
                        $resumen['total_ingresos'] += $movimiento->monto;
                    } else {
                        $resumen['total_egresos'] += $movimiento->monto;
                    }
                }
            }
            
            $resumen['saldo_final'] = $resumen['total_ingresos'] - $resumen['total_egresos'];
            
            return response()->json([
                'success' => true,
                'cajas' => $cajas,
                'resumen' => $resumen,
                'periodo' => [
                    'tipo' => $tipo,
                    'fecha' => $fecha,
                    'descripcion' => $this->getDescripcionPeriodo($tipo, $fechaBase)
                ]
            ]);
            
        } catch (\Exception $e) {
            log::error('Error generando reporte: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getDescripcionPeriodo($tipo, $fecha)
    {
        switch ($tipo) {
            case 'diario':
                return $fecha->format('d/m/Y');
            case 'semanal':
                return 'Semana del ' . $fecha->startOfWeek()->format('d/m/Y') . ' al ' . $fecha->endOfWeek()->format('d/m/Y');
            case 'mensual':
                return $fecha->format('F Y');
            default:
                return 'Período no especificado';
        }
    }
}