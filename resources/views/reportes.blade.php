@extends('layouts.app')
@section('content')

@section('title', 'Reportes')
@section('content_header')
    <h1>Reportes del Sistema</h1>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <style>
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 30px;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 50px;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@stop

@section('content')
<div class="container-fluid">
    <!-- Selector de período -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="form-group">
                <label>Seleccionar Período</label>
                <select id="periodoSelector" class="form-control">
                    <option value="diario">Diario (Mes Actual)</option>
                    <option value="mensual" selected>Mensual (Año Actual)</option>
                    <option value="anual">Anual</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Navegación por Tabs -->
    <ul class="nav nav-tabs" id="reportesTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="ventas-tab" data-toggle="tab" href="#ventas" role="tab">
                <i class="fas fa-chart-line"></i> Reporte de Ventas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="compras-tab" data-toggle="tab" href="#compras" role="tab">
                <i class="fas fa-shopping-cart"></i> Reporte de Compras
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="inventario-tab" data-toggle="tab" href="#inventario" role="tab">
                <i class="fas fa-boxes"></i> Reporte de Inventarios
            </a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Tab Ventas -->
        <div class="tab-pane fade show active" id="ventas" role="tabpanel">
            <div id="ventasContent">
                <div class="loading-spinner" style="display: block;">
                    <i class="fas fa-spinner fa-pulse fa-3x"></i>
                    <p>Cargando datos de ventas...</p>
                </div>
            </div>
        </div>

        <!-- Tab Compras -->
        <div class="tab-pane fade" id="compras" role="tabpanel">
            <div id="comprasContent">
                <div class="loading-spinner" style="display: block;">
                    <i class="fas fa-spinner fa-pulse fa-3x"></i>
                    <p>Cargando datos de compras...</p>
                </div>
            </div>
        </div>

        <!-- Tab Inventario -->
        <div class="tab-pane fade" id="inventario" role="tabpanel">
            <div id="inventarioContent">
                <div class="loading-spinner" style="display: block;">
                    <i class="fas fa-spinner fa-pulse fa-3x"></i>
                    <p>Cargando datos de inventario...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let ventasChart, comprasChart;

// Función para cargar datos de ventas
function loadVentasData(periodo = 'mensual') {
    $('#ventasContent').html('<div class="loading-spinner" style="display: block;"><i class="fas fa-spinner fa-pulse fa-3x"></i><p>Cargando datos...</p></div>');
    
    $.ajax({
        url: '{{ route("reportes.ventas-data") }}',
        type: 'GET',
        data: { periodo: periodo },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateVentasView(response.data, response.productos_top, response.resumen);
            }
        },
        error: function(xhr) {
            $('#ventasContent').html('<div class="alert alert-danger">Error al cargar los datos</div>');
        }
    });
}

function updateVentasView(data, productosTop, resumen) {
    let html = `
        <!-- Tarjetas de resumen -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info stat-card">
                    <div class="inner">
                        <h3>${resumen.total_ventas}</h3>
                        <p>Total Ventas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success stat-card">
                    <div class="inner">
                        <h3>$${formatNumber(resumen.monto_total)}</h3>
                        <p>Monto Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning stat-card">
                    <div class="inner">
                        <h3>${resumen.ventas_hoy}</h3>
                        <p>Ventas Hoy</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger stat-card">
                    <div class="inner">
                        <h3>$${formatNumber(resumen.promedio_venta)}</h3>
                        <p>Promedio por Venta</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evolución de Ventas</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="ventasEvolucionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Productos más vendidos y datos detallados -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 5 Productos Más Vendidos</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${productosTop.map(p => `
                                        <tr>
                                            <td>${p.nombre}</td>
                                            <td>${p.total_vendido}</td>
                                            <td>$${formatNumber(p.monto_total)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos Detallados</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th>N° Ventas</th>
                                        <th>Monto Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(item => `
                                        <tr>
                                            <td>${item.fecha}</td>
                                            <td>${item.total_ventas}</td>
                                            <td>$${formatNumber(item.monto_total)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#ventasContent').html(html);
    
    // Crear gráfico
    const ctx = document.getElementById('ventasEvolucionChart').getContext('2d');
    if(ventasChart) {
        ventasChart.destroy();
    }
    
    ventasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.fecha),
            datasets: [
                {
                    label: 'Monto de Ventas ($)',
                    data: data.map(item => item.monto_total),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y'
                },
                {
                    label: 'Número de Ventas',
                    data: data.map(item => item.total_ventas),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

// Función para cargar datos de compras
function loadComprasData(periodo = 'mensual') {
    $('#comprasContent').html('<div class="loading-spinner" style="display: block;"><i class="fas fa-spinner fa-pulse fa-3x"></i><p>Cargando datos...</p></div>');
    
    $.ajax({
        url: '{{ route("reportes.compras-data") }}',
        type: 'GET',
        data: { periodo: periodo },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateComprasView(response.data, response.proveedores_top, response.resumen);
            }
        },
        error: function(xhr) {
            $('#comprasContent').html('<div class="alert alert-danger">Error al cargar los datos</div>');
        }
    });
}

function updateComprasView(data, proveedoresTop, resumen) {
    let html = `
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info stat-card">
                    <div class="inner">
                        <h3>${resumen.total_compras}</h3>
                        <p>Total Compras</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success stat-card">
                    <div class="inner">
                        <h3>$${formatNumber(resumen.monto_total)}</h3>
                        <p>Monto Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning stat-card">
                    <div class="inner">
                        <h3>${resumen.compras_mes}</h3>
                        <p>Compras del Mes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger stat-card">
                    <div class="inner">
                        <h3>$${formatNumber(resumen.promedio_compra)}</h3>
                        <p>Promedio por Compra</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evolución de Compras</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="comprasEvolucionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 5 Proveedores</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proveedor</th>
                                        <th>Compras</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${proveedoresTop.map(p => `
                                        <tr>
                                            <td>${p.nombre}</td>
                                            <td>${p.total_compras}</td>
                                            <td>$${formatNumber(p.monto_total)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detalle por Período</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th>N° Compras</th>
                                        <th>Monto Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(item => `
                                        <tr>
                                            <td>${item.fecha}</td>
                                            <td>${item.total_compras}</td>
                                            <td>$${formatNumber(item.monto_total)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#comprasContent').html(html);
    
    // Crear gráfico
    const ctx = document.getElementById('comprasEvolucionChart').getContext('2d');
    if(comprasChart) {
        comprasChart.destroy();
    }
    
    comprasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.fecha),
            datasets: [{
                label: 'Monto de Compras ($)',
                data: data.map(item => item.monto_total),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Función para cargar datos de inventario
function loadInventarioData() {
    $('#inventarioContent').html('<div class="loading-spinner" style="display: block;"><i class="fas fa-spinner fa-pulse fa-3x"></i><p>Cargando datos...</p></div>');
    
    $.ajax({
        url: '{{ route("reportes.inventario-data") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateInventarioView(response);
            }
        },
        error: function(xhr) {
            $('#inventarioContent').html('<div class="alert alert-danger">Error al cargar los datos</div>');
        }
    });
}

function updateInventarioView(data) {
    let html = `
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-info stat-card">
                    <div class="inner">
                        <h3>${data.resumen.total_productos}</h3>
                        <p>Total Productos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success stat-card">
                    <div class="inner">
                        <h3>${data.resumen.stock_total}</h3>
                        <p>Stock Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning stat-card">
                    <div class="inner">
                        <h3>$${formatNumber(data.resumen.valor_inventario)}</h3>
                        <p>Valor Inventario</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger stat-card">
                    <div class="inner">
                        <h3>${data.resumen.productos_bajo_stock}</h3>
                        <p>Productos Bajo Stock</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">⚠️ Productos con Bajo Stock</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                        <th>Stock Mínimo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.bajo_stock.map(p => `
                                        <tr>
                                            <td>${p.nombre}</td>
                                            <td class="text-warning">${p.stock}</td>
                                            <td>${p.stock_minimo}</td>
                                            <td><span class="badge badge-warning">Bajo Stock</span></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger">
                        <h3 class="card-title">❌ Productos Sin Stock</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                        <th>Precio Venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.sin_stock.map(p => `
                                        <tr>
                                            <td>${p.nombre}</td>
                                            <td class="text-danger">${p.stock}</td>
                                            <td>$${p.precio_venta}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Productos por Categoría</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Productos</th>
                                        <th>Stock Total</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.por_categoria.map(c => `
                                        <tr>
                                            <td>${c.categoria}</td>
                                            <td>${c.total_productos}</td>
                                            <td>${c.stock_total}</td>
                                            <td>$${formatNumber(c.valor_inventario)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 10 Mayor Valor en Inventario</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                        <th>Valor Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.top_valor.map(p => `
                                        <tr>
                                            <td>${p.nombre}</td>
                                            <td>${p.stock}</td>
                                            <td>$${p.precio_venta}</td>
                                            <td>$${formatNumber(p.valor_total)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#inventarioContent').html(html);
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-CO', { 
        minimumFractionDigits: 0, 
        maximumFractionDigits: 0 
    }).format(num);
}

// Eventos al cargar la página
$(document).ready(function() {
    // Cargar datos iniciales
    loadVentasData('mensual');
    loadComprasData('mensual');
    loadInventarioData();
    
    // Cambiar período
    $('#periodoSelector').change(function() {
        const periodo = $(this).val();
        const activeTab = $('#reportesTab .nav-link.active').attr('href');
        
        if(activeTab === '#ventas') {
            loadVentasData(periodo);
        } else if(activeTab === '#compras') {
            loadComprasData(periodo);
        }
    });
    
    // Recargar datos al cambiar de tab
    $('#reportesTab a').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');
        const periodo = $('#periodoSelector').val();
        
        if(target === '#ventas') {
            loadVentasData(periodo);
        } else if(target === '#compras') {
            loadComprasData(periodo);
        } else if(target === '#inventario') {
            loadInventarioData();
        }
    });
});
</script>
@stop