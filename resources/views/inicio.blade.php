@extends('adminlte::page')

@section('title', 'Dashboard POS')

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard - Sistema POS</h1>
@stop

@section('content')
    <style>
        /* Mantener diseño original de los cards */
        .card-dashboard {
            border-left: 4px solid !important;
            transition: transform 0.3s;
            height: 120px;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
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
        }

        .card-subtitle {
            font-size: 0.9rem !important;
            color: #6c757d !important;
            margin-bottom: 0.25rem !important;
        }

        /* Ajustes para AdminLTE */
        .content-wrapper {
            background-color: #f4f6f9 !important;
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

        /* Ajustar tablas para AdminLTE */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075) !important;
        }

        /* Asegurar que los cards tengan fondo blanco */
        .card {
            background-color: #ffffff !important;
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
                            <small class="text-muted" id="comparativaVentas"></small>
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
                            <h6 class="card-subtitle">Ingresos Totales</h6>
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
                            <small class="text-muted">Valor promedio</small>
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
                            <small class="text-muted" id="productosBajoStock"></small>
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
                                        <th style="width: 20%;">Categoría</th>
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

        <!-- Tercera fila: Ventas Recientes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-2"></i>
                            Ventas Recientes
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="actualizarDashboard()" title="Actualizar">
                                <i class="fas fa-sync-alt"></i>
                            </button>
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
                                            <span class="loader"></span> Cargando ventas recientes...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Última actualización: <span id="fechaUltimaActualizacion">--:--:--</span></small>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('venta') }}" class="btn btn-sm btn-primary">
                                    Ver todas las ventas <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>// dashboard-fixed.js
$(document).ready(function() {
    console.log('Dashboard iniciado');
    
    // Primero cargar datos locales inmediatamente
    loadLocalData();
    
    // Luego intentar cargar datos del servidor
    setTimeout(loadServerData, 1000);
    
    // Configurar actualización periódica
    setInterval(loadServerData, 30000); // Cada 30 segundos
});

function loadLocalData() {
    console.log('Cargando datos locales...');
    
    // Datos locales de respaldo
    const localData = {
        estadisticas: {
            total_ventas_hoy: 0,
            ingresos_totales: 0,
            promedio_venta: 0,
            alertas_stock: 0
        },
        productos_vendidos: [
            {nombre: 'Esperando datos...', codigo: '', cantidad_vendida: 0, total_vendido: 0}
        ],
        stock_bajo: [
            {nombre: 'Esperando datos...', categoria: 'General', stock_actual: 0, stock_minimo: 5}
        ],
        ventas_recientes: [
            {numero_factura: 'Cargando...', fecha_venta: new Date(), total: 0, nombre_cliente: '...', estado: '...'}
        ]
    };
    
    updateUI(localData);
}

function loadServerData() {
    console.log('Intentando cargar datos del servidor...');
    
    // Mostrar indicador de carga
    showLoadingIndicator();
    
    $.ajax({
        url: '/api/dashboard/data', // Ruta CORREGIDA
        type: 'GET',
        dataType: 'json',
        timeout: 5000, // 5 segundos máximo
        success: function(response) {
            console.log('Datos recibidos del servidor:', response);
            
            if (response.success && response.data) {
                updateUI(response.data);
                hideLoadingIndicator();
                showSuccessMessage('Datos actualizados');
            } else {
                console.warn('Respuesta no válida:', response);
                showWarningMessage('Formato de datos incorrecto');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar datos:', status, error);
            
            if (status === 'timeout') {
                showErrorMessage('El servidor tardó en responder');
            } else if (xhr.status === 404) {
                showErrorMessage('Ruta no encontrada');
                // Intentar con ruta alternativa
                tryAlternativeRoute();
            } else if (xhr.status === 500) {
                showErrorMessage('Error del servidor');
            } else {
                showErrorMessage('Error de conexión: ' + error);
            }
            
            hideLoadingIndicator();
        }
    });
}

function tryAlternativeRoute() {
    console.log('Intentando ruta alternativa...');
    
    // Intentar diferentes rutas posibles
    const routes = [
        '/dashboard/data',
        '/admin/dashboard/data',
        '/data/dashboard'
    ];
    
    let tried = 0;
    
    routes.forEach(route => {
        $.ajax({
            url: route,
            type: 'GET',
            timeout: 3000,
            success: function(response) {
                if (response.success && response.data) {
                    console.log('Éxito con ruta:', route);
                    updateUI(response.data);
                    showSuccessMessage('Conectado usando ruta alternativa');
                }
            }
        });
    });
}

function updateUI(data) {
    console.log('Actualizando interfaz con:', data);
    
    // 1. Actualizar cards
    updateCards(data.estadisticas);
    
    // 2. Actualizar tablas
    updateTable('#cuerpoProductosVendidos', data.productos_vendidos, 'producto');
    updateTable('#cuerpoStockBajo', data.stock_bajo, 'stock');
    updateTable('#cuerpoVentasRecientes', data.ventas_recientes, 'venta');
}

function updateCards(stats) {
    // Total ventas hoy
    $('#totalVentasHoy').html(stats?.total_ventas_hoy !== undefined ? 
        '<span class="number">' + stats.total_ventas_hoy + '</span>' : 
        '<span class="text-muted">0</span>');
    
    // Ingresos totales
    $('#ingresosTotales').html(stats?.ingresos_totales !== undefined ? 
        '<span class="number">$' + parseFloat(stats.ingresos_totales).toFixed(2) + '</span>' : 
        '<span class="text-muted">$0.00</span>');
    
    // Promedio por venta
    $('#promedioVenta').html(stats?.promedio_venta !== undefined ? 
        '<span class="number">$' + parseFloat(stats.promedio_venta).toFixed(2) + '</span>' : 
        '<span class="text-muted">$0.00</span>');
    
    // Alertas stock
    $('#alertasStock').html(stats?.alertas_stock !== undefined ? 
        '<span class="number">' + stats.alertas_stock + '</span>' : 
        '<span class="text-muted">0</span>');
    
    // Actualizar texto descriptivo
    if (stats?.alertas_stock > 0) {
        $('#productosBajoStock').html('<span class="text-danger">' + stats.alertas_stock + ' productos críticos</span>');
    } else {
        $('#productosBajoStock').html('<span class="text-success">Stock óptimo</span>');
    }
}

function updateTable(selector, data, type) {
    let html = '';
    
    if (!data || data.length === 0) {
        // Mensaje cuando no hay datos
        const messages = {
            'producto': 'No hay productos vendidos',
            'stock': 'Todo el stock está en niveles adecuados',
            'venta': 'No hay ventas recientes'
        };
        
        const icons = {
            'producto': 'fa-chart-line',
            'stock': 'fa-check-circle',
            'venta': 'fa-receipt'
        };
        
        const colors = {
            'producto': 'muted',
            'stock': 'success',
            'venta': 'muted'
        };
        
        html = `
        <tr>
            <td colspan="6" class="text-center py-4 text-${colors[type]}">
                <i class="fas ${icons[type]} fa-2x mb-3"></i>
                <div class="font-weight-bold">${messages[type]}</div>
            </td>
        </tr>
        `;
    } else {
        // Generar filas con datos
        data.forEach((item, index) => {
            if (type === 'producto') {
                html += `
                <tr>
                    <td>
                        <div class="font-weight-bold">${safeText(item.nombre)}</div>
                        <small class="text-muted">${safeText(item.codigo)}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary">${item.cantidad_vendida || 0}</span>
                    </td>
                    <td class="text-right">
                        $${(item.total_vendido || 0).toFixed(2)}
                    </td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: ${Math.min((index + 1) * 20, 100)}%"></div>
                        </div>
                    </td>
                </tr>
                `;
            } else if (type === 'stock') {
                const esBajo = (item.stock_actual || 0) <= (item.stock_minimo || 5);
                const esCritico = (item.stock_actual || 0) <= 2;
                
                html += `
                <tr>
                    <td>
                        <div class="font-weight-bold">${safeText(item.nombre)}</div>
                        <small class="text-muted">${safeText(item.codigo)}</small>
                    </td>
                    <td>
                        <span class="badge badge-light">${safeText(item.categoria)}</span>
                    </td>
                    <td class="text-center ${esCritico ? 'text-danger font-weight-bold' : esBajo ? 'text-warning' : ''}">
                        ${item.stock_actual || 0}
                    </td>
                    <td class="text-center">${item.stock_minimo || 5}</td>
                    <td>
                        <span class="badge badge-${esCritico ? 'danger' : esBajo ? 'warning' : 'success'}">
                            ${esCritico ? 'CRÍTICO' : esBajo ? 'BAJO' : 'OK'}
                        </span>
                    </td>
                </tr>
                `;
            } else if (type === 'venta') {
                const fecha = item.fecha_venta ? 
                    formatDateTime(item.fecha_venta) : 
                    '--:--';
                
                html += `
                <tr>
                    <td>
                        <div class="font-weight-bold">${safeText(item.numero_factura)}</div>
                        <small class="text-muted">${fecha}</small>
                    </td>
                    <td>${safeText(item.nombre_cliente)}</td>
                    <td class="text-center">
                        <span class="badge badge-info">${item.cantidad_productos || 1}</span>
                    </td>
                    <td class="text-right font-weight-bold">
                        $${(item.total || 0).toFixed(2)}
                    </td>
                    <td>
                        <span class="badge badge-success">${safeText(item.estado)}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewSale(${item.id || 0})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                `;
            }
        });
    }
    
    $(selector).html(html);
}

// Utilidades
function safeText(text) {
    if (text === undefined || text === null) return '';
    return text.toString();
}

function formatDateTime(dateString) {
    try {
        const date = new Date(dateString);
        return date.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return '--:--';
    }
}

function showLoadingIndicator() {
    // Agregar spinner sutil a los títulos
    $('.card-title').each(function() {
        if (!$(this).find('.fa-spinner').length) {
            $(this).prepend('<i class="fas fa-spinner fa-spin mr-2" style="font-size: 0.8em;"></i>');
        }
    });
}

function hideLoadingIndicator() {
    $('.fa-spinner').remove();
}

function showSuccessMessage(text) {
    // Mostrar mensaje sutil
    const message = $(`
        <div class="alert alert-success alert-dismissible fade show position-fixed" 
             style="bottom: 20px; right: 20px; z-index: 9999; max-width: 300px;">
            <i class="fas fa-check-circle mr-2"></i>
            ${text}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(message);
    
    setTimeout(() => {
        message.alert('close');
    }, 3000);
}

function showWarningMessage(text) {
    const message = $(`
        <div class="alert alert-warning alert-dismissible fade show position-fixed" 
             style="bottom: 20px; right: 20px; z-index: 9999; max-width: 300px;">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            ${text}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(message);
    
    setTimeout(() => {
        message.alert('close');
    }, 4000);
}

function showErrorMessage(text) {
    const message = $(`
        <div class="alert alert-danger alert-dismissible fade show position-fixed" 
             style="bottom: 20px; right: 20px; z-index: 9999; max-width: 300px;">
            <i class="fas fa-times-circle mr-2"></i>
            ${text}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(message);
    
    setTimeout(() => {
        message.alert('close');
    }, 5000);
}

// Función para ver venta (placeholder)
function viewSale(id) {
    alert('Ver venta #' + id + ' (función por implementar)');
}

// Forzar recarga desde consola
window.refreshDashboard = loadServerData;
    </script>
@stop