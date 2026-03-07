@extends('layouts.app')

@section('title', 'Ferreteria')

@section('content_header')
    <h1 class="m-0 text-dark">Datos de ventas</h1>
@stop

@section('content')

    <style>
        /* Variables CSS para modo oscuro */
        :root {
            --dashboard-bg: #f4f6f9;
            --dashboard-card-bg: #ffffff;
            --dashboard-text-color: #495057;
            --dashboard-table-header-bg: #f8f9fa;
            --dashboard-table-text: #212529;
            --dashboard-muted-text: #6c757d;
        }

        /* Modo oscuro AdminLTE */
        .dark-mode {
            --dashboard-bg: #343a40;
            --dashboard-card-bg: #454d55;
            --dashboard-text-color: #e1e1e1;
            --dashboard-table-header-bg: #3a4047;
            --dashboard-table-text: #e1e1e1;
            --dashboard-muted-text: #adb5bd;
        }

        /* CORRECCIÓN: Aplicar variables a body en modo oscuro */
        body.dark-mode {
            background-color: var(--dashboard-bg);
            color: var(--dashboard-text-color);
        }

        /* Mantener diseño original de los cards */
        .card-dashboard {
            border-left: 4px solid !important;
            transition: transform 0.3s;
            height: 120px;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            background-color: var(--dashboard-card-bg) !important;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .card-dashboard.primary {
            border-left-color: #0d6efd !important;
        }

        .card-dashboard.success {
            border-left-color: #198754 !important;
        }

        .card-dashboard.warning {
            border-left-color: #ffc107 !important;
        }

        .card-dashboard.danger {
            border-left-color: #dc3545 !important;
        }

        .card-dashboard.info {
            border-left-color: #0dcaf0 !important;
        }

        .card-dashboard .card-icon {
            font-size: 2.5rem !important;
            opacity: 0.8;
        }

        .card-title {
            font-size: 1.8rem !important;
            font-weight: bold !important;
            margin-bottom: 0 !important;
            color: var(--dashboard-text-color) !important;
        }

        .card-subtitle {
            font-size: 0.9rem !important;
            color: var(--dashboard-muted-text) !important;
            margin-bottom: 0.25rem !important;
        }

        /* Ajustes para AdminLTE */
        .content-wrapper {
            background-color: var(--dashboard-bg) !important;
        }

        /* CORRECCIÓN: Aplicar a content-wrapper en modo oscuro */
        .dark-mode .content-wrapper {
            background-color: var(--dashboard-bg) !important;
        }

        .main-header {
            margin-bottom: 0 !important;
        }

        .small-box {
            margin-bottom: 0 !important;
        }

        /* Animaciones y efectos adicionales */
        .stock-bajo {
            animation: pulse 2s infinite;
        }

        /* Estilos para el modo oscuro - Tabla de stock bajo */
[data-bs-theme="dark"] .table-danger,
[data-bs-theme="dark"] .table-warning {
    --bs-table-color: #000000;
    --bs-table-bg: var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-accent-bg)));
    color: var(--bs-table-color) !important;
}

/* Estilos específicos para las celdas en modo oscuro */
[data-bs-theme="dark"] .table-danger td,
[data-bs-theme="dark"] .table-warning td,
[data-bs-theme="dark"] .table-danger th,
[data-bs-theme="dark"] .table-warning th {
    color: #000000 !important;
}

/* Para el texto dentro de las celdas específicamente */
[data-bs-theme="dark"] .table-danger .font-weight-bold,
[data-bs-theme="dark"] .table-warning .font-weight-bold,
[data-bs-theme="dark"] .table-danger .text-muted,
[data-bs-theme="dark"] .table-warning .text-muted {
    color: #000000 !important;
}

/* Estilos para modo oscuro - Tabla stock bajo */
[data-bs-theme="dark"] .table-danger {
    --bs-table-bg: rgba(220, 53, 69, 0.15) !important;
    --bs-table-color: #ffffff !important;
    --bs-table-border-color: rgba(220, 53, 69, 0.3) !important;
    background-color: var(--bs-table-bg) !important;
}

[data-bs-theme="dark"] .table-warning {
    --bs-table-bg: rgba(255, 193, 7, 0.15) !important;
    --bs-table-color: #ffffff !important;
    --bs-table-border-color: rgba(255, 193, 7, 0.3) !important;
    background-color: var(--bs-table-bg) !important;
}

/* Para las celdas específicamente */
[data-bs-theme="dark"] .table-danger td,
[data-bs-theme="dark"] .table-warning td,
[data-bs-theme="dark"] .table-danger th,
[data-bs-theme="dark"] .table-warning th {
    background-color: transparent !important;
    color: #ffffff !important;
    border-color: var(--bs-table-border-color) !important;
}

/* Para el texto dentro de las celdas */
[data-bs-theme="dark"] .table-danger .font-weight-bold,
[data-bs-theme="dark"] .table-warning .font-weight-bold,
[data-bs-theme="dark"] .table-danger .text-muted,
[data-bs-theme="dark"] .table-warning .text-muted {
    color: #f0f0f0 !important;
}

/* Para los badges dentro de la tabla */
[data-bs-theme="dark"] .table-danger .badge-danger,
[data-bs-theme="dark"] .table-warning .badge-warning {
    color: #000000 !important;
}

/* Estilos para modo oscuro - Tabla stock bajo */
[data-bs-theme="dark"] .table-danger {
    --bs-table-bg: rgba(220, 53, 69, 0.15) !important;
    --bs-table-color: #ffffff !important;
    --bs-table-border-color: rgba(220, 53, 69, 0.3) !important;
    background-color: var(--bs-table-bg) !important;
}

[data-bs-theme="dark"] .table-warning {
    --bs-table-bg: rgba(255, 193, 7, 0.15) !important;
    --bs-table-color: #ffffff !important;
    --bs-table-border-color: rgba(255, 193, 7, 0.3) !important;
    background-color: var(--bs-table-bg) !important;
}

/* Para las celdas específicamente */
[data-bs-theme="dark"] .table-danger td,
[data-bs-theme="dark"] .table-warning td,
[data-bs-theme="dark"] .table-danger th,
[data-bs-theme="dark"] .table-warning th {
    background-color: transparent !important;
    color: #ffffff !important;
    border-color: var(--bs-table-border-color) !important;
}

/* Para el texto dentro de las celdas */
[data-bs-theme="dark"] .table-danger .font-weight-bold,
[data-bs-theme="dark"] .table-warning .font-weight-bold,
[data-bs-theme="dark"] .table-danger .text-muted,
[data-bs-theme="dark"] .table-warning .text-muted {
    color: #f0f0f0 !important;
}

/* Para los badges dentro de la tabla */
[data-bs-theme="dark"] .table-danger .badge-danger,
[data-bs-theme="dark"] .table-warning .badge-warning {
    color: #000000 !important;
}

/* Versión alternativa con fondo más oscuro */
[data-bs-theme="dark"] .table-danger.dark-bg {
    --bs-table-bg: rgba(88, 21, 28, 0.8) !important;
    --bs-table-color: #ffffff !important;
    --bs-table-border-color: rgba(220, 53, 69, 0.5) !important;
}

[data-bs-theme="dark"] .table-warning.dark-bg {
    --bs-table-bg: rgba(102, 77, 3, 0.8) !important;
    --bs-table-color: #ffffff !important;
    --bs-table-border-color: rgba(255, 193, 7, 0.5) !important;
}

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .badge-stock {
            font-size: 0.8em !important;
            padding: 0.3em 0.6em !important;
        }

        /* Ajustar tablas para AdminLTE - MODIFICADO para modo oscuro */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075) !important;
        }

        /* CORRECCIÓN: Hover en modo oscuro */
        .dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.075) !important;
        }

        .card {
            background-color: var(--dashboard-card-bg) !important;
            color: var(--dashboard-text-color) !important;
        }

        .card-header {
            background-color: var(--dashboard-table-header-bg) !important;
            border-bottom: 1px solid rgba(0,0,0,.125) !important;
            color: var(--dashboard-text-color) !important;
        }

        /* CORRECCIÓN: Borde en modo oscuro */
        .dark-mode .card-header {
            border-bottom: 1px solid rgba(255,255,255,.125) !important;
        }

        .card-footer {
            background-color: var(--dashboard-table-header-bg) !important;
            border-top: 1px solid rgba(0,0,0,.125) !important;
            color: var(--dashboard-text-color) !important;
        }

        /* CORRECCIÓN: Borde en modo oscuro */
        .dark-mode .card-footer {
            border-top: 1px solid rgba(255,255,255,.125) !important;
        }

        .table {
            color: var(--dashboard-table-text) !important;
        }

        .thead-light {
            background-color: var(--dashboard-table-header-bg) !important;
            color: var(--dashboard-table-text) !important;
        }

        .text-muted {
            color: var(--dashboard-muted-text) !important;
        }

        /* CORRECCIÓN: Select en modo oscuro */
        .dark-mode select.form-control {
            background-color: var(--dashboard-card-bg);
            color: var(--dashboard-text-color);
            border-color: rgba(255,255,255,.125);
        }

        /* CORRECCIÓN: Botones en modo oscuro */
        .dark-mode .btn-tool {
            color: var(--dashboard-text-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-dashboard {
                height: auto;
                min-height: 100px;
            }
            
            .card-dashboard .card-icon {
                font-size: 2rem !important;
            }
            
            .card-title {
                font-size: 1.1rem !important;
            }
        }

/* Estilos para el dashboard */
.updating {
    color: #3498db !important;
    transition: color 0.3s ease;
}

.alert-pulse {
    animation: alertPulse 2s ease-in-out;
}

@keyframes alertPulse {
    0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); }
    100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
}

.loading tr {
    opacity: 0.6;
}

.product-rank {
    width: 24px;
    height: 24px;
    background: #3498db;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.bg-gradient-primary {
    background: linear-gradient(90deg, #3498db, #2ecc71);
}

.card-dashboard {
    transition: all 0.3s ease;
}

.card-dashboard:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Animación para números */
@keyframes countUp {
    from { opacity: 0.5; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.card-title {
    animation: countUp 0.5s ease-out;
}

/* Estilos para el dashboard */
.number {
    font-size: 1.8rem;
    font-weight: bold;
}

.subtle-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 0.9;
    }
}

/* Estilo para las tarjetas cuando se actualizan */
.card-updating {
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}
   /* Estilos para vista previa e impresión */
    @media print {
        body * {
            visibility: hidden;
        }
        .print-content, .print-content * {
            visibility: visible;
        }
        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background-color: white;
        }
        .no-print {
            display: none !important;
        }
        .modal {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: 100% !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .modal-content {
            border: none !important;
            box-shadow: none !important;
            min-height: 100vh !important;
        }
        .modal-header, .modal-footer {
            display: none !important;
        }
        .modal-body {
            padding: 0 !important;
        }
        .vista-previa-buttons,
        .view-navigation,
        .preview-title,
        .preview-container {
            display: none !important;
        }
        
        /* Específico para impresión de ticket */
        .ticket-print {
            font-family: 'Courier New', monospace !important;
            width: 300px !important;
            margin: 0 auto !important;
            padding: 10px !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
            border: none !important;
            background-color: white !important;
            box-shadow: none !important;
            transform: none !important;
            max-height: none !important;
            overflow: visible !important;
        }
        
        /* Específico para impresión de factura */
        .factura-print {
            font-family: Arial, sans-serif !important;
            width: 210mm !important;
            margin: 0 auto !important;
            padding: 20px !important;
            font-size: 14px !important;
            border: none !important;
            background-color: white !important;
            box-shadow: none !important;
            transform: none !important;
        }
    }
    

    /* Estilos específicos para ticket - IDÉNTICOS EN VISTA PREVIA E IMPRESIÓN */
   #vistaPreviaTicket .preview-container {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 70vh !important;
    background-color: #f5f5f5 !important;
    padding: 20px !important;
    margin: 0 !important;
}

#ticketPreview {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    transition: transform 0.2s ease;
}

#ticketPreview > div {
    width: 302px !important;
    max-width: 302px !important;
    min-width: 302px !important;
    margin: 0 auto !important;
    box-sizing: border-box !important;
}
    
    .ticket-header {
        text-align: center;
        border-bottom: 1px dashed #000;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    
    .ticket-item {
        border-bottom: 1px dotted #ccc;
        padding: 4px 0;
        page-break-inside: avoid; /* Evitar que los items se corten al imprimir */
    }
    
    .ticket-item-name {
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .ticket-item-details {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        margin-bottom: 2px;
    }
    
    .ticket-item-total {
        text-align: right;
        font-weight: bold;
        font-size: 11px;
    }
    
    .ticket-footer {
        border-top: 1px dashed #000;
        padding-top: 10px;
        margin-top: 10px;
        text-align: center;
        page-break-inside: avoid; /* Evitar que el footer se corte */
    }
    
    /* Contenedor de items del ticket SIN SCROLL */
    .ticket-items-container {
        margin: 10px 0;
        max-height: none !important; /* Eliminar límite de altura */
        overflow: visible !important; /* Eliminar scroll */
        page-break-inside: auto; /* Permitir salto de página si es necesario */
    }
    
    /* Estilos específicos para factura - IDÉNTICOS EN VISTA PREVIA E IMPRESIÓN */
    .factura-preview, .factura-print {
        font-family: Arial, sans-serif;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 20px;
        font-size: 14px;
        border: 1px solid #ddd;
        background-color: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        page-break-inside: avoid;
    }
    
    .factura-header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    
    .factura-cliente {
        border: 1px solid #000;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f9f9f9;
    }
    
    .factura-items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    .factura-items th {
        border-bottom: 2px solid #000;
        padding: 8px;
        text-align: left;
    }
    
    .factura-items td {
        border-bottom: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    
    .factura-totales {
        margin-top: 20px;
        border-top: 2px solid #000;
        padding-top: 10px;
    }
    
    .text-right {
        text-align: right !important;
    }
    
    .text-center {
        text-align: center !important;
    }
    
    /* Botones de vista previa */
    .vista-previa-buttons {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 15px;
        border-top: 1px solid #ddd;
        margin-top: 20px;
        z-index: 1000;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    
    /* Navegación entre vistas */
    .view-navigation {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    /* Contenedor de vista previa - SIN SCROLL PARA TICKET */
    .preview-container {
        max-height: none !important; /* Permitir altura ilimitada */
        overflow-y: visible !important; /* Eliminar scroll */
        padding: 20px;
        background: #f5f5f5;
        border-radius: 5px;
    }
    
    /* Título de vista previa */
    .preview-title {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }

    </style>

    <div class="container-fluid">
        <!-- Primera fila: Estadísticas principales -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard primary">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Total Ventas Hoy</h6>
                            <h3 class="card-title" id="totalVentasHoy">
                                <span class="loader"></span>
                            </h3>
                          
                        </div>
                        <div class="card-icon text-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard success">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Ingresos Totales hoy</h6>
                            <h3 class="card-title" id="ingresosTotales">
                                <span class="loader"></span>
                            </h3>
                            <small class="text-muted" id="tendenciaIngresos"></small>
                        </div>
                        <div class="card-icon text-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard warning">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Promedio por Venta</h6>
                            <h3 class="card-title" id="promedioVenta">
                                <span class="loader"></span>
                            </h3>
                           
                        </div>
                        <div class="card-icon text-warning">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard danger">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Alerta Stock Bajo</h6>
                            <h3 class="card-title" id="alertasStock">
                                <span class="loader"></span>
                            </h3>
                           
                        </div>
                        <div class="card-icon text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda fila: Gráficos y Tablas -->
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Productos Más Vendidos
                        </h3>
                        <div class="card-tools">
                            <select id="filtroPeriodo" class="form-control form-control-sm" style="width: auto;">
                                <option value="hoy">Hoy</option>
                                <option value="semana">Esta Semana</option>
                                <option value="mes" selected>Este Mes</option>
                                <option value="anio">Este Año</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40%;">Producto</th>
                                        <th style="width: 15%;">Cantidad</th>
                                        <th style="width: 25%;">Total Vendido</th>
                                        <th style="width: 20%;">%</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpoProductosVendidos">
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <span class="loader"></span> Cargando datos...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">Actualizado: <span id="fechaActualizacionProductos">--:--:--</span></small>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-arrow-circle-down mr-2"></i>
                            Productos con Stock Bajo
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 30%;">Producto</th>
                                        <th style="width: 20%;">Código</th>
                                        <th style="width: 15%;">Stock</th>
                                        <th style="width: 20%;">Mínimo</th>
                                        <th style="width: 15%;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpoStockBajo">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <span class="loader"></span> Cargando datos...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="alert alert-warning mb-0 py-2 d-none" id="alertaStock">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="mensajeAlerta"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Ventas Recientes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-secondary  mr-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="card-title mb-0">Ventas Recientes</h3>
                            <small class="text-muted d-block" id="ventasUpdateStatus">
                                Actualizado: <span id="fechaUltimaActualizacion">--:--:--</span>
                            </small>
                        </div>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="actualizarDashboard()" 
                                title="Actualizar datos"
                                data-toggle="tooltip">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <a href="{{ route('historial_ventas') }}" 
                           class="btn btn-sm btn-primary"
                           title="Ver todas las ventas"
                           data-toggle="tooltip">
                            <i class="fas fa-list mr-1"></i> Ver todas las ventas
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 15%;">Factura</th>
                                <th style="width: 15%;">Fecha/Hora</th>
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 15%;">Productos</th>
                                <th style="width: 15%;">Total</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoVentasRecientes">
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <span class="ml-2">Cargando ventas recientes...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>      

<!-- Modal para Ver Detalle de Venta -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" role="dialog" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalDetalleVentaLabel">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Detalle de Venta
                </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- VISTA PREVIA DE TICKET (oculta inicialmente) -->
                <div id="vistaPreviaTicket" style="display: none;">
                    <div class="preview-title">
                        <h4><i class="fas fa-receipt"></i> Vista Previa - Ticket de Venta</h4>
                        <p class="text-muted">Revise el ticket antes de imprimir</p>
                    </div>
                    
                    <div class="view-navigation">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Volver al Detalle
                        </button>
                        <div>
                            <button type="button" class="btn btn-info" onclick="ajustarTicket('disminuir')">
                                <i class="fas fa-search-minus"></i> Alejar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarTicket('aumentar')">
                                <i class="fas fa-search-plus"></i> Acercar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarTicket('reset')">
                                <i class="fas fa-sync-alt"></i> Tamaño Original
                            </button>
                        </div>
                    </div>
                    
                    <div class="preview-container" style="max-height: none !important; overflow-y: visible !important;">
                        <div class="print-content">
                            <div class="ticket-preview" id="ticketPreview" style="transform: scale(1); transform-origin: top center;">
                                <!-- Contenido del ticket se generará aquí -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="vista-previa-buttons">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="imprimirTicket()">
                            <i class="fas fa-print"></i> Imprimir Ticket
                        </button>
                    </div>
                </div>
                
                <!-- VISTA PREVIA DE FACTURA (oculta inicialmente) -->
                <div id="vistaPreviaFactura" style="display: none;">
                    <div class="preview-title">
                        <h4><i class="fas fa-file-invoice"></i> Vista Previa - Factura de Venta</h4>
                        <p class="text-muted">Revise la factura antes de imprimir</p>
                    </div>
                    
                    <div class="view-navigation">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Volver al Detalle
                        </button>
                        <div>
                            <button type="button" class="btn btn-info" onclick="ajustarFactura('disminuir')">
                                <i class="fas fa-search-minus"></i> Alejar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarFactura('aumentar')">
                                <i class="fas fa-search-plus"></i> Acercar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarFactura('reset')">
                                <i class="fas fa-sync-alt"></i> Tamaño Original
                            </button>
                        </div>
                    </div>
                    
                    <div class="preview-container">
                        <div class="print-content">
                            <div class="factura-preview" id="facturaPreview" style="transform: scale(0.8); transform-origin: top center;">
                                <!-- Contenido de la factura se generará aquí -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="vista-previa-buttons">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="imprimirFactura()">
                            <i class="fas fa-print"></i> Imprimir Factura
                        </button>
                    </div>
                </div>
                
                <!-- CONTENIDO DETALLE (visible inicialmente) -->
                <div id="contenidoDetalle">
                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">INFORMACIÓN DE LA VENTA</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="font-weight-bold" style="width: 40%;">Factura:</td>
                                    <td id="modalFactura">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Fecha:</td>
                                    <td id="modalFecha">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Hora:</td>
                                    <td id="modalHora">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Estado:</td>
                                    <td><span id="modalEstado" class="badge">-</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">INFORMACIÓN DEL CLIENTE</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="font-weight-bold" style="width: 40%;">Cliente:</td>
                                    <td id="modalCliente">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Documento:</td>
                                    <td id="modalDocumento">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Método de Pago:</td>
                                    <td id="modalMetodoPago">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Vendedor:</td>
                                    <td id="modalVendedor">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Detalle de Productos -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-boxes mr-2"></i>Productos Vendidos</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Código</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-right">Precio Unit.</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalDetalleProductos">
                                        <!-- Los productos se llenarán aquí -->
                                    </tbody>
                                   <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="text-right font-weight-bold">Subtotal:</td>
                                            <td class="text-right" id="modalSubtotalProductos">$0.00</td>
                                        </tr>
                                        <tr id="filaDescuento" style="display:none;">
                                            <td colspan="3"></td>
                                            <td class="text-right">Descuento:</td>
                                            <td class="text-right text-success" id="modalDescuento">-$0.00</td>
                                        </tr>
                                        <tr id="filaIVA" style="display:none;">
                                            <td colspan="3"></td>
                                            <td class="text-right">IVA (19%):</td>
                                            <td class="text-right" id="modalIVA">$0.00</td>
                                        </tr>
                                        <tr id="filaOtrosCargos" style="display:none;">
                                            <td colspan="3"></td>
                                            <td class="text-right">IVA:</td>
                                            <td class="text-right" id="modalOtrosCargos">$0.00</td>
                                        </tr>
                                        <tr class="font-weight-bold" style="border-top: 2px solid #dee2e6;">
                                            <td colspan="3"></td>
                                            <td class="text-right" style="font-size: 1.1em;">TOTAL:</td>
                                            <td class="text-right" style="font-size: 1.1em;" id="modalTotalVenta">$0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mt-3" id="modalObservacionesContainer" style="display: none;">
                        <h6 class="text-muted"><i class="fas fa-sticky-note mr-2"></i>Observaciones</h6>
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <p class="mb-0" id="modalObservaciones"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <div class="modal-footer no-print">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
                
                <!-- Botones de impresión SEPARADOS pero uno al lado del otro -->
                <button type="button" class="btn btn-primary me-2" onclick="mostrarVistaPrevia('ticket')">
                    <i class="fas fa-receipt mr-1"></i> Vista Previa Ticket
                </button>
                
                <button type="button" class="btn btn-info" onclick="mostrarVistaPrevia('factura')">
                    <i class="fas fa-file-invoice mr-1"></i> Vista Previa Factura
                </button>
                
                <!-- Botón para cancelar venta (solo para ventas no canceladas) -->
                <button type="button" class="btn btn-danger ms-2 d-none" id="btnCancelarVentaModal" onclick="cancelarVenta(modalVentaId)">
                    <i class="fas fa-ban mr-1"></i> Cancelar Venta
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para imprimir/compartir reporte -->
<div class="modal fade" id="modalReporte" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-file-export"></i> Exportar Reporte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="formatoReporte">Formato de exportación</label>
                    <select class="form-control" id="formatoReporte">
                        <option value="pdf">PDF (Recomendado)</option>
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="rangoReporte">Rango de fechas</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="fechaInicioReporte">
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="fechaFinReporte">
                        </div>
                    </div>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="incluirDetalles" checked>
                    <label class="form-check-label" for="incluirDetalles">
                        Incluir detalles de productos
                    </label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="incluirTotales" checked>
                    <label class="form-check-label" for="incluirTotales">
                        Incluir totales y resúmenes
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarReporte">
                    <i class="fas fa-download"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

@stop


@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

// ============================================
// INICIALIZAR VARIABLES GLOBALES
// ============================================
window.dashboardInterval = window.dashboardInterval || null;
window.ultimaActualizacion = window.ultimaActualizacion || null;
window.periodoActual = window.periodoActual || 'mes';
window.ventaActual = window.ventaActual || null;

var tablaVentas;
var modalVentaId = null;
var datosVenta = null;
var datosCliente = null;
var datosVendedor = null;
var detallesVenta = null;
var escalaTicket = 1;
var escalaFactura = 0.8;

// Variables para almacenar el contenido generado
var contenidoTicketGenerado = '';
var contenidoFacturaGenerado = '';

// ============================================
// FUNCIÓN PARA FORMATEAR NÚMEROS SIN DECIMALES
// ============================================
function formatSinDecimales(numero) {
    var entero = Math.round(numero);
    return entero.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

// ============================================
// INICIALIZACIÓN
// ============================================
$(document).ready(function() {
    console.log('🚀 Iniciando Dashboard POS');
    
    cargarDatosDashboard();
    window.dashboardInterval = setInterval(cargarDatosDashboard, 30000);
    
    $('#filtroPeriodo').on('change', function() {
        window.periodoActual = $(this).val();
        cargarDatosDashboard();
    });
    
    // Inicializar DataTable
    if ($('#tablaVentas').length) {
        initDataTable();
    }
});

// ============================================
// FUNCIÓN PRINCIPAL DE CARGA
// ============================================
function cargarDatosDashboard() {
    const url = '/dashboard/data';
    
    $.ajax({
        url: url,
        method: 'GET',
        data: { periodo: window.periodoActual },
        success: function(response) {
            if (response.success) {
                actualizarEstadisticas(response.data.estadisticas);
                actualizarProductosVendidos(response.data.productos_vendidos);
                actualizarStockBajo(response.data.stock_bajo);
                actualizarVentasRecientes(response.data.ventas_recientes);
                actualizarTimestamps();
                window.ultimaActualizacion = new Date();
            } else {
                mostrarError('No se pudieron cargar los datos');
            }
        },
        error: function() {
            mostrarError('Error de conexión con el servidor');
        }
    });
}

// ============================================
// ACTUALIZAR ESTADÍSTICAS
// ============================================
function actualizarEstadisticas(stats) {
    $('#totalVentasHoy').html(stats.ventas_hoy || 0);
    $('#comparativaVentas').text(`${stats.ventas_hoy || 0} ventas realizadas`);
    
    const ingresos = parseFloat(stats.ingresos_hoy || 0);
    $('#ingresosTotales').html(formatearMoneda(ingresos));
    
    const promedio = parseFloat(stats.promedio_venta || 0);
    $('#promedioVenta').html(formatearMoneda(promedio));
    
    const stockBajo = parseInt(stats.productos_stock_bajo || 0);
    $('#alertasStock').html(stockBajo);
    
    if (stockBajo > 0) {
        $('#productosBajoStock').html(`${stockBajo} producto${stockBajo !== 1 ? 's' : ''} requiere${stockBajo !== 1 ? 'n' : ''} atención`);
        $('#alertasStock').closest('.card-dashboard').addClass('stock-bajo');
    } else {
        $('#productosBajoStock').html('Todos los productos OK');
        $('#alertasStock').closest('.card-dashboard').removeClass('stock-bajo');
    }
}

// ============================================
// ACTUALIZAR PRODUCTOS VENDIDOS
// ============================================
function actualizarProductosVendidos(productos) {
    let html = '';
    
    if (!productos || productos.length === 0) {
        html = `<tr><td colspan="4" class="text-center py-4"><i class="fas fa-info-circle fa-2x text-muted mb-2"></i><div class="text-muted">No hay ventas en este período</div></td></tr>`;
    } else {
        productos.forEach((producto, index) => {
            const porcentaje = parseFloat(producto.porcentaje || 0).toFixed(1);
            const cantidad = parseInt(producto.total_cantidad || 0);
            const total = parseFloat(producto.total_vendido || 0);
            
            html += `<tr>
                <td><div class="d-flex align-items-center"><span class="badge badge-primary mr-2">${index + 1}</span><div><div class="font-weight-bold">${escapeHtml(producto.nombre)}</div><small class="text-muted">${escapeHtml(producto.codigo)}</small></div></div></td>
                <td class="text-center font-weight-bold">${cantidad}</td>
                <td class="text-right">${formatearMoneda(total)}</td>
                <td><div class="progress" style="height: 20px;"><div class="progress-bar bg-success" style="width: ${porcentaje}%">${porcentaje}%</div></div></td>
            </tr>`;
        });
    }
    
    $('#cuerpoProductosVendidos').html(html);
}

// ============================================
// ACTUALIZAR STOCK BAJO
// ============================================
function actualizarStockBajo(stockItems) {
    let html = '';
    
    if (!stockItems || stockItems.length === 0) {
        html = `<tr><td colspan="5" class="text-center py-4"><i class="fas fa-check-circle fa-2x text-success mb-2"></i><div class="text-success font-weight-bold">Todo el stock está en niveles adecuados</div></td></tr>`;
        $('#alertaStock').addClass('d-none');
    } else {
        stockItems.forEach((item) => {
            const stock = parseInt(item.stock_actual || 0);
            const minimo = parseInt(item.stock_minimo || 5);
            const esCritico = stock <= 2;
            const estadoBadge = esCritico ? 'danger' : 'warning';
            
            html += `<tr class="${esCritico ? 'table-danger' : 'table-warning'}">
                <td><div class="font-weight-bold">${escapeHtml(item.nombre)}</div></td>
                <td><small class="text-muted">${escapeHtml(item.codigo)}</small></td>
                <td class="text-center"><span class="badge badge-${estadoBadge} font-weight-bold">${stock}</span></td>
                <td class="text-center">${minimo}</td>
                <td><span class="badge badge-${estadoBadge}"><i class="fas fa-exclamation-triangle mr-1"></i>${item.estado}</span></td>
            </tr>`;
        });
        
        $('#mensajeAlerta').text(`${stockItems.length} producto${stockItems.length !== 1 ? 's necesitan' : ' necesita'} reabastecimiento`);
        $('#alertaStock').removeClass('d-none');
    }
    
    $('#cuerpoStockBajo').html(html);
}

// ============================================
// ACTUALIZAR VENTAS RECIENTES
// ============================================
function actualizarVentasRecientes(ventas) {
    let html = '';
    
    if (!ventas || ventas.length === 0) {
        html = `<tr><td colspan="7" class="text-center py-4"><i class="fas fa-receipt fa-2x text-muted mb-2"></i><div class="text-muted">No hay ventas recientes</div></td></tr>`;
    } else {
        ventas.forEach((venta) => {
            const estadoBadge = obtenerBadgeEstado(venta.estado);
            const fecha = new Date(venta.fecha_venta);
            const fechaFormateada = formatearFecha(fecha);
            
            html += `<tr>
                <td><span class="badge badge-info">${escapeHtml(venta.numero_factura)}</span></td>
                <td><small>${fechaFormateada}</small></td>
                <td>${escapeHtml(venta.cliente)}</td>
                <td class="text-center"><span class="badge badge-secondary">${venta.total_productos}</span></td>
                <td class="text-right font-weight-bold">${formatearMoneda(venta.total)}</td>
                <td><span class="badge badge-${estadoBadge.class}"><i class="fas ${estadoBadge.icon} mr-1"></i>${estadoBadge.text}</span></td>
                <td class="text-center"><a href="javascript:void(0)" class="btn btn-sm btn-outline-primary" onclick="verDetalleVenta(${venta.id})" title="Ver Detalle venta"><i class="fas fa-eye"></i></a></td>
            </tr>`;
        });
    }
    
    $('#cuerpoVentasRecientes').html(html);
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================
function actualizarTimestamps() {
    const ahora = new Date();
    const horaFormateada = ahora.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    $('#fechaUltimaActualizacion').text(horaFormateada);
    $('#fechaActualizacionProductos').text(horaFormateada);
}

function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(valor || 0);
}

function formatearFecha(fecha) {
    return fecha.toLocaleDateString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function obtenerBadgeEstado(estado) {
    const estados = { 
        'completada': { class: 'success', icon: 'fa-check-circle', text: 'Completada' }, 
        'pendiente': { class: 'warning', icon: 'fa-clock', text: 'Pendiente' }, 
        'cancelada': { class: 'danger', icon: 'fa-times-circle', text: 'Cancelada' } 
    };
    return estados[estado] || { class: 'secondary', icon: 'fa-question-circle', text: estado };
}

function mostrarError(mensaje) {
    console.error('❌', mensaje);
}

// ============================================
// INICIALIZAR DATATABLE
// ============================================
function initDataTable() {
    tablaVentas = $('#tablaVentas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('historial.ventas.data') }}",
            type: "GET",
            data: function(d) {
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
                d.estado = $('#estado_venta').val();
                d.metodo_pago = $('#metodo_pago').val();
                d.cliente = $('#buscar_cliente').val();
                d.factura = $('#buscar_factura').val();
                return d;
            }
        },
        columns: [
            { data: "numero_factura", name: "ventas.numero_factura", defaultContent: "N/A" },
            { 
                data: "fecha_venta", 
                name: "ventas.fecha_venta",
                render: function(data, type, row) {
                    if (type === 'display') return row.fecha_formateada + '<br><small class="text-muted">' + row.hora_formateada + '</small>';
                    return data;
                }
            },
            { data: "cliente_nombre", name: "cliente_nombre" },
            { data: "vendedor_nombre", name: "vendedor_nombre" },
            { data: "total_productos", name: "total_productos", className: "text-center" },
            { 
                data: "total", 
                name: "ventas.total", 
                className: "text-right",
                render: function(data) { return '<span data-total="' + data + '">' + data + '</span>'; }
            },
            { data: "estado", name: "ventas.estado" },
            { data: "metodo_pago", name: "ventas.metodo_pago" },
            { data: "acciones", name: "acciones", orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            emptyTable: "No hay productos registrados.",
            info: "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            infoEmpty: "Mostrando 0 a 0 de 0 Entradas",
            infoFiltered: "(Filtrado de _MAX_ total entradas)",
            lengthMenu: "Mostrar _MENU_ Entradas",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "Sin resultados encontrados",
            paginate: { first: "Primero", last: "Ultimo", next: "Siguiente", previous: "Anterior" }
        },
        pageLength: 10,
        drawCallback: function(settings) {
            var api = this.api();
            var total = 0;
            api.rows({page: 'current'}).every(function() {
                var data = this.data();
                var valorTotal = 0;
                if (data.total) {
                    if (typeof data.total === 'string') {
                        var valorLimpio = data.total.replace(/[\$\.]/g, '').replace(',', '.');
                        valorTotal = parseFloat(valorLimpio);
                    } else {
                        valorTotal = parseFloat(data.total);
                    }
                }
                if (!isNaN(valorTotal)) total += valorTotal;
            });
            if ($('#totalGeneral').length) {
                $('#totalGeneral').text('$' + total.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }));
            }
        }
    });
    
    // Eventos de filtros
    $('#fecha_desde, #fecha_hasta, #estado_venta, #metodo_pago').on('change', function() { 
        if (tablaVentas) tablaVentas.ajax.reload(); 
    });
    
    $('#buscar_cliente, #buscar_factura').on('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() { 
            if (tablaVentas) tablaVentas.ajax.reload(); 
        }, 500);
    });
    
    $('#btnFiltrar').on('click', function() { 
        if (tablaVentas) tablaVentas.ajax.reload(); 
    });
    
    $('#btnLimpiarFiltros').on('click', function() {
        $('#fecha_desde, #fecha_hasta, #estado_venta, #metodo_pago, #buscar_cliente, #buscar_factura').val('');
        if (tablaVentas) tablaVentas.ajax.reload();
    });
    
    // Reportes
    $('#descargarReporte').on('click', function() { 
        $('#modalReporte').modal('show'); 
    });
    
    $('#btnGenerarReporte').on('click', function() {
        var params = new URLSearchParams({
            formato: $('#formatoReporte').val(),
            fecha_inicio: $('#fechaInicioReporte').val(),
            fecha_fin: $('#fechaFinReporte').val(),
            detalles: $('#incluirDetalles').is(':checked') ? 1 : 0,
            totales: $('#incluirTotales').is(':checked') ? 1 : 0,
            estado: $('#estado_venta').val(),
            metodo_pago: $('#metodo_pago').val()
        });
        window.open("{{ route('ventas.reporte') }}?" + params.toString(), '_blank');
        $('#modalReporte').modal('hide');
    });
}

// ============================================
// VER DETALLE DE VENTA
// ============================================
function verDetalleVenta(id) {
    modalVentaId = id;
    
    $.ajax({
        url: "{{ url('ventas/detalle') }}/" + id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                datosVenta = response.data.venta;
                datosCliente = response.data.cliente;
                datosVendedor = response.data.vendedor;
                detallesVenta = response.data.detalles;
                
                $('#modalFactura').text(datosVenta.numero_factura || 'N/A');
                $('#modalFecha').text(datosVenta.fecha || 'N/A');
                $('#modalHora').text(datosVenta.hora || 'N/A');
                
                var estado = datosVenta.estado || 'pendiente';
                var estadoTexto = '';
                var badgeClass = '';
                
                switch(estado) {
                    case 'completada': estadoTexto = 'Completada'; badgeClass = 'badge-success'; break;
                    case 'pendiente': estadoTexto = 'Pendiente'; badgeClass = 'badge-warning'; break;
                    case 'cancelada': estadoTexto = 'Cancelada'; badgeClass = 'badge-danger'; break;
                    default: estadoTexto = estado.charAt(0).toUpperCase() + estado.slice(1); badgeClass = 'badge-secondary';
                }
                
                $('#modalEstado').removeClass().addClass('badge ' + badgeClass).text(estadoTexto);
                $('#modalCliente').text(datosCliente ? datosCliente.nombre : 'Cliente General');
                $('#modalDocumento').text(datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A');
                $('#modalMetodoPago').text(datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A');
                $('#modalVendedor').text(datosVendedor ? datosVendedor.nombre : 'N/A');
                
                var htmlProductos = '';
                var subtotalProductos = 0;
                
                if (detallesVenta && detallesVenta.length > 0) {
                    detallesVenta.forEach(function(p) {
                        var cantidad = parseFloat(p.cantidad) || 0;
                        var precioUnitario = parseFloat(p.precio_unitario) || 0;
                        var subtotal = parseFloat(p.subtotal) || 0;
                        subtotalProductos += subtotal;
                        
                        htmlProductos += '<tr>' +
                            '<td>' + (p.nombre || 'Producto sin nombre') + '</td>' +
                            '<td>' + (p.codigo || 'N/A') + '</td>' +
                            '<td class="text-center">' + cantidad.toFixed(0) + '</td>' +
                            '<td class="text-right">$' + precioUnitario.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>' +
                            '<td class="text-right">$' + subtotal.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>' +
                            '</tr>';
                    });
                } else {
                    htmlProductos = '<tr><td colspan="5" class="text-center text-muted">No hay productos registrados</td></tr>';
                }
                
                $('#modalDetalleProductos').html(htmlProductos);
                
                var totalVenta = parseFloat(datosVenta.total) || 0;
                $('#modalSubtotalProductos').text('$' + subtotalProductos.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                
                var diferencia = totalVenta - subtotalProductos;
                $('#filaIVA, #filaDescuento, #filaOtrosCargos').hide();
                
                if (Math.abs(diferencia) > 0.01) {
                    var ivaCalculado = subtotalProductos * 0.19;
                    
                    if (Math.abs(diferencia - ivaCalculado) < 1) {
                        $('#modalIVA').text('$' + diferencia.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#filaIVA').show();
                    } else if (diferencia < 0) {
                        $('#modalDescuento').text('-$' + Math.abs(diferencia).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#filaDescuento').show();
                    } else {
                        $('#modalOtrosCargos').text('$' + diferencia.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#filaOtrosCargos').show();
                    }
                }
                
                $('#modalTotalVenta').text('$' + totalVenta.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                
                var observaciones = datosVenta.observaciones || '';
                if (observaciones && observaciones.trim() !== '') {
                    $('#modalObservaciones').text(observaciones);
                    $('#modalObservacionesContainer').show();
                } else {
                    $('#modalObservacionesContainer').hide();
                }
                
                var btnCancelar = $('#btnCancelarVentaModal');
                if (estado !== 'cancelada' && estado !== 'completada') {
                    btnCancelar.removeClass('d-none');
                } else {
                    btnCancelar.addClass('d-none');
                }
                
                mostrarContenidoDetalle();
                $('#modalDetalleVenta').modal('show');
            } else {
                alert('Error: ' + (response.message || 'No se pudo cargar el detalle'));
            }
        },
        error: function() {
            alert('Error al cargar el detalle de la venta');
        }
    });
}

// ============================================
// FUNCIONES DE VISTA PREVIA - TICKET 80mm
// ============================================

function mostrarVistaPrevia(tipo) {
    prepararDatosVistaPrevia(tipo);
    
    if (tipo === 'ticket') {
        mostrarVistaPreviaTicket();
    } else {
        mostrarVistaPreviaFactura();
    }
}

function mostrarVistaPreviaTicket() {
    prepararDatosVistaPrevia('ticket');
    ocultarTodosContenidos();
    $('#vistaPreviaTicket').show();
    
    // Configurar contenedor para ticket centrado de 80mm
    $('.preview-container').css({
        'display': 'flex',
        'justify-content': 'center',
        'align-items': 'center',
        'min-height': '70vh',
        'max-height': 'none',
        'overflow-y': 'visible',
        'background': '#f5f5f5',
        'padding': '20px',
        'margin': '0'
    });
    
    aplicarEscalaTicket();
}

function mostrarVistaPreviaFactura() {
    prepararDatosVistaPrevia('factura');
    ocultarTodosContenidos();
    $('#vistaPreviaFactura').show();
    aplicarEscalaFactura();
    
    // Configurar contenedor para factura (scroll normal)
    $('.preview-container').css({
        'max-height': '70vh',
        'overflow-y': 'auto',
        'display': 'block',
        'justify-content': 'normal',
        'align-items': 'normal',
        'min-height': 'auto',
        'background': 'transparent',
        'padding': '0',
        'margin': '0'
    });
}

function prepararDatosVistaPrevia(tipo) {
    if (!datosVenta) return;
    
    var totalVenta = parseFloat(datosVenta.total) || 0;
    var subtotalProductos = 0;
    var totalProductosVendidos = 0;
    
    if (detallesVenta && detallesVenta.length > 0) {
        detallesVenta.forEach(function(p) {
            var cantidad = parseFloat(p.cantidad) || 0;
            var subtotal = parseFloat(p.subtotal) || 0;
            subtotalProductos += subtotal;
            totalProductosVendidos += cantidad;
        });
    }
    
    var diferencia = totalVenta - subtotalProductos;
    var tieneIVA = false;
    var tieneDescuento = false;
    var valorIVA = 0;
    var valorDescuento = 0;
    
    if (Math.abs(diferencia) > 0.01) {
        if (diferencia > 0) {
            tieneIVA = true;
            valorIVA = diferencia;
        } else {
            tieneDescuento = true;
            valorDescuento = Math.abs(diferencia);
        }
    }
    
    if (tipo === 'ticket') {
        // ============================================
        // TICKET 80mm - 302px EXACTOS
        // ============================================
        contenidoTicketGenerado = `
            <div style="width: 302px; max-width: 302px; min-width: 302px; margin: 0 auto; background: white; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box;">
                <div style="width: 100%; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.2; padding: 10px; box-sizing: border-box;">
                    
                    <!-- HEADER -->
                    <div style="text-align: center; padding-bottom: 8px; border-bottom: 1px dashed #000; margin-bottom: 8px;">
                        <h4 style="margin: 0; font-size: 14px; font-weight: bold;">SUPERMERCADO XYZ</h4>
                        <p style="margin: 2px 0; font-size: 10px;">NIT: 123456789-0</p>
                        <p style="margin: 2px 0; font-size: 9px;">Dirección: Calle 123 #45-67</p>
                        <p style="margin: 2px 0; font-size: 9px;">Tel: (601) 123-4567</p>
                        <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                        <p style="margin: 2px 0;"><strong>FACTURA:</strong> ${datosVenta.numero_factura || 'N/A'}</p>
                        
                        <!-- FECHA Y HORA EN LA MISMA LÍNEA -->
                        <p style="margin: 2px 0; display: flex; justify-content: center; gap: 10px;">
                            <span><strong>FECHA:</strong> ${datosVenta.fecha || 'N/A'}</span>
                            <span><strong>HORA:</strong> ${datosVenta.hora || 'N/A'}</span>
                        </p>
                    </div>
                    
                    <!-- CLIENTE -->
                    <div style="margin: 6px 0;">
                        <p style="margin: 2px 0;"><strong>CLIENTE:</strong> ${datosCliente ? datosCliente.nombre : 'Cliente General'}</p>
                        <p style="margin: 2px 0;"><strong>DOC:</strong> ${datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A'}</p>
                        <p style="margin: 2px 0;"><strong>VENDEDOR:</strong> ${datosVendedor ? datosVendedor.nombre : 'N/A'}</p>
                    </div>
                    
                    <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                    
                    <!-- ENCABEZADOS DE COLUMNAS -->
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #000; font-weight: bold; font-size: 11px; margin-bottom: 4px;">
                        <div style="width: 35%;">DESCRIPCIÓN</div>
                        <div style="width: 15%; text-align: center;">CANT.</div>
                        <div style="width: 20%; text-align: right;">V.UNIT</div>
                        <div style="width: 30%; text-align: right;">VR.TOTAL</div>
                    </div>
                    
                    <div style="margin: 8px 0;">
        `;
        
        // PRODUCTOS
        if (detallesVenta && detallesVenta.length > 0) {
            detallesVenta.forEach(function(p) {
                var cantidad = parseFloat(p.cantidad) || 0;
                var precioUnitario = parseFloat(p.precio_unitario) || 0;
                var subtotal = parseFloat(p.subtotal) || 0;
                var nombreProducto = p.nombre || 'Producto';
                
                if (nombreProducto.length > 20) {
                    nombreProducto = nombreProducto.substring(0, 17) + '...';
                }
                
                contenidoTicketGenerado += `
                    <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dotted #ccc;">
                        <div style="width: 35%; font-size: 11px;">${nombreProducto}</div>
                        <div style="width: 15%; text-align: center; font-size: 11px;">${cantidad}</div>
                        <div style="width: 20%; text-align: right; font-size: 11px;">$${formatSinDecimales(precioUnitario)}</div>
                        <div style="width: 30%; text-align: right; font-size: 11px; font-weight: bold;">$${formatSinDecimales(subtotal)}</div>
                    </div>
                `;
            });
        } else {
            contenidoTicketGenerado += `<div style="text-align: center; padding: 10px; font-size: 11px;">No hay productos registrados</div>`;
        }
        
        contenidoTicketGenerado += `
                    </div>
                    
                    <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                    
                    <!-- TOTAL PRODUCTOS -->
                    <div style="margin-bottom: 5px;">
                        <p style="margin: 2px 0; font-size: 11px; font-weight: bold;">TOTAL PRODUCTOS: ${totalProductosVendidos} unidades</p>
                    </div>
                    
                    <div style="text-align: right; margin-top: 8px;">
                        <p style="margin: 3px 0; font-size: 11px;">Subtotal: $${formatSinDecimales(subtotalProductos)}</p>
        `;
        
        if (tieneIVA) {
            contenidoTicketGenerado += `<p style="margin: 3px 0; font-size: 11px;">IVA (19%): $${formatSinDecimales(valorIVA)}</p>`;
        }
        
        if (tieneDescuento) {
            contenidoTicketGenerado += `<p style="margin: 3px 0; font-size: 11px;">Descuento: -$${formatSinDecimales(valorDescuento)}</p>`;
        }
        
        contenidoTicketGenerado += `
                        <p style="margin: 5px 0; font-weight: bold; font-size: 13px; border-top: 1px dashed #000; padding-top: 3px;">TOTAL: $${formatSinDecimales(totalVenta)}</p>
                        <p style="margin: 3px 0; font-size: 11px;"><strong>PAGO:</strong> ${datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A'}</p>
                    </div>
                    
                    <!-- FOOTER -->
                    <div style="border-top: 1px dashed #000; padding-top: 8px; margin-top: 8px; text-align: center;">
                        <p style="margin: 2px 0; font-size: 10px; font-weight: bold;">¡GRACIAS POR SU COMPRA!</p>
                        <p style="margin: 2px 0; font-size: 9px;">Conserve este ticket para cambios</p>
                        <p style="margin: 2px 0; font-size: 9px;">${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO', {hour: '2-digit', minute:'2-digit'})}</p>
                    </div>
                    
                </div>
            </div>
        `;
        
        $('#ticketPreview').html(contenidoTicketGenerado);
        
    } else {
        // ============================================
        // FACTURA - Formato A4
        // ============================================
        contenidoFacturaGenerado = `
            <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: white;">
                
                <!-- HEADER FACTURA -->
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;">
                    <h1 style="margin: 0;">FACTURA DE VENTA</h1>
                    <h3 style="margin: 5px 0;">SUPERMERCADO XYZ</h3>
                    <p style="margin: 2px 0;">NIT: 123456789-0</p>
                    <p style="margin: 2px 0;">Dirección: Calle 123 #45-67, Bogotá D.C.</p>
                    <p style="margin: 2px 0;">Tel: (601) 123-4567 | Email: info@superxyz.com</p>
                </div>
                
                <!-- INFORMACIÓN FACTURA Y CLIENTE -->
                <div style="display: flex; margin-bottom: 20px;">
                    <div style="flex: 1; padding-right: 15px;">
                        <h4>INFORMACIÓN DE FACTURA</h4>
                        <table style="width: 100%;">
                            <tr><td><strong>No. Factura:</strong></td><td>${datosVenta.numero_factura || 'N/A'}</td></tr>
                            <tr><td><strong>Fecha:</strong></td><td>${datosVenta.fecha || 'N/A'}</td></tr>
                            <tr><td><strong>Hora:</strong></td><td>${datosVenta.hora || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td>${datosVenta.estado ? datosVenta.estado.charAt(0).toUpperCase() + datosVenta.estado.slice(1) : 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div style="flex: 1; padding-left: 15px;">
                        <h4>INFORMACIÓN DEL CLIENTE</h4>
                        <table style="width: 100%;">
                            <tr><td><strong>Nombre:</strong></td><td>${datosCliente ? datosCliente.nombre : 'Cliente General'}</td></tr>
                            <tr><td><strong>Documento:</strong></td><td>${datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A'}</td></tr>
                            <tr><td><strong>Método de Pago:</strong></td><td>${datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A'}</td></tr>
                            <tr><td><strong>Vendedor:</strong></td><td>${datosVendedor ? datosVendedor.nombre : 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                
               
               <!-- TOTAL DE PRODUCTOS VENDIDOS -->
                <div style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #27292a;">
                    <h5 style="margin: 0; color: #1c1c1d;">
                        <i class="fas fa-boxes" style="margin-right: 8px;"></i>
                        TOTAL DE PRODUCTOS VENDIDOS: <strong>${totalProductosVendidos} unidades</strong>
                    </h5>
                </div>
                
                <!-- TABLA DE PRODUCTOS -->
                <h4>DETALLE DE PRODUCTOS</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: left;">DESCRIPCIÓN</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: left;">CÓDIGO</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: center;">CANTIDAD</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: right;">PRECIO UNITARIO</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: right;">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (detallesVenta && detallesVenta.length > 0) {
            detallesVenta.forEach(function(p) {
                var cantidad = parseFloat(p.cantidad) || 0;
                var precioUnitario = parseFloat(p.precio_unitario) || 0;
                var subtotal = parseFloat(p.subtotal) || 0;
                
                contenidoFacturaGenerado += `
                    <tr>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px;">${p.nombre || 'Producto sin nombre'}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px;">${p.codigo || 'N/A'}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: center;">${cantidad}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: right;">$${precioUnitario.toLocaleString('es-CO')}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: right;">$${subtotal.toLocaleString('es-CO')}</td>
                    </tr>
                `;
            });
        } else {
            contenidoFacturaGenerado += `<tr><td colspan="5" style="padding: 20px; text-align: center;">No hay productos registrados</td></tr>`;
        }
        
        contenidoFacturaGenerado += `
                    </tbody>
                </table>
                
                <!-- TOTALES -->
                <div style="margin-top: 20px; border-top: 2px solid #000; padding-top: 10px;">
                    <div style="display: flex;">
                        <div style="flex: 2;"></div>
                        <div style="flex: 1;">
                            <table style="width: 100%;">
                                <tr><td><strong>Subtotal:</strong></td><td style="text-align: right;">$${subtotalProductos.toLocaleString('es-CO')}</td></tr>
        `;
        
        if (tieneIVA) {
            contenidoFacturaGenerado += `<tr><td>IVA (19%):</td><td style="text-align: right;">$${valorIVA.toLocaleString('es-CO')}</td></tr>`;
        }
        
        if (tieneDescuento) {
            contenidoFacturaGenerado += `<tr><td>Descuento:</td><td style="text-align: right;">-$${valorDescuento.toLocaleString('es-CO')}</td></tr>`;
        }
        
        contenidoFacturaGenerado += `
                                <tr style="border-top: 1px solid #000;">
                                    <td><strong>TOTAL:</strong></td>
                                    <td style="text-align: right;"><strong>$${totalVenta.toLocaleString('es-CO')}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
        `;
        
        if (datosVenta.observaciones) {
            contenidoFacturaGenerado += `
                <div style="margin-top: 30px;">
                    <h5>Observaciones:</h5>
                    <div style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                        ${datosVenta.observaciones}
                    </div>
                </div>
            `;
        }

            
        contenidoFacturaGenerado += `
                <!-- FIRMAS -->
                <div style="margin-top: 50px; display: flex;">
                    <div style="flex: 1; text-align: center;">
                        <hr style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">
                        <p>Firma del Cliente</p>
                    </div>
                    <div style="flex: 1; text-align: center;">
                        <hr style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">
                        <p>Firma del Vendedor</p>
                    </div>
                </div>
                
                <!-- PIE DE PÁGINA -->
                <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                    <p>Documento generado el: ${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO')}</p>
                    <p>Este documento es válido como factura de venta según Resolución DIAN 12345</p>
                </div>
                
            </div>
        `;
        
        $('#facturaPreview').html(contenidoFacturaGenerado);
    }
}

// ============================================
// ESCALAS Y ZOOM
// ============================================

function aplicarEscalaTicket() {
    $('#ticketPreview').css({
        'transform': 'scale(' + escalaTicket + ')',
        'transform-origin': 'center center'
    });
}

function aplicarEscalaFactura() {
    $('#facturaPreview').css('transform', 'scale(' + escalaFactura + ')');
}

function ajustarTicket(accion) {
    if (accion === 'aumentar' && escalaTicket < 1.5) {
        escalaTicket += 0.1;
    } else if (accion === 'disminuir' && escalaTicket > 0.5) {
        escalaTicket -= 0.1;
    } else if (accion === 'reset') {
        escalaTicket = 1;
    }
    aplicarEscalaTicket();
}

function ajustarFactura(accion) {
    if (accion === 'aumentar' && escalaFactura < 1.2) {
        escalaFactura += 0.1;
    } else if (accion === 'disminuir' && escalaFactura > 0.5) {
        escalaFactura -= 0.1;
    } else if (accion === 'reset') {
        escalaFactura = 0.8;
    }
    aplicarEscalaFactura();
}

// ============================================
// IMPRESIÓN
// ============================================

function imprimirTicket() {
    var currentScale = escalaTicket;
    $('#ticketPreview').css('transform', 'scale(1)');
    
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ticket de Venta</title>
            <style>
                @page { size: 80mm auto; margin: 0; }
                body { margin: 0; padding: 0; font-family: 'Courier New', monospace; background: white; width: 80mm; }
                .ticket-print { width: 80mm; margin: 0 auto; padding: 10px; box-sizing: border-box; }
            </style>
        </head>
        <body>
            <div class="ticket-print">${contenidoTicketGenerado}</div>
        </body>
        </html>
    `;
    
    var iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.opacity = '0';
    document.body.appendChild(iframe);
    
    var iframeDoc = iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(printContent);
    iframeDoc.close();
    
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            $('#ticketPreview').css('transform', 'scale(' + currentScale + ')');
            document.body.removeChild(iframe);
        }, 100);
    }, 100);
}

function imprimirFactura() {
    var currentScale = escalaFactura;
    $('#facturaPreview').css('transform', 'scale(0.8)');
    
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Factura de Venta</title>
            <style>
                @page { size: A4; margin: 0; }
                body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: white; }
                .factura-print { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20px; box-sizing: border-box; }
            </style>
        </head>
        <body>
            <div class="factura-print">${contenidoFacturaGenerado}</div>
        </body>
        </html>
    `;
    
    var iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.opacity = '0';
    document.body.appendChild(iframe);
    
    var iframeDoc = iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(printContent);
    iframeDoc.close();
    
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            $('#facturaPreview').css('transform', 'scale(' + currentScale + ')');
            document.body.removeChild(iframe);
        }, 100);
    }, 100);
}

// ============================================
// CONTROL DE VISTAS
// ============================================

function mostrarContenidoDetalle() {
    ocultarTodosContenidos();
    $('#contenidoDetalle').show();
    escalaTicket = 1;
    escalaFactura = 0.8;
    
    $('.preview-container').css({
        'max-height': '70vh',
        'overflow-y': 'auto',
        'display': 'block',
        'justify-content': 'normal',
        'align-items': 'normal',
        'min-height': 'auto',
        'background': 'transparent',
        'padding': '0',
        'margin': '0'
    });
}

function volverAlDetalle() {
    mostrarContenidoDetalle();
}

function ocultarTodosContenidos() {
    $('#vistaPreviaTicket, #vistaPreviaFactura, #contenidoDetalle').hide();
}

// ============================================
// CANCELAR VENTA
// ============================================

function cancelarVenta(id) {
    id = id || modalVentaId;
    if (!id) { 
        alert('ID no válido'); 
        return; 
    }
    
    if (confirm('¿Está seguro de cancelar esta venta?')) {
        $.ajax({
            url: "{{ url('ventas/cancelar') }}/" + id,
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    alert('Venta cancelada exitosamente');
                    $('#modalDetalleVenta').modal('hide');
                    if (tablaVentas) tablaVentas.ajax.reload();
                } else {
                    alert('Error: ' + (response.message || 'No se pudo cancelar'));
                }
            },
            error: function() { 
                alert('Error en la solicitud'); 
            }
        });
    }
}

// ============================================
// DATATABLE
// ============================================

jQuery(document).ready(function($) {
    tablaVentas = $('#tablaVentas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('historial.ventas.data') }}",
            type: "GET",
            data: function(d) {
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
                d.estado = $('#estado_venta').val();
                d.metodo_pago = $('#metodo_pago').val();
                d.cliente = $('#buscar_cliente').val();
                d.factura = $('#buscar_factura').val();
                return d;
            }
        },
        columns: [
            { data: "numero_factura", name: "ventas.numero_factura", defaultContent: "N/A" },
            { 
                data: "fecha_venta", 
                name: "ventas.fecha_venta",
                render: function(data, type, row) {
                    if (type === 'display') return row.fecha_formateada + '<br><small class="text-muted">' + row.hora_formateada + '</small>';
                    return data;
                }
            },
            { data: "cliente_nombre", name: "cliente_nombre" },
            { data: "vendedor_nombre", name: "vendedor_nombre" },
            { data: "total_productos", name: "total_productos", className: "text-center" },
            { 
                data: "total", 
                name: "ventas.total", 
                className: "text-right",
                render: function(data) { return '<span data-total="' + data + '">' + data + '</span>'; }
            },
            { data: "estado", name: "ventas.estado" },
            { data: "metodo_pago", name: "ventas.metodo_pago" },
            { data: "acciones", name: "acciones", orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            emptyTable: "No hay productos registrados.",
            info: "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            infoEmpty: "Mostrando 0 a 0 de 0 Entradas",
            infoFiltered: "(Filtrado de _MAX_ total entradas)",
            lengthMenu: "Mostrar _MENU_ Entradas",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "Sin resultados encontrados",
            paginate: { first: "Primero", last: "Ultimo", next: "Siguiente", previous: "Anterior" }
        },
        pageLength: 10,
        drawCallback: function(settings) {
            var api = this.api();
            var total = 0;
            api.rows({page: 'current'}).every(function() {
                var data = this.data();
                var valorTotal = 0;
                if (data.total) {
                    if (typeof data.total === 'string') {
                        var valorLimpio = data.total.replace(/[\$\.]/g, '').replace(',', '.');
                        valorTotal = parseFloat(valorLimpio);
                    } else {
                        valorTotal = parseFloat(data.total);
                    }
                }
                if (!isNaN(valorTotal)) total += valorTotal;
            });
            if ($('#totalGeneral').length) {
                $('#totalGeneral').text('$' + total.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }));
            }
        }
    });
    
    // FILTROS
    $('#fecha_desde, #fecha_hasta, #estado_venta, #metodo_pago').on('change', function() { tablaVentas.ajax.reload(); });
    $('#buscar_cliente, #buscar_factura').on('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() { tablaVentas.ajax.reload(); }, 500);
    });
    $('#btnFiltrar').on('click', function() { tablaVentas.ajax.reload(); });
    $('#btnLimpiarFiltros').on('click', function() {
        $('#fecha_desde, #fecha_hasta, #estado_venta, #metodo_pago, #buscar_cliente, #buscar_factura').val('');
        tablaVentas.ajax.reload();
    });
    
    // REPORTE
    $('#descargarReporte').on('click', function() { $('#modalReporte').modal('show'); });
    $('#btnGenerarReporte').on('click', function() {
        var params = new URLSearchParams({
            formato: $('#formatoReporte').val(),
            fecha_inicio: $('#fechaInicioReporte').val(),
            fecha_fin: $('#fechaFinReporte').val(),
            detalles: $('#incluirDetalles').is(':checked') ? 1 : 0,
            totales: $('#incluirTotales').is(':checked') ? 1 : 0,
            estado: $('#estado_venta').val(),
            metodo_pago: $('#metodo_pago').val()
        });
        window.open("{{ route('ventas.reporte') }}?" + params.toString(), '_blank');
        $('#modalReporte').modal('hide');
    });
});

    </script>
@stop