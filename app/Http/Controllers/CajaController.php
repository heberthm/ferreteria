<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $caja = Caja::whereDate('fecha_apertura', $hoy)->first();
        
        $totalVentas = 0;
        $totalIngresos = 0;
        $totalEgresos = 0;
        $saldoActual = 0;
        
        if ($caja) {
            $movimientos = MovimientoCaja::where('caja_id', $caja->id)->get();
            
            foreach ($movimientos as $movimiento) {
                if ($movimiento->tipo === 'venta') {
                    $totalVentas += $movimiento->monto;
                } elseif ($movimiento->tipo === 'ingreso') {
                    $totalIngresos += $movimiento->monto;
                } elseif ($movimiento->tipo === 'egreso') {
                    $totalEgresos += $movimiento->monto;
                }
            }
            
            // Calcular saldo actual
            $saldoActual = $caja->monto_inicial + $totalVentas + $totalIngresos - $totalEgresos;
        }
        
        return view('caja.index', compact('caja', 'totalVentas', 'totalIngresos', 'totalEgresos', 'saldoActual'));
    }
    
    public function abrirCaja(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0'
        ]);
        
        $hoy = Carbon::today();
        $cajaExistente = Caja::whereDate('fecha_apertura', $hoy)->first();
        
        if ($cajaExistente) {
            return redirect()->back()->with('error', 'Ya existe una caja abierta para hoy.');
        }
        
        $caja = new Caja();
        $caja->monto_inicial = $request->monto_inicial;
        $caja->fecha_apertura = Carbon::now();
        $caja->estado = Caja::ABIERTA;
        $caja->usuario_id = Auth::id();
        $caja->save();
        
        return redirect()->route('caja.index')->with('success', 'Caja abierta correctamente.');
    }
    
    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);
        
        $hoy = Carbon::today();
        $caja = Caja::whereDate('fecha_apertura', $hoy)->first();
        
        if (!$caja) {
            return redirect()->back()->with('error', 'No hay caja abierta para cerrar.');
        }
        
        // Calcular el saldo esperado
        $totalVentas = 0;
        $totalIngresos = 0;
        $totalEgresos = 0;
        
        $movimientos = MovimientoCaja::where('caja_id', $caja->id)->get();
        
        foreach ($movimientos as $movimiento) {
            if ($movimiento->tipo === 'venta') {
                $totalVentas += $movimiento->monto;
            } elseif ($movimiento->tipo === 'ingreso') {
                $totalIngresos += $movimiento->monto;
            } elseif ($movimiento->tipo === 'egreso') {
                $totalEgresos += $movimiento->monto;
            }
        }
        
        $saldoEsperado = $caja->monto_inicial + $totalVentas + $totalIngresos - $totalEgresos;
        $diferencia = $request->monto_cierre - $saldoEsperado;
        
        // Determinar el estado basado en la diferencia
        if (abs($diferencia) <= 0.05) { // Tolerancia de 5 céntimos
            $estado = Caja::CERRADA;
        } else {
            $estado = Caja::EN_REVISION;
        }
        
        $caja->monto_cierre = $request->monto_cierre;
        $caja->fecha_cierre = Carbon::now();
        $caja->observaciones = $request->observaciones;
        $caja->estado = $estado;
        $caja->save();
        
        $mensaje = $estado === Caja::CERRADA 
            ? 'Caja cerrada correctamente.' 
            : 'Caja cerrada con diferencias. Requiere revisión.';
        
        return redirect()->route('caja.index')->with(
            $estado === Caja::CERRADA ? 'success' : 'warning', 
            $mensaje
        );
    }
    
    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'tipo_movimiento' => 'required|in:ingreso,egreso',
            'monto_movimiento' => 'required|numeric|min:0.01',
            'descripcion_movimiento' => 'required|string'
        ]);
        
        $hoy = Carbon::today();
        $caja = Caja::whereDate('fecha_apertura', $hoy)->first();
        
        if (!$caja) {
            return redirect()->back()->with('error', 'No hay caja abierta para registrar movimientos.');
        }
        
        if (!$caja->estaAbierta()) {
            return redirect()->back()->with('error', 'La caja no está abierta. No se pueden registrar movimientos.');
        }
        
        $movimiento = new MovimientoCaja();
        $movimiento->caja_id = $caja->id;
        $movimiento->tipo = $request->tipo_movimiento;
        $movimiento->monto = $request->monto_movimiento;
        $movimiento->descripcion = $request->descripcion_movimiento;
        $movimiento->fecha = Carbon::now();
        $movimiento->save();
        
        return redirect()->route('caja.index')->with('success', 'Movimiento registrado correctamente.');
    }
    
    public function obtenerMovimientos()
    {
        $hoy = Carbon::today();
        $caja = Caja::whereDate('fecha_apertura', $hoy)->first();
        
        if (!$caja) {
            return response()->json([]);
        }
        
        $movimientos = MovimientoCaja::where('caja_id', $caja->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($movimientos);
    }
    
    public function cambiarEstado(Request $request, Caja $caja)
    {
        $request->validate([
            'estado' => 'required|in:abierta,cerrada,en_revision'
        ]);
        
        // Solo permitir cambiar estado si el usuario es administrador
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No tiene permisos para cambiar el estado de la caja.');
        }
        
        $caja->estado = $request->estado;
        $caja->save();
        
        return redirect()->back()->with('success', 'Estado de caja actualizado correctamente.');
    }
    
    public function historial()
    {
        $cajas = Caja::with('usuario')
            ->orderBy('fecha_apertura', 'desc')
            ->paginate(10);
            
        return view('caja.historial', compact('cajas'));
    }
}