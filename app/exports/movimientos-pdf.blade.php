<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Movimientos de Caja</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background-color: #2c3e50; color: white; padding: 8px; text-align: left; }
        .table td { padding: 6px; border: 1px solid #ddd; }
        .table tr:nth-child(even) { background-color: #f8f9fa; }
        .summary { margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
        .summary h3 { margin-top: 0; color: #2c3e50; }
        .positive { color: #28a745; font-weight: bold; }
        .negative { color: #dc3545; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Movimientos de Caja</h1>
        <p>Generado el: {{ $fechaGeneracion }}</p>
        @if(isset($filtros['fecha_inicio']) || isset($filtros['fecha_fin']))
            <p>
                Período: 
                {{ isset($filtros['fecha_inicio']) ? \Carbon\Carbon::parse($filtros['fecha_inicio'])->format('d/m/Y') : 'Inicio' }} 
                - 
                {{ isset($filtros['fecha_fin']) ? \Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y') : 'Fin' }}
            </p>
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th class="text-right">Monto</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $movimiento)
            <tr>
                <td>{{ $movimiento->id_movimiento_caja }}</td>
                <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $movimiento->descripcion }}</td>
                <td>
                    @if($movimiento->tipo == 'ingreso')
                        <span class="positive">INGRESO</span>
                    @else
                        <span class="negative">EGRESO</span>
                    @endif
                </td>
                <td class="text-right">$ {{ number_format($movimiento->monto, 2) }}</td>
                <td>{{ $movimiento->usuario->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay movimientos registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Resumen</h3>
        <p><strong>Total Ingresos:</strong> <span class="positive">$ {{ number_format($totalIngresos, 2) }}</span></p>
        <p><strong>Total Egresos:</strong> <span class="negative">$ {{ number_format($totalEgresos, 2) }}</span></p>
        <p><strong>Saldo Final:</strong> 
            <span class="{{ $saldoFinal >= 0 ? 'positive' : 'negative' }}">
                $ {{ number_format($saldoFinal, 2) }}
            </span>
        </p>
        <p><strong>Total de Movimientos:</strong> {{ $movimientos->count() }}</p>
    </div>

    <div class="footer">
        <p>Sistema de Caja Menor - Generado automáticamente</p>
    </div>
</body>
</html>