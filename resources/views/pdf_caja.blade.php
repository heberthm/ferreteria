<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movimientos de Caja - {{ now()->format('d/m/Y') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background-color: #3498db; color: white; padding: 8px; }
        .table td { padding: 6px; border: 1px solid #ddd; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 10px; color: white; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-primary { background-color: #007bff; }
        .bg-warning { background-color: #ffc107; color: black !important; }
        .text-right { text-align: right; }
        .ingreso { background-color: #d4edda; }
        .egreso { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Movimientos de Caja</h2>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
            <tr class="{{ $movimiento->tipo === 'ingreso' ? 'ingreso' : 'egreso' }}">
                <td>{{ $movimiento->fecha->format('d/m/Y H:i') }}</td>
                <td>{{ $movimiento->descripcion }}</td>
                <td>
                    @if($movimiento->tipo === 'ingreso')
                        <span class="badge bg-success">INGRESO</span>
                    @else
                        <span class="badge bg-danger">EGRESO</span>
                    @endif
                </td>
                <td class="text-right">$ {{ number_format($movimiento->monto, 2) }}</td>
                <td>
                    @if($movimiento->estado === 'completado')
                        <span class="badge bg-primary">COMPLETADO</span>
                    @elseif($movimiento->estado === 'pendiente')
                        <span class="badge bg-warning">PENDIENTE</span>
                    @else
                        <span class="badge bg-secondary">ANULADO</span>
                    @endif
                </td>
                <td>{{ $movimiento->usuario->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>