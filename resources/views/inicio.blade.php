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
    <script>
    $(document).ready(function() {
        // Cargar datos iniciales
        actualizarDashboard();
        
        // Actualizar automáticamente cada 30 segundos
        setInterval(actualizarDashboard, 30000);
        
        // Evento para cambiar el periodo del filtro
        $('#filtroPeriodo').change(function() {
            cargarProductosVendidos($(this).val());
        });
        
        // Actualizar hora actual
        actualizarHora();
        setInterval(actualizarHora, 1000);
    });

    function actualizarHora() {
        const ahora = new Date();
        const hora = ahora.getHours().toString().padStart(2, '0');
        const minutos = ahora.getMinutes().toString().padStart(2, '0');
        const segundos = ahora.getSeconds().toString().padStart(2, '0');
        const horaStr = `${hora}:${minutos}:${segundos}`;
        
        $('#fechaUltimaActualizacion').text(horaStr);
    }

    function actualizarDashboard() {
        const horaActualizacion = new Date().toLocaleTimeString('es-ES');
        $('#fechaActualizacionProductos').text(horaActualizacion);
        
        cargarEstadisticas();
        cargarProductosVendidos($('#filtroPeriodo').val());
        cargarStockBajo();
        cargarVentasRecientes();
        
        // Mostrar notificación de actualización
        toastr.success('Datos actualizados correctamente', 'Actualización', {
            timeOut: 2000,
            progressBar: true
        });
    }

    function cargarEstadisticas() {
        $.ajax({
            url: '{{ route("dashboard.estadisticas") }}',
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Mostrar loaders
                $('#totalVentasHoy, #ingresosTotales, #promedioVenta, #alertasStock')
                    .html('<span class="loader"></span>');
            },
            success: function(response) {
                if(response.success) {
                    // Actualizar total ventas hoy
                    $('#totalVentasHoy').html(response.total_ventas_hoy);
                    $('#comparativaVentas').html(
                        response.comparativa_ventas > 0 ? 
                        `<span class="text-success"><i class="fas fa-arrow-up"></i> ${response.comparativa_ventas}% vs ayer</span>` :
                        `<span class="text-danger"><i class="fas fa-arrow-down"></i> ${Math.abs(response.comparativa_ventas)}% vs ayer</span>`
                    );
                    
                    // Actualizar ingresos totales
                    $('#ingresosTotales').html('$' + response.ingresos_totales.toLocaleString('es-ES', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#tendenciaIngresos').html(
                        response.tendencia_ingresos.includes('↑') ? 
                        `<span class="text-success">${response.tendencia_ingresos}</span>` :
                        `<span class="text-danger">${response.tendencia_ingresos}</span>`
                    );
                    
                    // Actualizar promedio por venta
                    $('#promedioVenta').html('$' + response.promedio_venta.toLocaleString('es-ES', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    
                    // Actualizar alertas de stock
                    $('#alertasStock').html(response.alertas_stock);
                    $('#productosBajoStock').html(response.alertas_stock > 0 ? 
                        `<span class="text-danger">${response.alertas_stock} productos críticos</span>` :
                        '<span class="text-success">Stock óptimo</span>'
                    );
                    
                    // Aplicar animación si hay alertas
                    const cardAlerta = $('#alertasStock').closest('.card-dashboard');
                    if(response.alertas_stock > 0) {
                        cardAlerta.addClass('stock-bajo');
                    } else {
                        cardAlerta.removeClass('stock-bajo');
                    }
                }
            },
            error: function() {
                $('#totalVentasHoy').html('<span class="text-danger">Error</span>');
                $('#ingresosTotales').html('<span class="text-danger">Error</span>');
                $('#promedioVenta').html('<span class="text-danger">Error</span>');
                $('#alertasStock').html('<span class="text-danger">Error</span>');
            }
        });
    }

    function cargarProductosVendidos(periodo) {
        $.ajax({
            url: '{{ route("dashboard.productos-vendidos") }}',
            type: 'GET',
            data: { periodo: periodo },
            dataType: 'json',
            success: function(response) {
                if(response.success && response.productos.length > 0) {
                    let html = '';
                    let totalVentas = response.productos.reduce((sum, p) => sum + p.cantidad_vendida, 0);
                    
                    response.productos.forEach(function(producto, index) {
                        let porcentaje = totalVentas > 0 ? ((producto.cantidad_vendida / totalVentas) * 100).toFixed(1) : 0;
                        
                        html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-2" style="width: 24px; height: 24px; background-color: ${getColor(index)}; border-radius: 4px;"></div>
                                    <div>
                                        <strong>${producto.nombre}</strong>
                                        <br>
                                        <small class="text-muted">${producto.codigo || 'N/A'}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary" style="font-size: 0.9rem;">
                                    ${producto.cantidad_vendida}
                                </span>
                            </td>
                            <td>
                                <strong>$${parseFloat(producto.total_vendido).toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}</strong>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: ${porcentaje}%; background-color: ${getColor(index)};"
                                         aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
                                        ${porcentaje}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                    
                    $('#cuerpoProductosVendidos').html(html);
                } else {
                    $('#cuerpoProductosVendidos').html(`
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle mr-2"></i>
                                No hay datos de ventas para el periodo seleccionado
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#cuerpoProductosVendidos').html(`
                    <tr>
                        <td colspan="4" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Error al cargar los datos
                        </td>
                    </tr>
                `);
            }
        });
    }

    function cargarStockBajo() {
        $.ajax({
            url: '{{ route("dashboard.stock-bajo") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    let html = '';
                    
                    if(response.productos.length > 0) {
                        response.productos.forEach(function(producto) {
                            let estado = '';
                            let claseEstado = '';
                            let iconoEstado = '';
                            
                            if(producto.stock_actual <= producto.stock_minimo * 0.5) {
                                estado = 'CRÍTICO';
                                claseEstado = 'danger';
                                iconoEstado = 'fa-exclamation-circle';
                            } else if(producto.stock_actual <= producto.stock_minimo) {
                                estado = 'BAJO';
                                claseEstado = 'warning';
                                iconoEstado = 'fa-exclamation-triangle';
                            } else if(producto.stock_actual <= producto.stock_minimo * 1.5) {
                                estado = 'MEDIO';
                                claseEstado = 'info';
                                iconoEstado = 'fa-info-circle';
                            }
                            
                            let porcentaje = (producto.stock_actual / producto.stock_minimo) * 100;
                            let colorBarra = '';
                            
                            if(porcentaje <= 50) colorBarra = 'danger';
                            else if(porcentaje <= 100) colorBarra = 'warning';
                            else colorBarra = 'success';
                            
                            html += `
                            <tr>
                                <td>
                                    <strong>${producto.nombre}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">${producto.categoria || 'Sin categoría'}</span>
                                </td>
                                <td class="text-center">
                                    <span class="${producto.stock_actual <= producto.stock_minimo ? 'text-danger font-weight-bold' : 'text-warning'}">
                                        ${producto.stock_actual}
                                    </span>
                                </td>
                                <td class="text-center">${producto.stock_minimo}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-${claseEstado} badge-stock mr-2">
                                            <i class="fas ${iconoEstado} mr-1"></i>${estado}
                                        </span>
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-${colorBarra}" 
                                                 style="width: ${Math.min(porcentaje, 100)}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            `;
                        });
                        
                        // Mostrar alerta si hay productos críticos
                        let productosCriticos = response.productos.filter(p => p.stock_actual <= p.stock_minimo * 0.5).length;
                        if(productosCriticos > 0) {
                            $('#mensajeAlerta').html(`<strong>¡Atención!</strong> Hay ${productosCriticos} producto(s) con stock crítico. Se recomienda realizar pedido urgente.`);
                            $('#alertaStock').removeClass('d-none').removeClass('alert-warning').addClass('alert-danger');
                        } else {
                            $('#alertaStock').addClass('d-none');
                        }
                    } else {
                        html = `
                        <tr>
                            <td colspan="5" class="text-center py-4 text-success">
                                <i class="fas fa-check-circle mr-2"></i>
                                Todo el stock está en niveles adecuados
                            </td>
                        </tr>
                        `;
                        $('#alertaStock').addClass('d-none');
                    }
                    
                    $('#cuerpoStockBajo').html(html);
                }
            },
            error: function() {
                $('#cuerpoStockBajo').html(`
                    <tr>
                        <td colspan="5" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Error al cargar los datos de stock
                        </td>
                    </tr>
                `);
            }
        });
    }

    function cargarVentasRecientes() {
        $.ajax({
            url: '{{ route("dashboard.ventas-recientes") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success && response.ventas.length > 0) {
                    let html = '';
                    
                    response.ventas.forEach(function(venta) {
                        let fecha = new Date(venta.fecha_venta);
                        let fechaFormateada = fecha.toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        let claseEstado = '';
                        let iconoEstado = '';
                        
                        switch(venta.estado) {
                            case 'completada':
                                claseEstado = 'success';
                                iconoEstado = 'fa-check-circle';
                                break;
                            case 'pendiente':
                                claseEstado = 'warning';
                                iconoEstado = 'fa-clock';
                                break;
                            case 'cancelada':
                                claseEstado = 'danger';
                                iconoEstado = 'fa-times-circle';
                                break;
                            default:
                                claseEstado = 'secondary';
                                iconoEstado = 'fa-question-circle';
                        }
                        
                        html += `
                        <tr>
                            <td>
                                <strong>${venta.numero_factura}</strong>
                            </td>
                            <td>${fechaFormateada}</td>
                            <td>${venta.nombre_cliente || 'Cliente no registrado'}</td>
                            <td class="text-center">
                                <span class="badge badge-info">
                                    ${venta.cantidad_productos || 1}
                                </span>
                            </td>
                            <td>
                                <strong>$${parseFloat(venta.total).toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })}</strong>
                            </td>
                            <td>
                                <span class="badge badge-${claseEstado}">
                                    <i class="fas ${iconoEstado} mr-1"></i>
                                    ${venta.estado}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', '') }}/${venta.id}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        `;
                    });
                    
                    $('#cuerpoVentasRecientes').html(html);
                } else {
                    $('#cuerpoVentasRecientes').html(`
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle mr-2"></i>
                                No hay ventas recientes para mostrar
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#cuerpoVentasRecientes').html(`
                    <tr>
                        <td colspan="7" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Error al cargar las ventas recientes
                        </td>
                    </tr>
                `);
            }
        });
    }

    function getColor(index) {
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
            '#6A4C93', '#FF595E', '#1982C4', '#8AC926'
        ];
        return colors[index % colors.length];
    }
    
    // Inicializar toastr para notificaciones
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    </script>
@stop