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

/* Estilos para botones de acción */
.btn-group .btn-xs {
    padding: 0.20rem 0.4rem;
    font-size: 0.75rem;
    border-radius: 0.2rem;
    margin: 0 2px;
}

.btn-group .btn-info {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-group .btn-warning {
    color: #212529;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-group .btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Estilo para la tabla responsive */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

#tablaCompras {
    width: 100% !important;
    font-size: 0.9rem;
}

#tablaCompras th {
    white-space: nowrap;
}

/* Estilos para botones de acción */
.btn-group .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    margin: 0 2px;
}

.btn-group .btn-info {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-group .btn-warning {
    color: #212529;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-group .btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-group .btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-group .btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-group .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Asegurar que los botones sean visibles */
#tablaCompras td .btn-group {
    display: flex;
    justify-content: center;
    white-space: nowrap;
}

#tablaCompras td {
    vertical-align: middle;
}

/* Estilos para el modal de edición */
#modalEditarCompra .modal-header {
    border-bottom: 1px solid #dee2e6;
}

#modalEditarCompra .modal-footer {
    border-top: 1px solid #dee2e6;
}

#modalEditarCompra .form-control-plaintext {
    background-color: #f8f9fa;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

[data-bs-theme="dark"] #modalEditarCompra .form-control-plaintext {
    background-color: #2d3236;
    border-color: #495057;
    color: #e9ecef;
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
                        </div>
                        <div class="card-icon text-primary"><i class="fas fa-box"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard success">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Total Invertido hoy</h6>
                            <h3 class="card-title" id="totalInvertido"><span class="loader"></span></h3>
                            
                        </div>
                        <div class="card-icon text-success"><i class="fas fa-dollar-sign"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card card-dashboard warning">
                    <div class="card-body d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="card-subtitle">Productos Comprados hoy</h6>
                            <h3 class="card-title" id="productosComprados"><span class="loader"></span></h3>
                            
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
                   <div class="d-flex">
                        <button type="button" class="btn btn-primary btn-sm" id="btnAplicarFiltros" style="margin-right: 12px; width: 48%;">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnLimpiarFiltros" style="width: 48%;">
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
                      <div class="table-responsive">
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                      </div>  
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
                                    <select id="id_proveedor" name="id_proveedor" class="form-control">
                                        <option value="">Cargando proveedores...</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
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

<!-- ========================================= -->
<!-- MODAL EDITAR COMPRA -->
<!-- ========================================= -->

<div class="modal fade" id="modalEditarCompra" tabindex="-1" aria-labelledby="modalEditarCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEditarCompraLabel">
                    <i class="fas fa-edit"></i> Editar Compra #<span id="editar_id_compra"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formEditarCompra">
                    <input type="hidden" id="editar_id_inventario" name="id_inventario">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Producto:</label>
                                <p class="form-control-plaintext" id="editar_producto_nombre"></p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_cantidad" class="form-label">
                                    <i class="fas fa-sort-numeric-up"></i> Cantidad *
                                </label>
                                <input type="number" class="form-control" id="editar_cantidad" min="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_precio" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Precio de Compra *
                                </label>
                                <input type="number" step="0.01" class="form-control" id="editar_precio" min="0" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_proveedor" class="form-label">
                                    <i class="fas fa-truck"></i> Proveedor
                                </label>
                                <input type="text" class="form-control" id="editar_proveedor" placeholder="Nombre del proveedor">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editar_factura" class="form-label">
                                    <i class="fas fa-file-invoice"></i> N° Factura
                                </label>
                                <input type="text" class="form-control" id="editar_factura" placeholder="Número de factura">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_fecha" class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha de Compra *
                                </label>
                                <input type="date" class="form-control" id="editar_fecha" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_metodo_pago" class="form-label">
                                    <i class="fas fa-credit-card"></i> Método de Pago
                                </label>
                                <select class="form-control" id="editar_metodo_pago">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_notas" class="form-label">
                                    <i class="fas fa-comment"></i> Notas Adicionales
                                </label>
                                <textarea class="form-control" id="editar_notas" rows="3" placeholder="Observaciones opcionales..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Stock actual del producto:</strong> <span id="editar_stock_actual" class="badge bg-secondary">0</span>
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Total:</strong> $<span id="editar_total">0.00</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btnActualizarCompra">
                    <i class="fas fa-save"></i> Actualizar Compra
                </button>
            </div>
        </div>
    </div>
</div>
                   

@endsection

@push('js')

<script>
$(document).ready(function() {
    console.log('Document ready - Inicializando...');
    
    // =========================================
    // VARIABLES GLOBALES
    // =========================================
    let productoActual = null;
    let buscarTimer = null;
    let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    // =========================================
    // CONFIGURACIÓN AJAX
    // =========================================
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    });
    
    // =========================================
    // INICIALIZAR DATATABLE
    // =========================================
    if ($.fn.DataTable.isDataTable('#tablaCompras')) {
        $('#tablaCompras').DataTable().destroy();
    }
    
    var tablaCompras = $('#tablaCompras').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/compras/listar',
            type: 'GET',
            data: function(d) {
                d.fecha_inicio = $('#filtroFechaInicio').val();
                d.fecha_fin = $('#filtroFechaFin').val();
                d.proveedor = $('#filtroProveedor').val();
            },
            error: function(xhr, error, code) {
                console.log('Error en AJAX:', error);
                console.log('Respuesta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id_compra', name: 'id_compra' },
            { 
                data: 'fecha_compra', 
                name: 'fecha_compra',
                render: function(data) {
                    if (data) {
                        var fecha = new Date(data);
                        return fecha.toLocaleDateString('es-ES') + ' ' + 
                               fecha.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
                    }
                    return '';
                }
            },
            { 
                data: null,
                name: 'producto',
                render: function(data) {
                    return '<strong>' + (data.producto_nombre || 'N/A') + '</strong>' + 
                           (data.producto_codigo ? '<br><small>' + data.producto_codigo + '</small>' : '');
                }
            },
            { 
                data: 'cantidad', 
                name: 'cantidad',
                className: 'text-center',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            { 
                data: 'precio_compra', 
                name: 'precio_compra',
                className: 'text-right',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'total', 
                name: 'total',
                className: 'text-right',
                render: function(data) {
                    return '<strong>$' + parseFloat(data).toFixed(2) + '</strong>';
                }
            },
            { 
                data: 'proveedor', 
                name: 'proveedor',
                render: function(data) {
                    return data || 'Sin proveedor';
                }
            },
            { 
                data: 'stock_nuevo', 
                name: 'stock_nuevo',
                className: 'text-center',
                render: function(data) {
                    var clase = data > 10 ? 'success' : (data > 5 ? 'warning' : 'danger');
                    return '<span class="badge bg-' + clase + '">' + data + '</span>';
                }
            },
            { 
                data: 'acciones', 
                name: 'acciones',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        language: {
            "emptyTable": "No hay compras registradas",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron resultados",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        order: [[0, 'desc']],
        drawCallback: function(settings) {
            console.log('DataTable redibujado - eventos de botones reasignados automáticamente');
        }
    });
    
    // =========================================
    // FILTROS
    // =========================================
    $('#btnAplicarFiltros').click(function() {
        var fechaInicio = $('#filtroFechaInicio').val();
        var fechaFin = $('#filtroFechaFin').val();
        
        if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
            toastr.warning('La fecha de inicio no puede ser mayor a la fecha fin');
            return;
        }
        
        tablaCompras.ajax.reload();
    });
    
    $('#btnLimpiarFiltros').click(function() {
        $('#filtroFechaInicio').val('');
        $('#filtroFechaFin').val('');
        $('#filtroProveedor').val('');
        tablaCompras.ajax.reload();
    });
    
    // =========================================
    // ACCIONES DE LA TABLA - VERSIÓN CORREGIDA
    // =========================================
    
    // VER COMPRA
    $(document).on('click', '.ver-compra', function() {
        var id = $(this).data('id');
        console.log('Ver compra ID:', id);
        
        $.ajax({
            url: '/compras/mostrar/' + id,
            type: 'GET',
            success: function(response) {
                console.log('Respuesta ver compra:', response);
                
                if (response.success) {
                    var data = response.data;
                    var fecha = data.fecha_compra ? new Date(data.fecha_compra).toLocaleString('es-ES') : 'N/A';
                    var total = (data.cantidad * data.precio_compra).toFixed(2);
                    
                    Swal.fire({
                        title: 'Detalles de la Compra',
                        html: `
                            <div style="text-align: left; max-height: 400px; overflow-y: auto;">
                                <p><strong>ID:</strong> ${data.id_compra}</p>
                                <p><strong>Fecha:</strong> ${fecha}</p>
                                <p><strong>Producto:</strong> ${data.producto?.nombre || 'N/A'}</p>
                                <p><strong>Cantidad:</strong> ${data.cantidad}</p>
                                <p><strong>Precio Unit.:</strong> $${parseFloat(data.precio_compra).toFixed(2)}</p>
                                <p><strong>Total:</strong> $${total}</p>
                                <p><strong>Proveedor:</strong> ${data.proveedor || 'Sin proveedor'}</p>
                                <p><strong>Factura:</strong> ${data.numero_factura || 'N/A'}</p>
                                <p><strong>Método Pago:</strong> ${data.metodo_pago || 'N/A'}</p>
                                <p><strong>Stock Nuevo:</strong> ${data.stock_nuevo}</p>
                                <p><strong>Notas:</strong> ${data.notas || 'Sin notas'}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        width: '600px'
                    });
                } else {
                    toastr.error(response.message || 'Error al cargar los detalles');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                toastr.error('Error al cargar los detalles de la compra');
            }
        });
    });
    
   // =========================================
// EDITAR COMPRA - FUNCIONALIDAD COMPLETA
// =========================================
$(document).on('click', '.editar-compra', function() {
    var id = $(this).data('id');
    console.log('Editando compra ID:', id);
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando datos...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Obtener datos de la compra
    $.ajax({
        url: '/compras/mostrar/' + id,
        type: 'GET',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                var data = response.data;
                
                // Llenar el formulario de edición
                $('#editar_id_inventario').val(data.id_compra);
                $('#editar_id_compra').text(data.id_compra);
                $('#editar_producto_nombre').text(data.producto?.nombre || 'N/A');
                $('#editar_cantidad').val(data.cantidad);
                $('#editar_precio').val(data.precio_compra);
                $('#editar_proveedor').val(data.proveedor || '');
                $('#editar_factura').val(data.numero_factura || '');
                
                // Formatear fecha para input type="date"
                if (data.fecha_compra) {
                    var fecha = new Date(data.fecha_compra);
                    var año = fecha.getFullYear();
                    var mes = String(fecha.getMonth() + 1).padStart(2, '0');
                    var dia = String(fecha.getDate()).padStart(2, '0');
                    $('#editar_fecha').val(año + '-' + mes + '-' + dia);
                } else {
                    $('#editar_fecha').val('');
                }
                
                $('#editar_metodo_pago').val(data.metodo_pago || 'efectivo');
                $('#editar_notas').val(data.notas || '');
                $('#editar_stock_actual').text(data.stock_nuevo || 0);
                
                // Calcular total
                calcularTotalEditar();
                
                // Abrir modal
                $('#modalEditarCompra').modal('show');
            } else {
                toastr.error(response.message || 'Error al cargar los datos');
            }
        },
        error: function(xhr) {
            Swal.close();
            console.error('Error:', xhr);
            toastr.error('Error al cargar los datos de la compra');
        }
    });
});

// Calcular total en edición
function calcularTotalEditar() {
    let cantidad = parseFloat($('#editar_cantidad').val()) || 0;
    let precio = parseFloat($('#editar_precio').val()) || 0;
    let total = cantidad * precio;
    $('#editar_total').text(total.toFixed(2));
}

// Eventos para calcular total en tiempo real
$('#editar_cantidad, #editar_precio').on('input', calcularTotalEditar);

// =========================================
// ACTUALIZAR COMPRA
// =========================================
$('#btnActualizarCompra').on('click', function(e) {
    e.preventDefault();
    
    let id = $('#editar_id_inventario').val();
    
    if (!id) {
        toastr.error('Error: ID de compra no válido');
        return;
    }
    
    // Validar campos requeridos
    let cantidad = $('#editar_cantidad').val();
    let precio = $('#editar_precio').val();
    let fecha = $('#editar_fecha').val();
    
    if (!cantidad || cantidad <= 0) {
        toastr.error('La cantidad debe ser mayor a 0');
        return;
    }
    
    if (!precio || precio < 0) {
        toastr.error('El precio no puede ser negativo');
        return;
    }
    
    if (!fecha) {
        toastr.error('La fecha es requerida');
        return;
    }
    
    // Preparar datos
    let datos = {
        cantidad: cantidad,
        precio_compra: precio,
        proveedor: $('#editar_proveedor').val(),
        numero_factura: $('#editar_factura').val(),
        metodo_pago: $('#editar_metodo_pago').val(),
        notas: $('#editar_notas').val()
    };
    
    console.log('Actualizando compra:', datos);
    
    // Mostrar loading en botón
    let btn = $(this);
    btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...').prop('disabled', true);
    
    $.ajax({
        url: '/compras/actualizar/' + id,
        method: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta:', response);
            
            if (response.success) {
                toastr.success('Compra actualizada correctamente');
                
                // Cerrar modal
                $('#modalEditarCompra').modal('hide');
                
                // Recargar tabla y estadísticas
                tablaCompras.ajax.reload(null, false);
                cargarEstadisticas();
            } else {
                toastr.error(response.message || 'Error al actualizar');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            
            let mensaje = 'Error al actualizar la compra';
            if (xhr.status === 422 && xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    // Mostrar errores de validación
                    let errores = xhr.responseJSON.errors;
                    let mensajes = [];
                    for (let campo in errores) {
                        mensajes.push(errores[campo][0]);
                    }
                    mensaje = mensajes.join('<br>');
                    toastr.error(mensaje, 'Errores de validación');
                } else if (xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                    toastr.error(mensaje);
                }
            } else {
                toastr.error(mensaje);
            }
        },
        complete: function() {
            btn.html('<i class="fas fa-save"></i> Actualizar Compra').prop('disabled', false);
        }
    });
});

// Limpiar modal al cerrar
$('#modalEditarCompra').on('hidden.bs.modal', function() {
    $('#formEditarCompra')[0].reset();
    $('#editar_id_inventario').val('');
    $('#editar_id_compra').text('');
    $('#editar_producto_nombre').text('');
    $('#editar_stock_actual').text('0');
    $('#editar_total').text('0.00');
});
    
    // ELIMINAR/ANULAR COMPRA
    $(document).on('click', '.eliminar-compra', function() {
        var id = $(this).data('id');
        console.log('Anular compra ID:', id);
        
        Swal.fire({
            title: '¿Anular compra?',
            text: 'Esta acción revertirá el movimiento de inventario y no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                anularCompra(id);
            }
        });
    });
    
    function anularCompra(id) {
        Swal.fire({
            title: 'Anulando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '/compras/anular/' + id,
            type: 'DELETE',
            data: {
                _token: CSRF_TOKEN
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        title: '¡Anulada!',
                        text: response.message || 'Compra anulada correctamente',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    tablaCompras.ajax.reload(null, false);
                    cargarEstadisticas();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Error al anular la compra',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                console.error('Error:', xhr);
                
                let errorMessage = 'Error al anular la compra';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    }
    
    // =========================================
    // CARGAR PROVEEDORES
    // =========================================
    function cargarProveedores() {
        console.log('Cargando proveedores...');
        
        const $selectProveedor = $('#id_proveedor');
        $selectProveedor.html('<option value="">Cargando proveedores...</option>').prop('disabled', true);
        
        $.ajax({
            url: '/proveedores/lista',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Proveedores recibidos:', response);
                
                $selectProveedor.empty().prop('disabled', false);
                $selectProveedor.append('<option value="">Seleccione un proveedor</option>');
                
                if (Array.isArray(response) && response.length > 0) {
                    $.each(response, function(index, proveedor) {
                        let optionValue = proveedor.id_proveedor;
                        let optionText = proveedor.razon_social + ' (' + (proveedor.nit || 'Sin NIT') + ')';
                        $selectProveedor.append('<option value="' + optionValue + '">' + optionText + '</option>');
                    });
                } else {
                    $selectProveedor.append('<option value="" disabled>No hay proveedores disponibles</option>');
                }
            },
            error: function(xhr) {
                console.error('Error cargando proveedores:', xhr);
                $selectProveedor.empty().prop('disabled', false);
                $selectProveedor.append('<option value="">Error al cargar proveedores</option>');
                toastr.error('Error al cargar proveedores');
            }
        });
    }
    
    // =========================================
    // BÚSQUEDA DE PRODUCTOS
    // =========================================
    $('#inputBuscar').on('input', function() {
        let termino = $(this).val().trim();
        console.log('Término de búsqueda:', termino);
        
        clearTimeout(buscarTimer);
        
        if (termino.length < 2) {
            $('#listaBusqueda').empty().hide();
            return;
        }
        
        buscarTimer = setTimeout(function() {
            buscarProductos(termino);
        }, 300);
    });
    
    function buscarProductos(termino) {
        console.log('Buscando productos con término:', termino);
        
        $('#listaBusqueda').html(
            '<div class="list-group-item text-center py-2">' +
            '<span class="spinner-border spinner-border-sm text-primary me-1"></span>' +
            ' Buscando...</div>'
        ).show();
        
        $.ajax({
            url: '/compras/buscar-productos',
            method: 'GET',
            data: { termino: termino },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de búsqueda:', response);
                
                if (response.success && response.productos && response.productos.length > 0) {
                    mostrarResultados(response.productos);
                } else {
                    $('#listaBusqueda').html(
                        '<div class="list-group-item text-center text-muted py-2">' +
                        '<i class="fas fa-search me-1"></i> No se encontraron productos</div>'
                    ).show();
                }
            },
            error: function(xhr) {
                console.error('Error en búsqueda:', xhr);
                $('#listaBusqueda').html(
                    '<div class="list-group-item text-danger py-2">' +
                    '<i class="fas fa-exclamation-triangle me-1"></i>' +
                    ' Error al buscar productos</div>'
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
    }
    
    $(document).on('click', '.producto-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let id = $(this).data('id');
        let nombre = $(this).data('nombre');
        let codigo = $(this).data('codigo');
        let stock = parseInt($(this).data('stock'));
        let precio = parseFloat($(this).data('precio'));
        
        console.log('Producto seleccionado:', { id, nombre, codigo, stock, precio });
        
        seleccionarProducto(id, nombre, codigo, stock, precio);
    });
    
    function seleccionarProducto(id, nombre, codigo, stock, precio) {
        productoActual = { id, nombre, codigo, stock, precio };
        
        $('#hiddenIdProducto').val(id);
        $('#inputBuscar').val(nombre);
        $('#txtProducto').text(nombre +  (codigo ? ' (' + codigo + ')' : ''));
        $('#txtStock').text(stock);
        $('#inputPrecio').val(precio > 0 ? precio : '');
        $('#seccionProducto').show();
        $('#listaBusqueda').hide();
        $('#btnGuardar').prop('disabled', false);
        
        calcularTotal();
        
        setTimeout(function() {
            $('#inputCantidad').focus();
        }, 300);
    }
    
    function calcularTotal() {
        let cantidad = parseFloat($('#inputCantidad').val()) || 0;
        let precio = parseFloat($('#inputPrecio').val()) || 0;
        let total = cantidad * precio;
        $('#txtTotal').text(total.toFixed(2));
    }
    
    $('#inputCantidad, #inputPrecio').on('input', calcularTotal);
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#inputBuscar, #listaBusqueda, .producto-item').length) {
            $('#listaBusqueda').hide();
        }
    });
    
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
        
        let datos = {
            id_producto: $('#hiddenIdProducto').val(),
            cantidad_comprada: $('#inputCantidad').val(),
            precio_compra: $('#inputPrecio').val(),
            id_proveedor: $('#id_proveedor').val() || null,
            numero_factura: $('#inputFactura').val() || '',
            fecha_compra: $('#inputFecha').val(),
            metodo_pago: $('#inputMetodo').val() || 'efectivo',
            notas: $('#inputNotas').val() || ''
        };
        
        console.log('Datos a enviar:', datos);
        
        if (!datos.id_producto || !datos.cantidad_comprada || datos.cantidad_comprada <= 0 || 
            !datos.precio_compra || datos.precio_compra < 0 || !datos.fecha_compra) {
            toastr.error('Complete todos los campos requeridos');
            return;
        }
        
        let btn = $(this);
        btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...').prop('disabled', true);
        
        $.ajax({
            url: '/compras/guardar',
            method: 'POST',
            data: JSON.stringify(datos),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                
                toastr.success('Compra registrada correctamente');
                
                let modal = bootstrap.Modal.getInstance($('#modalCompra')[0]);
                if (modal) {
                    modal.hide();
                }
                
                setTimeout(function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('overflow', '');
                }, 150);
                
                tablaCompras.ajax.reload();
                cargarEstadisticas();
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                
                let mensaje = 'Error al guardar la compra';
                if (xhr.status === 422 && xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        mensaje = 'Errores de validación';
                        console.error(xhr.responseJSON.errors);
                    } else if (xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                }
                
                toastr.error(mensaje);
            },
            complete: function() {
                btn.html('<i class="fas fa-save"></i> Guardar Compra').prop('disabled', false);
            }
        });
    });
    
    // =========================================
    // EVENTOS DEL MODAL
    // =========================================
    $('#modalCompra').on('show.bs.modal', function() {
        console.log('Abriendo modal - cargando proveedores');
        cargarProveedores();
    });
    
    $('#modalCompra').on('shown.bs.modal', function() {
        console.log('Modal mostrado - enfocando búsqueda');
        $('#inputBuscar').focus();
    });
    
    $('#modalCompra').on('hidden.bs.modal', function() {
        console.log('Cerrando modal - limpiando formulario');
        
        $('#formCompra')[0].reset();
        $('#hiddenIdProducto').val('');
        $('#txtProducto').text('');
        $('#txtStock').text('');
        $('#txtTotal').text('0.00');
        $('#seccionProducto').hide();
        $('#listaBusqueda').empty().hide();
        $('#btnGuardar').prop('disabled', true);
        productoActual = null;
        
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('overflow', '');
        }, 100);
    });
    
    // =========================================
    // CARGAR ESTADÍSTICAS
    // =========================================
    function cargarEstadisticas() {
        $.ajax({
            url: '/compras/estadisticas',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#comprasHoy').text(response.compras_hoy || 0);
                    $('#totalInvertido').text('$' + Math.round(response.total_invertido || 0).toLocaleString());
                    $('#productosComprados').text(response.productos_comprados || 0);
                    $('#comprasMes').text(response.compras_mes || 0);
                }
            },
            error: function() {
                $('#comprasHoy, #productosComprados, #comprasMes').text('0');
                $('#totalInvertido').text('$0');
            }
        });
    }
    
    // =========================================
    // INICIALIZACIÓN
    // =========================================
    cargarEstadisticas();
    setInterval(cargarEstadisticas, 30000);
    
});
</script>

@endpush