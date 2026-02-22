@extends('layouts.app')

@section('title', 'Gestión de Compras')

@section('content')
<style>
    /* Variables CSS para tema claro/oscuro */
    :root {
        --dashboard-bg: #f8f9fa;
        --dashboard-card-bg: #ffffff;
        --dashboard-text-color: #212529;
        --dashboard-muted-text: #6c757d;
    }

    [data-bs-theme="dark"] {
        --dashboard-bg: #1a1d20;
        --dashboard-card-bg: #2d3236;
        --dashboard-text-color: #e9ecef;
        --dashboard-muted-text: #adb5bd;
    }

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

    .card-dashboard.primary  { border-left-color: #0d6efd !important; }
    .card-dashboard.success  { border-left-color: #198754 !important; }
    .card-dashboard.warning  { border-left-color: #ffc107 !important; }
    .card-dashboard.danger   { border-left-color: #dc3545 !important; }
    .card-dashboard.info     { border-left-color: #0dcaf0 !important; }

    .card-dashboard .card-icon { font-size: 2.5rem !important; opacity: 0.8; }

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

    .content-wrapper { background-color: var(--dashboard-bg) !important; }
    .dark-mode .content-wrapper { background-color: var(--dashboard-bg) !important; }
    .main-header { margin-bottom: 0 !important; }
    .small-box { margin-bottom: 0 !important; }

    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
    @keyframes spin   { to { transform: rotate(360deg); } }

    .loader {
        display: inline-block;
        width: 20px; height: 20px;
        border: 3px solid rgba(0,0,0,.1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }

    [data-bs-theme="dark"] .table-danger,
    [data-bs-theme="dark"] .table-warning {
        --bs-table-color: #000000;
        color: var(--bs-table-color) !important;
    }

    /* ── BÚSQUEDA ─────────────────────────────────────────────── */
    .busqueda-wrapper { position: relative; }

    #listaBusqueda {
        position: absolute;
        top: calc(100% + 2px);
        left: 0; right: 0;
        z-index: 1055;                 /* por encima del modal */
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background: #fff;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        display: none;                 /* oculto por defecto */
    }

    [data-bs-theme="dark"] #listaBusqueda {
        background: #2d3236;
        border-color: #495057;
    }

    #listaBusqueda .list-group-item {
        cursor: pointer;
        border-left: none;
        border-right: none;
        transition: background .15s;
    }

    #listaBusqueda .list-group-item:first-child { border-top: none; }
    #listaBusqueda .list-group-item:last-child  { border-bottom: none; }

    #listaBusqueda .list-group-item:hover,
    #listaBusqueda .list-group-item:focus {
        background-color: #e9f3ff;
        outline: none;
    }

    [data-bs-theme="dark"] #listaBusqueda .list-group-item:hover {
        background-color: #3a3f44;
    }

    /* ── SECCIÓN PRODUCTO ─────────────────────────────────────── */
    #seccionProducto {
        background: #f0f7ff;
        border: 1px solid #b8d4f5;
        border-radius: 8px;
        padding: 18px;
        margin-top: 16px;
        animation: fadeInDown .25s ease;
    }

    [data-bs-theme="dark"] #seccionProducto {
        background: #1e2d3d;
        border-color: #2c5282;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── CARD / FILTER ────────────────────────────────────────── */
    .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); }
    [data-bs-theme="dark"] .card { background-color: #2d3236; }

    .filter-section {
        background: var(--dashboard-card-bg);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        margin-bottom: 20px;
    }

    [data-bs-theme="dark"] .filter-section { background-color: #2d3236; }

    .filter-section .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--dashboard-text-color);
    }

    .btn-filter { min-width: 100px; }

    .card-header {
        background-color: var(--dashboard-card-bg);
        border-bottom: 1px solid rgba(0,0,0,.125);
    }

    [data-bs-theme="dark"] .card-header {
        background-color: #2d3236;
        border-bottom-color: #495057;
    }

    .card-header .card-title {
        font-size: 1.1rem !important;
        font-weight: 600 !important;
        margin-bottom: 0 !important;
    }
</style>

<!-- Page Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-shopping-cart"></i> Gestión de Compras</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item active">Compras</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- ── ESTADÍSTICAS ───────────────────────────────────── -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard primary">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Compras Hoy</h6>
                            <h3 class="card-title" id="comprasHoy"><span class="loader"></span></h3>
                            <small class="text-muted">Registros de hoy</small>
                        </div>
                        <div class="card-icon text-primary"><i class="fas fa-box"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard success">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Total Invertido</h6>
                            <h3 class="card-title" id="totalInvertido"><span class="loader"></span></h3>
                            <small class="text-muted">Inversión de hoy</small>
                        </div>
                        <div class="card-icon text-success"><i class="fas fa-dollar-sign"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard warning">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Productos Comprados</h6>
                            <h3 class="card-title" id="productosComprados"><span class="loader"></span></h3>
                            <small class="text-muted">Unidades hoy</small>
                        </div>
                        <div class="card-icon text-warning"><i class="fas fa-cubes"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard info">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Compras del Mes</h6>
                            <h3 class="card-title" id="comprasMes"><span class="loader"></span></h3>
                            <small class="text-muted">Total mensual</small>
                        </div>
                        <div class="card-icon text-info"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── FILTROS ─────────────────────────────────────────── -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label for="filtroFechaInicio" class="form-label">
                        <i class="fas fa-calendar-day"></i> Fecha Inicio
                    </label>
                    <input type="date" class="form-control" id="filtroFechaInicio">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="filtroFechaFin" class="form-label">
                        <i class="fas fa-calendar-day"></i> Fecha Fin
                    </label>
                    <input type="date" class="form-control" id="filtroFechaFin">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="filtroProveedor" class="form-label">
                        <i class="fas fa-truck"></i> Proveedor
                    </label>
                    <input type="text" class="form-control" id="filtroProveedor" placeholder="Buscar por proveedor...">
                </div>
                <div class="col-md-3 mb-2">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-filter" id="btnAplicarFiltros">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary btn-filter" id="btnLimpiarFiltros">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── TABLA ──────────────────────────────────────────── -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-list"></i> Historial de Compras
                        </h3>
                        <button type="button" class="btn btn-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalCompra">
                            <i class="fas fa-plus"></i> Nueva Compra
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="tablaCompras" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                    <th>Proveedor</th>
                                    <th>Stock Nuevo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ── MODAL NUEVA COMPRA ─────────────────────────────────────── -->
<div class="modal fade" id="modalCompra" tabindex="-1"
     aria-labelledby="modalCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title" id="modalCompraLabel">
                    <i class="fas fa-shopping-cart"></i> Registrar Nueva Compra
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formCompra" autocomplete="off">
                    <input type="hidden" id="hiddenIdProducto" name="id_producto">

                    <!-- ── Búsqueda ─────────────────────────────── -->
                    <div class="mb-3 busqueda-wrapper">
                        <label for="inputBuscar" class="form-label">
                            <i class="fas fa-search"></i> Buscar Producto
                        </label>
                        <input type="text" class="form-control" id="inputBuscar"
                               placeholder="Escriba nombre o código del producto..."
                               autocomplete="off">
                        {{-- Lista de resultados posicionada absolutamente --}}
                        <div id="listaBusqueda" class="list-group"></div>
                    </div>

                    <!-- ── Sección del producto seleccionado ────── -->
                    <div id="seccionProducto" style="display:none;">

                        <div class="alert alert-info mb-3">
                            <strong>Producto:</strong> <span id="txtProducto"></span><br>
                            <strong>Stock Actual:</strong>
                            <span id="txtStock" class="badge bg-secondary"></span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inputCantidad" class="form-label">
                                    <i class="fas fa-sort-numeric-up"></i> Cantidad a Comprar *
                                </label>
                                <input type="number" class="form-control" id="inputCantidad"
                                       min="1" value="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="inputPrecio" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Precio de Compra *
                                </label>
                                <input type="number" class="form-control" id="inputPrecio"
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inputProveedor" class="form-label">
                                    <i class="fas fa-truck"></i> Proveedor
                                </label>
                                <input type="text" class="form-control" id="inputProveedor"
                                       placeholder="Nombre del proveedor">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="inputFactura" class="form-label">
                                    <i class="fas fa-file-invoice"></i> N° Factura
                                </label>
                                <input type="text" class="form-control" id="inputFactura"
                                       placeholder="Número de factura">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inputFecha" class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha de Compra *
                                </label>
                                <input type="date" class="form-control" id="inputFecha"
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="inputMetodo" class="form-label">
                                    <i class="fas fa-credit-card"></i> Método de Pago
                                </label>
                                <select class="form-control" id="inputMetodo">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="inputNotas" class="form-label">
                                <i class="fas fa-comment"></i> Notas Adicionales
                            </label>
                            <textarea class="form-control" id="inputNotas" rows="2"
                                      placeholder="Observaciones opcionales..."></textarea>
                        </div>

                        <div class="alert alert-success mb-0">
                            <strong>Total a Pagar:</strong> $<span id="txtTotal">0.00</span>
                        </div>

                    </div>{{-- /seccionProducto --}}
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardar" disabled>
                    <i class="fas fa-save"></i> Guardar Compra
                </button>
            </div>
        </div>
    </div>
</div>

                   

@endsection

@push('js')
<script>
$(document).ready(function() {
    console.log('✅ Document ready - Módulo Compras');
    
    // =========================================
    // VARIABLES GLOBALES
    // =========================================
    let productoActual = null;
    let buscarTimer = null;
    let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    // =========================================
    // BÚSQUEDA DE PRODUCTOS
    // =========================================
    $('#inputBuscar').on('input', function() {
        let termino = $(this).val().trim();
        console.log('🔍 Input detectado:', termino);
        
        clearTimeout(buscarTimer);
        
        if (termino.length < 2) {
            $('#listaBusqueda').hide();
            return;
        }
        
        buscarTimer = setTimeout(function() {
            buscarProductos(termino);
        }, 300);
    });
    
    function buscarProductos(termino) {
        console.log('🔎 Buscando productos con término:', termino);
        
        // Mostrar spinner
        $('#listaBusqueda').html(
            '<div class="list-group-item text-center py-2">' +
            '<span class="spinner-border spinner-border-sm text-primary me-1"></span>' +
            ' Buscando...</div>'
        ).show();
        
        // Hacer la petición AJAX
        $.ajax({
            url: '/compras/buscar-productos', // Ajusta la URL según tu ruta
            method: 'GET',
            data: { termino: termino },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            success: function(response) {
                console.log('📦 Respuesta recibida:', response);
                
                if (response.success && response.productos && response.productos.length > 0) {
                    mostrarResultados(response.productos);
                } else {
                    $('#listaBusqueda').html(
                        '<div class="list-group-item text-center text-muted py-2">' +
                        '<i class="fas fa-search me-1"></i>No se encontraron productos</div>'
                    ).show();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error en búsqueda:', error);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                $('#listaBusqueda').html(
                    '<div class="list-group-item text-danger py-2">' +
                    '<i class="fas fa-exclamation-triangle me-1"></i>' +
                    'Error al buscar productos</div>'
                ).show();
            }
        });
    }
    
   function mostrarResultados(productos) {
    let html = '';
    
    productos.forEach(function(producto) {
        let stockClass = producto.stock > 10 ? 'bg-success' :
                        producto.stock > 0 ? 'bg-warning text-dark' : 'bg-danger';
        
        html += `
            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 producto-item"
                    data-id="${producto.id_producto}"
                    data-nombre="${producto.nombre}"
                    data-codigo="${producto.codigo || ''}"
                    data-stock="${producto.stock}"
                    data-precio="${producto.precio_venta || 0}">
                <div>
                    <strong>${escapeHtml(producto.nombre)}</strong>
                    ${producto.codigo ? `<br><small class="text-muted">${escapeHtml(producto.codigo)}</small>` : ''}
                </div>
                <span class="badge ${stockClass}">Stock: ${producto.stock}</span>
            </button>
        `;
    });
    
    $('#listaBusqueda').html(html).show();
    
    // Agregar evento click a cada resultado
    $('.producto-item').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let id = $(this).data('id');
        let nombre = $(this).data('nombre');
        let codigo = $(this).data('codigo');
        let stock = parseInt($(this).data('stock'));
        let precio = parseFloat($(this).data('precio'));
        
        console.log('🎯 Producto seleccionado:', { id, nombre, codigo, stock, precio });
        
        seleccionarProducto(id, nombre, codigo, stock, precio);
    });
}

// =========================================
// SELECCIONAR PRODUCTO Y MOSTRAR SECCIÓN
// =========================================
function seleccionarProducto(id, nombre, codigo, stock, precio) {
    console.log('✅ Producto seleccionado:', { id, nombre, codigo, stock, precio });
    
    // Guardar producto actual
    productoActual = { id, nombre, codigo, stock, precio };
    
    // Llenar campos ocultos y de texto
    $('#hiddenIdProducto').val(id);
    $('#inputBuscar').val(nombre);
    $('#txtProducto').text(nombre + (codigo ? ' (' + codigo + ')' : ''));
    $('#txtStock').text(stock);
    $('#inputPrecio').val(precio > 0 ? precio : '');
    
    // 👉 MOSTRAR LA SECCIÓN DEL PRODUCTO
    $('#seccionProducto').show();
    
    // Ocultar lista de resultados
    $('#listaBusqueda').hide();
    
    // Habilitar botón guardar
    $('#btnGuardar').prop('disabled', false);
    
    // Calcular total inicial
    calcularTotal();
    
    // Scroll suave hacia la sección
    setTimeout(function() {
        $('html, body').animate({
            scrollTop: $('#seccionProducto').offset().top - 100
        }, 500);
    }, 100);
    
    // Opcional: Enfocar el campo cantidad
    setTimeout(function() {
        $('#inputCantidad').focus();
    }, 300);
}


$(document).on('click', function(e) {
    if (!$(e.target).closest('#inputBuscar, #listaBusqueda, .producto-item').length) {
        $('#listaBusqueda').hide();
    }
});

    // =========================================
    // CÁLCULO DE TOTAL
    // =========================================
   
    function calcularTotal() {
    let cantidad = parseFloat($('#inputCantidad').val()) || 0;
    let precio = parseFloat($('#inputPrecio').val()) || 0;
    let total = cantidad * precio;
    
    $('#txtTotal').text(total.toFixed(2));
    }

    // Actualizar total cuando cambien cantidad o precio
    $('#inputCantidad, #inputPrecio').on('input', calcularTotal);
    
    // =========================================
    // OCULTAR LISTA AL HACER CLICK FUERA
    // =========================================
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#inputBuscar, #listaBusqueda').length) {
            $('#listaBusqueda').hide();
        }
    });
    
    // =========================================
    // LIMPIAR FORMULARIO AL CERRAR MODAL
    // =========================================
    $('#modalCompra').on('hidden.bs.modal', function() {
        console.log('🧹 Limpiando formulario');
        
        $('#formCompra')[0].reset();
        $('#hiddenIdProducto').val('');
        $('#txtProducto').text('');
        $('#txtStock').text('');
        $('#txtTotal').text('0.00');
        $('#seccionProducto').hide();
        $('#listaBusqueda').hide();
        $('#btnGuardar').prop('disabled', true);
        
        productoActual = null;
    });
    
    // =========================================
    // FUNCIÓN PARA ESCAPAR HTML
    // =========================================
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    
    // =========================================
    // GUARDAR COMPRA
    // =========================================
    $('#btnGuardar').on('click', function(e) {
    e.preventDefault();
    
    if (!productoActual) {
        toastr.warning('Debe seleccionar un producto');
        return;
    }
    
    let cantidad = parseInt($('#inputCantidad').val());
    let precio = parseFloat($('#inputPrecio').val());
    let id_proveedor = $('#inputProveedor').val();
    let fecha = $('#inputFecha').val();
    
    console.log('📦 Datos a enviar:', {
        id_producto: $('#hiddenIdProducto').val(),
        cantidad: cantidad,
        precio_unitario: precio,
        id_proveedor: id_proveedor || null,
        numero_factura: $('#inputFactura').val(),
        fecha_compra: fecha,
        metodo_pago: $('#inputMetodo').val(),
        notas: $('#inputNotas').val()
    });
    
    // Validaciones básicas antes de enviar
    if (isNaN(cantidad) || cantidad <= 0) {
        toastr.warning('La cantidad debe ser mayor a 0');
        return;
    }
    
    if (isNaN(precio) || precio < 0) {
        toastr.warning('El precio debe ser mayor o igual a 0');
        return;
    }
    
    if (!fecha) {
        toastr.warning('La fecha es requerida');
        return;
    }
    
    let datos = {
        id_producto: $('#hiddenIdProducto').val(),
        cantidad: cantidad,
        precio_unitario: precio,
        id_proveedor: id_proveedor || null,
        numero_factura: $('#inputFactura').val() || '',
        fecha_compra: fecha,
        metodo_pago: $('#inputMetodo').val(),
        notas: $('#inputNotas').val() || ''
    };
        
        console.log('📦 Guardando compra:', datos);
        
        let btn = $(this);
        btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...').prop('disabled', true);
        
        $.ajax({
            url: '/compras/guardar',
            method: 'POST',
            data: JSON.stringify(datos),
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            success: function(response) {
                console.log('✅ Compra guardada:', response);
                
                if (response.success) {
                    toastr.success('Compra registrada correctamente');
                    
                    $('#modalCompra').modal('hide');
                    
                    // Recargar tabla si existe
                    if ($.fn.DataTable && $('#tablaCompras').length) {
                        $('#tablaCompras').DataTable().ajax.reload();
                    }
                    
                    // Recargar estadísticas
                    cargarEstadisticas();
                } else {
                    toastr.error(response.message || 'Error al guardar');
                }
            },
            error: function(xhr) {
                console.error('❌ Error:', xhr);
                
                let mensaje = 'Error al guardar la compra';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    mensaje = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                
                toastr.error(mensaje);
            },
            complete: function() {
                btn.html('<i class="fas fa-save"></i> Guardar Compra').prop('disabled', false);
            }
        });
    });
    
    // =========================================
    // CARGAR ESTADÍSTICAS
    // =========================================
    function cargarEstadisticas() {
        $.ajax({
            url: '/compras/estadisticas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#comprasHoy').text(response.compras_hoy || 0);
                    $('#totalInvertido').text('$' + (response.total_invertido || 0).toFixed(2));
                    $('#productosComprados').text(response.productos_comprados || 0);
                    $('#comprasMes').text(response.compras_mes || 0);
                }
            },
            error: function(xhr) {
                console.error('Error cargando estadísticas:', xhr);
            }
        });
    }
    
    // Cargar estadísticas al inicio
    cargarEstadisticas();
});
</script>
@endpush