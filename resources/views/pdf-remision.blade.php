<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Remisión {{ $remision->numero_remision }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            overflow: hidden;
        }
        
        .empresa {
            float: left;
            width: 60%;
        }
        
        .remision-info {
            float: right;
            width: 35%;
            text-align: right;
        }
        
        .cliente-info {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            background-color: #f2f2f2;
        }
        
        .totales {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        
        .totales table {
            width: 100%;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 14px;
            color: #d9534f;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .observaciones {
            margin: 20px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 3px solid #007bff;
        }
        
        .entrega-info {
            margin: 20px 0;
            padding: 10px;
            background: #e8f4f8;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa">
            <h2>{{ $empresa['nombre'] }}</h2>
            <p>NIT: {{ $empresa['nit'] }}<br>
            Dirección: {{ $empresa['direccion'] }}<br>
            Teléfono: {{ $empresa['telefono'] }}<br>
            Email: {{ $empresa['email'] }}</p>
        </div>
        <div class="remision-info">
            <h3>REMISIÓN</h3>
            <p><strong>Número:</strong> {{ $remision->numero_remision }}<br>
            <strong>Fecha:</strong> {{ $remision->fecha_remision ? $remision->fecha_remision->format('d/m/Y') : '—' }}<br>
            <strong>Estado:</strong> {{ $remision->estado_texto }}</p>
        </div>
    </div>

    <div class="cliente-info">
        <h4>DATOS DEL CLIENTE</h4>
        <p><strong>Nombre:</strong> {{ $remision->cliente_nombre }}<br>
        <strong>Cédula/NIT:</strong> {{ $remision->cliente_cedula }}<br>
        @if($remision->cliente_telefono)<strong>Teléfono:</strong> {{ $remision->cliente_telefono }}<br>@endif
        @if($remision->cliente_email)<strong>Email:</strong> {{ $remision->cliente_email }}@endif
        </p>
    </div>

    @if($remision->direccion_entrega || $remision->conductor || $remision->vehiculo_placa)
    <div class="entrega-info">
        <h4>INFORMACIÓN DE ENTREGA</h4>
        @if($remision->direccion_entrega)<p><strong>Dirección de entrega:</strong> {{ $remision->direccion_entrega }}</p>@endif
        @if($remision->conductor)<p><strong>Conductor:</strong> {{ $remision->conductor }}</p>@endif
        @if($remision->vehiculo_placa)<p><strong>Vehículo/Placa:</strong> {{ $remision->vehiculo_placa }}</p>@endif
        @if($remision->fecha_entrega_estimada)<p><strong>Fecha estimada de entrega:</strong> {{ $remision->fecha_entrega_estimada->format('d/m/Y') }}</p>@endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Descuento</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($remision->detalles as $detalle)
            <tr>
                <td>{{ $detalle->codigo_producto }}</td>
                <td>{{ $detalle->nombre_producto }}</td>
                <td>{{ $detalle->unidad_medida }}</td>
                <td style="text-align: center">{{ number_format($detalle->cantidad, 0) }}</td>
                <td style="text-align: right">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td style="text-align: right">${{ number_format($detalle->descuento, 0, ',', '.') }}</td>
                <td style="text-align: right">${{ number_format($detalle->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <table>
            <tr><td style="text-align: right"><strong>Subtotal:</strong></td><td style="text-align: right">${{ number_format($remision->subtotal, 0, ',', '.') }}</td></tr>
            <tr><td style="text-align: right"><strong>Descuento:</strong></td><td style="text-align: right">${{ number_format($remision->descuento, 0, ',', '.') }}</td></tr>
            <tr class="total-final"><td style="text-align: right"><strong>TOTAL:</strong></td><td style="text-align: right"><strong>${{ number_format($remision->total, 0, ',', '.') }}</strong></td></tr>
        </table>
    </div>

    @if($remision->observaciones)
    <div class="observaciones">
        <strong>Observaciones:</strong><br>
        {{ $remision->observaciones }}
    </div>
    @endif

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>