<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cotización {{ $cotizacion->numero_cotizacion }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }

        .header { width: 100%; margin-bottom: 20px; }
        .empresa-info { float: left; width: 55%; }
        .cotizacion-info { float: right; width: 40%; text-align: right; }
        .clearfix::after { content: ""; display: table; clear: both; }

        .empresa-nombre { font-size: 18px; font-weight: bold; color: #2c3e50; }
        .empresa-detalle { font-size: 10px; color: #666; margin-top: 3px; }

        .numero-cotizacion { font-size: 22px; font-weight: bold; color: #e74c3c; }
        .estado-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
        }
        .estado-activa    { background: #27ae60; color: white; }
        .estado-vencida   { background: #e74c3c; color: white; }
        .estado-aceptada  { background: #2980b9; color: white; }
        .estado-rechazada { background: #95a5a6; color: white; }

        .divider { border: none; border-top: 2px solid #2c3e50; margin: 15px 0; }
        .divider-light { border: none; border-top: 1px solid #ddd; margin: 10px 0; }

        /* Sección cliente */
        .seccion { margin-bottom: 15px; }
        .seccion-titulo {
            background: #2c3e50;
            color: white;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .seccion-body { padding: 8px 10px; background: #f9f9f9; border: 1px solid #eee; }

        .info-grid { width: 100%; }
        .info-grid td { padding: 2px 5px; font-size: 10px; }
        .info-label { font-weight: bold; color: #555; width: 35%; }

        /* Tabla productos */
        .tabla-productos { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .tabla-productos thead tr { background: #2c3e50; color: white; }
        .tabla-productos th { padding: 6px 8px; text-align: left; font-size: 10px; }
        .tabla-productos th.text-right { text-align: right; }
        .tabla-productos th.text-center { text-align: center; }
        .tabla-productos tbody tr:nth-child(even) { background: #f5f5f5; }
        .tabla-productos tbody tr:nth-child(odd)  { background: #ffffff; }
        .tabla-productos td { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #eee; }
        .tabla-productos td.text-right  { text-align: right; }
        .tabla-productos td.text-center { text-align: center; }

        /* Totales */
        .totales-wrap { width: 100%; margin-top: 10px; }
        .totales-tabla { float: right; width: 45%; border-collapse: collapse; }
        .totales-tabla td { padding: 4px 8px; font-size: 11px; }
        .totales-tabla .label { text-align: right; color: #555; }
        .totales-tabla .valor { text-align: right; font-weight: bold; min-width: 100px; }
        .totales-tabla .fila-total { background: #2c3e50; color: white; }
        .totales-tabla .fila-total td { font-size: 13px; padding: 6px 8px; }

        /* Pie */
        .terminos { margin-top: 20px; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 8px; }
        .footer { margin-top: 15px; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 8px; }

        .text-danger { color: #e74c3c; }
        .text-primary { color: #2980b9; }

        @media print {
        body { margin: 0; padding: 0; }
        @page { margin: 15mm 20mm; }
    }
    </style>
</head>
<body>

    <!-- ENCABEZADO -->
    <div class="header clearfix">
        <div class="empresa-info">
            <div class="empresa-nombre">{{ $empresa['nombre'] }}</div>
            <div class="empresa-detalle">NIT: {{ $empresa['nit'] }}</div>
            <div class="empresa-detalle">{{ $empresa['direccion'] }}</div>
            <div class="empresa-detalle">Tel: {{ $empresa['telefono'] }} | {{ $empresa['email'] }}</div>
        </div>
        <div class="cotizacion-info">
            <div class="numero-cotizacion">{{ $cotizacion->numero_cotizacion }}</div>
            <div style="font-size:10px; color:#666; margin-top:4px;">
                Fecha: <strong>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</strong>
            </div>
            @if($cotizacion->fecha_validez)
            <div style="font-size:10px; color:#666;">
                Válido hasta: <strong>{{ $cotizacion->fecha_validez->format('d/m/Y') }}</strong>
            </div>
            @endif
            <div class="estado-badge estado-{{ $cotizacion->estado }}">
                {{ strtoupper($cotizacion->estado) }}
            </div>
        </div>
    </div>

    <hr class="divider">

    <!-- CLIENTE Y VENDEDOR -->
    <div class="clearfix" style="margin-bottom:15px;">
        <div style="float:left; width:55%;">
            <div class="seccion-titulo">Información del Cliente</div>
            <div class="seccion-body">
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Nombre:</td>
                        <td>{{ $cotizacion->cliente ? $cotizacion->cliente->nombre : $cotizacion->cliente_nombre }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Cédula/NIT:</td>
                        <td>{{ $cotizacion->cliente ? $cotizacion->cliente->cedula : $cotizacion->cliente_cedula }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Teléfono:</td>
                        <td>{{ $cotizacion->cliente ? $cotizacion->cliente->telefono : $cotizacion->cliente_telefono }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Email:</td>
                        <td>{{ $cotizacion->cliente ? $cotizacion->cliente->email : $cotizacion->cliente_email }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="float:right; width:42%;">
            <div class="seccion-titulo">Información de la Cotización</div>
            <div class="seccion-body">
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Vendedor:</td>
                        <td>{{ $cotizacion->vendedor ? $cotizacion->vendedor->name : 'N/A' }}</td>
                    </tr>
                    @if($cotizacion->metodo_pago_sugerido)
                    <tr>
                        <td class="info-label">Método pago:</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $cotizacion->metodo_pago_sugerido)) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- PRODUCTOS -->
    <div class="seccion-titulo">Productos Cotizados</div>
    <table class="tabla-productos">
        <thead>
            <tr>
                <th style="width:8%">Código</th>
                <th style="width:38%">Descripción</th>
                <th style="width:7%" class="text-center">Cant.</th>
                <th style="width:8%">U.M.</th>
                <th style="width:15%" class="text-right">P. Unitario</th>
                <th style="width:12%" class="text-right">Descuento</th>
                <th style="width:12%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->detalles as $detalle)
            <tr>
                <td>{{ $detalle->codigo_producto ?? '—' }}</td>
                <td>{{ $detalle->nombre_producto }}</td>
                <td class="text-center">{{ number_format($detalle->cantidad, 0) }}</td>
                <td>{{ $detalle->unidad_medida ?? '—' }}</td>
                <td class="text-right">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td class="text-right text-danger">${{ number_format($detalle->descuento ?? 0, 0, ',', '.') }}</td>
                <td class="text-right text-primary"><strong>${{ number_format($detalle->total, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALES -->
    <div class="totales-wrap clearfix">
        <table class="totales-tabla">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="valor">${{ number_format($cotizacion->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($cotizacion->descuento > 0)
            <tr>
                <td class="label">Descuento:</td>
                <td class="valor text-danger">-${{ number_format($cotizacion->descuento, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($cotizacion->impuesto > 0)
            <tr>
                <td class="label">IVA:</td>
                <td class="valor">${{ number_format($cotizacion->impuesto, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="fila-total">
                <td class="label" style="color:white; font-weight:bold;">TOTAL:</td>
                <td class="valor" style="color:white;">${{ number_format($cotizacion->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- OBSERVACIONES -->
    @if($cotizacion->observaciones)
    <div style="margin-top:60px;">
        <div class="seccion-titulo">Observaciones</div>
        <div class="seccion-body">{{ $cotizacion->observaciones }}</div>
    </div>
    @endif

    <!-- TÉRMINOS -->
    @if($cotizacion->terminos_condiciones)
    <div class="terminos">
        <strong>Términos y Condiciones:</strong> {{ $cotizacion->terminos_condiciones }}
    </div>
    @endif

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }} |
        {{ $empresa['nombre'] }} — {{ $empresa['email'] }}
    </div>

</body>
</html>