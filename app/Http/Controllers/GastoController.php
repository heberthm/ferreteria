<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\CategoriaGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'diario');
        $mes = $request->get('mes', Carbon::now()->month);
        $anio = $request->get('anio', Carbon::now()->year);
        
        $query = Gasto::with('categoriaGasto')
            ->where('tipo', $tipo);
            
        // Si es tipo mensual, filtrar por mes y año
        if ($tipo == 'mensual') {
            $query->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio);
        } else {
            // Si es diario, mostrar solo el mes y año seleccionados
            $query->whereMonth('fecha', $mes)
                ->whereYear('fecha', $anio);
        }
        
        $gastos = $query->orderBy('fecha', 'desc')->get();
        
        $total = $gastos->sum('monto');
        $categorias = CategoriaGasto::all();
        
        return view('gastos.index', compact('gastos', 'total', 'categorias', 'tipo', 'mes', 'ano'));
    }

    public function create()
    {
        $categorias = CategoriaGasto::all();
        return view('crear_gastos', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
            'id_categoria_gasto' => 'required|exists:categorias_gastos,id',
            'comprobante' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque,otro',
            'tipo' => 'required|in:diario,mensual',
            'estado' => 'required|in:pendiente,pagado',
        ]);

        // Procesar archivo
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        // Asignar usuario actual
        $validated['user_id'] = auth()->id();

        Gasto::create($validated);

        return redirect()->route('gastos', ['tipo' => $validated['tipo']])
            ->with('success', 'Gasto registrado exitosamente.');
    }

    public function edit(Gasto $gasto)
    {
        $categorias = CategoriaGasto::all();
        return view('editar_gasto', compact('gasto', 'categorias'));
    }

    public function update(Request $request, Gasto $gasto)
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
            'categoria_gasto_id' => 'required|exists:categorias_gastos,id',
            'comprobante' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque,otro',
            'tipo' => 'required|in:diario,mensual',
            'estado' => 'required|in:pendiente,pagado',
        ]);

        // Procesar archivo
        if ($request->hasFile('comprobante')) {
            // Eliminar archivo anterior si existe
            if ($gasto->comprobante) {
                Storage::disk('public')->delete($gasto->comprobante);
            }
            
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        $gasto->update($validated);

        return redirect()->route('gasto', ['tipo' => $gasto->tipo])
            ->with('success', 'Gasto actualizado exitosamente.');
    }

    public function destroy(Gasto $gasto)
    {
        // Eliminar comprobante si existe
        if ($gasto->comprobante) {
            Storage::disk('public')->delete($gasto->comprobante);
        }
        
        $tipo = $gasto->tipo;
        $gasto->delete();

        return redirect()->route('gasto', ['tipo' => $tipo])
            ->with('success', 'Gasto eliminado exitosamente.');
    }

    public function reporte(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $gastos = Gasto::with('categoriaGasto')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->get();
            
        $totalDiario = $gastos->where('tipo', 'diario')->sum('monto');
        $totalMensual = $gastos->where('tipo', 'mensual')->sum('monto');
        $totalGeneral = $gastos->sum('monto');
        
        // Agrupar por categoría
        $gastosPorCategoria = $gastos->groupBy('categoria_gasto_id')
            ->map(function ($items, $key) {
                $categoria = $items->first()->categoriaGasto;
                return [
                    'categoria' => $categoria->nombre,
                    'total' => $items->sum('monto'),
                    'porcentaje' => 0, // Se calculará después
                ];
            });
            
        // Calcular porcentajes
        if ($totalGeneral > 0) {
            $gastosPorCategoria = $gastosPorCategoria->map(function ($item) use ($totalGeneral) {
                $item['porcentaje'] = ($item['total'] / $totalGeneral) * 100;
                return $item;
            });
        }
        
        return view('reporte_gasto', compact(
            'gastos', 
            'totalDiario', 
            'totalMensual', 
            'totalGeneral', 
            'gastosPorCategoria',
            'fechaInicio',
            'fechaFin'
        ));
    }
}