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
    <script>// ============================================
// DASHBOARD POS - SISTEMA DE ACTUALIZACIÓN
// ============================================

let dashboardInterval = null;
let ultimaActualizacion = null;
let periodoActual = 'mes';

// ============================================
// INICIALIZACIÓN
// ============================================
$(document).ready(function() {
    console.log('🚀 Iniciando Dashboard POS');
    
    // Cargar datos inmediatamente
    cargarDatosDashboard();
    
    // Configurar actualización automática cada 30 segundos
    dashboardInterval = setInterval(cargarDatosDashboard, 30000);
    
    // Event listeners
    $('#filtroPeriodo').on('change', function() {
        periodoActual = $(this).val();
        console.log('📊 Cambiando período a:', periodoActual);
        cargarDatosDashboard();
    });
    
    console.log('✅ Dashboard inicializado');
});

// ============================================
// FUNCIÓN PRINCIPAL DE CARGA
// ============================================
function cargarDatosDashboard() {
    const url = '/dashboard/data';
    
    console.log('🔄 Cargando datos del dashboard...');
    
    $.ajax({
        url: url,
        method: 'GET',
        data: { periodo: periodoActual },
        success: function(response) {
            console.log('✅ Datos recibidos:', response);
            
            if (response.success) {
                actualizarEstadisticas(response.data.estadisticas);
                actualizarProductosVendidos(response.data.productos_vendidos);
                actualizarStockBajo(response.data.stock_bajo);
                actualizarVentasRecientes(response.data.ventas_recientes);
                actualizarTimestamps();
                
                ultimaActualizacion = new Date();
                console.log('✨ Dashboard actualizado correctamente');
            } else {
                console.error('❌ Error en respuesta:', response.message);
                mostrarError('No se pudieron cargar los datos');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX:', {xhr, status, error});
            mostrarError('Error de conexión con el servidor');
        }
    });
}

// ============================================
// ACTUALIZAR ESTADÍSTICAS PRINCIPALES
// ============================================
function actualizarEstadisticas(stats) {
    // Total ventas hoy
    $('#totalVentasHoy').html(stats.ventas_hoy || 0);
    $('#comparativaVentas').text(`${stats.ventas_hoy || 0} ventas realizadas`);
    
    // Ingresos totales
    const ingresos = parseFloat(stats.ingresos_hoy || 0);
    $('#ingresosTotales').html(formatearMoneda(ingresos));
    $('#tendenciaIngresos').text(`Total acumulado del día`);
    
    // Promedio por venta
    const promedio = parseFloat(stats.promedio_venta || 0);
    $('#promedioVenta').html(formatearMoneda(promedio));
    
    // Stock bajo
    const stockBajo = parseInt(stats.productos_stock_bajo || 0);
    $('#alertasStock').html(stockBajo);
    
    if (stockBajo > 0) {
        $('#productosBajoStock').html(`${stockBajo} producto${stockBajo !== 1 ? 's' : ''} requiere${stockBajo !== 1 ? 'n' : ''} atención`);
        $('#alertasStock').closest('.card-dashboard').addClass('stock-bajo');
    } else {
        $('#productosBajoStock').html('Todos los productos OK');
        $('#alertasStock').closest('.card-dashboard').removeClass('stock-bajo');
    }
    
    console.log('📊 Estadísticas actualizadas');
}

// ============================================
// ACTUALIZAR PRODUCTOS MÁS VENDIDOS
// ============================================
function actualizarProductosVendidos(productos) {
    let html = '';
    
    if (!productos || productos.length === 0) {
        html = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                    <div class="text-muted">No hay ventas en este período</div>
                </td>
            </tr>
        `;
    } else {
        productos.forEach((producto, index) => {
            const porcentaje = parseFloat(producto.porcentaje || 0).toFixed(1);
            const cantidad = parseInt(producto.total_cantidad || 0);
            const total = parseFloat(producto.total_vendido || 0);
            
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-primary mr-2">${index + 1}</span>
                            <div>
                                <div class="font-weight-bold">${escapeHtml(producto.nombre)}</div>
                                <small class="text-muted">${escapeHtml(producto.codigo)}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center font-weight-bold">${cantidad}</td>
                    <td class="text-right">${formatearMoneda(total)}</td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: ${porcentaje}%" 
                                 aria-valuenow="${porcentaje}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${porcentaje}%
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#cuerpoProductosVendidos').html(html);
    console.log('📦 Productos vendidos actualizados');
}

// ============================================
// ACTUALIZAR STOCK BAJO
// ============================================
function actualizarStockBajo(stockItems) {
    let html = '';
    
    if (!stockItems || stockItems.length === 0) {
        html = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <div class="text-success font-weight-bold">Todo el stock está en niveles adecuados</div>
                </td>
            </tr>
        `;
        $('#alertaStock').addClass('d-none');
    } else {
        stockItems.forEach((item) => {
            const stock = parseInt(item.stock_actual || 0);
            const minimo = parseInt(item.stock_minimo || 5);
            const esCritico = stock <= 2;
            const estadoBadge = esCritico ? 'danger' : 'warning';
            
            html += `
                <tr class="${esCritico ? 'table-danger' : 'table-warning'}">
                    <td>
                        <div class="font-weight-bold">${escapeHtml(item.nombre)}</div>
                    </td>
                    <td>
                        <small class="text-muted">${escapeHtml(item.codigo)}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-${estadoBadge} font-weight-bold">${stock}</span>
                    </td>
                    <td class="text-center">${minimo}</td>
                    <td>
                        <span class="badge badge-${estadoBadge}">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            ${item.estado}
                        </span>
                    </td>
                </tr>
            `;
        });
        
        // Mostrar alerta
        $('#mensajeAlerta').text(`${stockItems.length} producto${stockItems.length !== 1 ? 's necesitan' : ' necesita'} reabastecimiento`);
        $('#alertaStock').removeClass('d-none');
    }
    
    $('#cuerpoStockBajo').html(html);
    console.log('📉 Stock bajo actualizado');
}

// ============================================
// ACTUALIZAR VENTAS RECIENTES
// ============================================
function actualizarVentasRecientes(ventas) {
    let html = '';
    
    if (!ventas || ventas.length === 0) {
        html = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                    <div class="text-muted">No hay ventas recientes</div>
                </td>
            </tr>
        `;
    } else {
        ventas.forEach((venta) => {
            const estadoBadge = obtenerBadgeEstado(venta.estado);
            const fecha = new Date(venta.fecha_venta);
            const fechaFormateada = formatearFecha(fecha);
            
            html += `
                <tr>
                    <td>
                        <span class="badge badge-info">${escapeHtml(venta.numero_factura)}</span>
                    </td>
                    <td>
                        <small>${fechaFormateada}</small>
                    </td>
                    <td>${escapeHtml(venta.cliente)}</td>
                    <td class="text-center">
                        <span class="badge badge-secondary">${venta.total_productos}</span>
                    </td>
                    <td class="text-right font-weight-bold">${formatearMoneda(venta.total)}</td>
                    <td>
                        <span class="badge badge-${estadoBadge.class}">
                            <i class="fas ${estadoBadge.icon} mr-1"></i>
                            ${estadoBadge.text}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="/ventas/${venta.id}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#cuerpoVentasRecientes').html(html);
    console.log('🧾 Ventas recientes actualizadas');
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================

function actualizarTimestamps() {
    const ahora = new Date();
    const horaFormateada = ahora.toLocaleTimeString('es-CO', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    });
    
    $('#fechaUltimaActualizacion').text(horaFormateada);
    $('#fechaActualizacionProductos').text(horaFormateada);
}

function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(valor || 0);
}

function formatearFecha(fecha) {
    return fecha.toLocaleDateString('es-CO', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
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
    // Podrías agregar un toast o notificación aquí
}

// ============================================
// FUNCIONES PÚBLICAS
// ============================================

// Función para actualizar manualmente
window.actualizarDashboard = function() {
    console.log('🔄 Actualización manual solicitada');
    cargarDatosDashboard();
};

// Función para debug
window.debugDashboard = function() {
    console.log('🔍 Estado del Dashboard:', {
        periodoActual,
        ultimaActualizacion,
        intervaloActivo: !!dashboardInterval
    });
};

// Limpiar intervalo al salir
$(window).on('beforeunload', function() {
    if (dashboardInterval) {
        clearInterval(dashboardInterval);
        console.log('🛑 Intervalo de actualización detenido');
    }
});
    </script>
@stop