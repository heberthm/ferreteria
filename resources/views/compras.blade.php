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
/* ============================================================
   MÓDULO COMPRAS
   ============================================================ */

var productoActual = null;
var buscarTimer    = null;   // debounce
var CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/* ── Polyfill mínimo de SweetAlert2 ──────────────────────── */
if (typeof Swal === 'undefined') {
    console.warn('⚠️ SweetAlert2 no cargado. Usando alert() nativo.');
    window.Swal = {
        fire: function(opts) {
            var msg = (opts.title || '') + '\n' + (opts.text || opts.html || '');
            alert(msg.trim());
            return Promise.resolve({ isConfirmed: true });
        }
    };
}

/* ── INICIO ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ Página de compras cargada');
    inicializar();
});

function inicializar() {

    var inputBuscar   = document.getElementById('inputBuscar');
    var listaBusqueda = document.getElementById('listaBusqueda');
    var btnGuardar    = document.getElementById('btnGuardar');
    var inputCantidad = document.getElementById('inputCantidad');
    var inputPrecio   = document.getElementById('inputPrecio');

    if (!inputBuscar) { console.error('❌ #inputBuscar no encontrado'); return; }

    /* ── Búsqueda con debounce de 250 ms ─────────────────── */
    inputBuscar.addEventListener('input', function () {
        clearTimeout(buscarTimer);
        buscarTimer = setTimeout(buscarProducto, 250);
    });

    /* ── Ocultar lista al hacer clic fuera ───────────────── */
    document.addEventListener('click', function (e) {
        if (e.target !== inputBuscar && !listaBusqueda.contains(e.target)) {
            ocultarLista();
        }
    });

    /* ── Tecla Escape cierra la lista ───────────────────── */
    inputBuscar.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') ocultarLista();
    });

    /* ── Eventos del modal ───────────────────────────────── */
    var modal = document.getElementById('modalCompra');

    modal.addEventListener('show.bs.modal',  limpiarFormulario);

    modal.addEventListener('shown.bs.modal', function () {
        setTimeout(function () { inputBuscar.focus(); }, 200);
    });

    /* ── Botón guardar ───────────────────────────────────── */
    if (btnGuardar) btnGuardar.addEventListener('click', guardarCompra);

    /* ── Cálculo del total ───────────────────────────────── */
    if (inputCantidad) inputCantidad.addEventListener('input', calcularTotal);
    if (inputPrecio)   inputPrecio.addEventListener('input',   calcularTotal);

    /* ── Inicializar DataTable y stats ───────────────────── */
    cargarTabla();
    cargarEstadisticas();

    console.log('✅ Módulo inicializado');
}

/* ── BÚSQUEDA ───────────────────────────────────────────────── */
function buscarProducto() {
    var input  = document.getElementById('inputBuscar');
    var lista  = document.getElementById('listaBusqueda');
    var termino = input.value.trim();

    if (termino.length < 2) { ocultarLista(); return; }

    /* Mostrar spinner */
    lista.innerHTML =
        '<div class="list-group-item text-center py-2">' +
        '<span class="spinner-border spinner-border-sm text-primary me-1"></span>' +
        ' Buscando...</div>';
    lista.style.display = 'block';

    fetch('/productos/buscar?termino=' + encodeURIComponent(termino), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(function (data) {
        if (data.success && data.productos && data.productos.length > 0) {
            renderResultados(data.productos);
        } else {
            lista.innerHTML =
                '<div class="list-group-item text-center text-muted py-2">' +
                '<i class="fas fa-search me-1"></i>No se encontraron productos</div>';
            lista.style.display = 'block';
        }
    })
    .catch(function (err) {
        console.error('❌ Error búsqueda:', err);
        lista.innerHTML =
            '<div class="list-group-item text-danger py-2">' +
            '<i class="fas fa-exclamation-triangle me-1"></i>' +
            'Error al buscar: ' + err.message + '</div>';
        lista.style.display = 'block';
    });
}

/* Renderiza los ítems de resultado usando createElement (sin onclick inline) */
function renderResultados(productos) {
    var lista = document.getElementById('listaBusqueda');
    lista.innerHTML = '';           // limpiar

    productos.forEach(function (prod) {
        var stockClass = prod.stock > 10 ? 'bg-success'
                       : prod.stock > 0  ? 'bg-warning text-dark'
                       :                   'bg-danger';

        var item = document.createElement('button');
        item.type = 'button';
        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2';

        item.innerHTML =
            '<div>' +
              '<strong>' + escapeHtml(prod.nombre) + '</strong>' +
              '<br><small class="text-muted">' + escapeHtml(prod.codigo || '') + '</small>' +
            '</div>' +
            '<span class="badge ' + stockClass + '">Stock: ' + prod.stock + '</span>';

        /* Guardar datos en dataset para evitar problemas de escapado */
        item.dataset.id     = prod.id_producto;
        item.dataset.nombre = prod.nombre;
        item.dataset.codigo = prod.codigo  || '';
        item.dataset.stock  = prod.stock;
        item.dataset.precio = prod.precio_compra || 0;

        item.addEventListener('click', function () {
            seleccionarProducto(
                this.dataset.id,
                this.dataset.nombre,
                this.dataset.codigo,
                parseInt(this.dataset.stock),
                parseFloat(this.dataset.precio)
            );
        });

        lista.appendChild(item);
    });

    lista.style.display = 'block';
}

function ocultarLista() {
    var lista = document.getElementById('listaBusqueda');
    if (lista) lista.style.display = 'none';
}

/* ── SELECCIÓN DE PRODUCTO ──────────────────────────────────── */
function seleccionarProducto(id, nombre, codigo, stock, precio) {
    console.log('✅ Producto seleccionado:', { id, nombre, codigo, stock, precio });

    productoActual = { id: id, nombre: nombre, codigo: codigo, stock: stock, precio: precio };

    /* Rellenar campos ocultos y de texto */
    document.getElementById('hiddenIdProducto').value = id;
    document.getElementById('inputBuscar').value       = nombre;
    document.getElementById('txtProducto').textContent = nombre + (codigo ? ' (' + codigo + ')' : '');
    document.getElementById('txtStock').textContent    = stock;
    document.getElementById('inputPrecio').value       = precio > 0 ? precio : '';

    /* MOSTRAR sección del producto */
    var seccion = document.getElementById('seccionProducto');
    seccion.style.display = 'block';

    /* Scroll suave dentro del modal */
    setTimeout(function () {
        seccion.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 50);

    /* Cerrar lista */
    ocultarLista();

    /* Habilitar botón */
    document.getElementById('btnGuardar').disabled = false;

    /* Calcular total */
    calcularTotal();
}

/* ── CÁLCULO TOTAL ──────────────────────────────────────────── */
function calcularTotal() {
    var cantidad = parseFloat(document.getElementById('inputCantidad').value) || 0;
    var precio   = parseFloat(document.getElementById('inputPrecio').value)   || 0;
    document.getElementById('txtTotal').textContent = (cantidad * precio).toFixed(2);
}

/* ── GUARDAR COMPRA ─────────────────────────────────────────── */
function guardarCompra() {
    if (!productoActual || !document.getElementById('hiddenIdProducto').value) {
        Swal.fire({ icon: 'warning', title: 'Producto requerido', text: 'Debe seleccionar un producto.' });
        return;
    }

    var cantidad = parseInt(document.getElementById('inputCantidad').value);
    if (isNaN(cantidad) || cantidad <= 0) {
        Swal.fire({ icon: 'warning', title: 'Cantidad inválida', text: 'La cantidad debe ser mayor a 0.' });
        return;
    }

    var precio = parseFloat(document.getElementById('inputPrecio').value);
    if (isNaN(precio) || precio < 0) {
        Swal.fire({ icon: 'warning', title: 'Precio inválido', text: 'El precio debe ser mayor o igual a 0.' });
        return;
    }

    var datos = {
        id_producto:       document.getElementById('hiddenIdProducto').value,
        cantidad_comprada: cantidad,
        precio_compra:     precio,
        proveedor:         document.getElementById('inputProveedor').value || '',
        numero_factura:    document.getElementById('inputFactura').value   || '',
        fecha_compra:      document.getElementById('inputFecha').value,
        metodo_pago:       document.getElementById('inputMetodo').value,
        notas:             document.getElementById('inputNotas').value     || ''
    };

    var btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    fetch('/compras/guardar', {
        method: 'POST',
        headers: {
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     CSRF_TOKEN,
            'Accept':           'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(function (res) {
        if (!res.ok) return res.json().then(function (e) { throw new Error(e.message || 'Error ' + res.status); });
        return res.json();
    })
    .then(function (data) {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Compra Registrada!',
                html: '<strong>Producto:</strong> '   + data.data.producto.nombre   + '<br>' +
                      '<strong>Cantidad:</strong> '   + data.data.cantidad_agregada + '<br>' +
                      '<strong>Stock Anterior:</strong> ' + data.data.stock_anterior + '<br>' +
                      '<strong>Stock Nuevo:</strong> '    + data.data.stock_nuevo,
                timer: 3000,
                showConfirmButton: true
            });

            var modalEl = document.getElementById('modalCompra');
            var bsModal = bootstrap.Modal.getInstance(modalEl);
            if (bsModal) bsModal.hide();

            cargarTabla();
            cargarEstadisticas();
            limpiarFormulario();
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
    })
    .catch(function (err) {
        console.error('❌ Error al guardar:', err);
        Swal.fire({ icon: 'error', title: 'Error al Guardar', text: err.message });
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar Compra';
    });
}

/* ── LIMPIAR FORMULARIO ─────────────────────────────────────── */
function limpiarFormulario() {
    document.getElementById('inputBuscar').value      = '';
    document.getElementById('hiddenIdProducto').value = '';
    document.getElementById('inputCantidad').value    = '1';
    document.getElementById('inputPrecio').value      = '';
    document.getElementById('inputProveedor').value   = '';
    document.getElementById('inputFactura').value     = '';
    document.getElementById('inputNotas').value       = '';
    document.getElementById('inputMetodo').value      = 'efectivo';
    document.getElementById('inputFecha').value       = new Date().toISOString().split('T')[0];
    document.getElementById('txtProducto').textContent = '';
    document.getElementById('txtStock').textContent    = '';
    document.getElementById('txtTotal').textContent    = '0.00';

    /* OCULTAR sección del producto */
    document.getElementById('seccionProducto').style.display = 'none';
    ocultarLista();

    document.getElementById('btnGuardar').disabled = true;
    productoActual = null;
}

/* ── TABLA DataTables ───────────────────────────────────────── */
function cargarTabla() {
    // Verificar que las rutas existen antes de inicializar DataTable
    fetch('/compras/listar', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
    })
    .then(r => {
        console.log('Status /compras/listar:', r.status, r.url);
        return r.json();
    })
    .then(data => console.log('Datos recibidos:', data))
    .catch(err => console.error('❌ Error listar:', err));
}

/* ── ESTADÍSTICAS ───────────────────────────────────────────── */
function cargarEstadisticas() {
    fetch('/compras/estadisticas', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
    })
    .then(function (res) {
        if (!res.ok) throw new Error('Error ' + res.status);
        return res.json();
    })
    .then(function (data) {
        if (data.success) {
            actualizarCard('comprasHoy',         data.compras_hoy       || 0);
            actualizarCard('totalInvertido',    '$' + (data.total_invertido  || 0).toFixed(2));
            actualizarCard('productosComprados', data.productos_comprados || 0);
            actualizarCard('comprasMes',         data.compras_mes        || 0);
        }
    })
    .catch(function (err) {
        console.error('❌ Error estadísticas:', err);
        ['comprasHoy','productosComprados','comprasMes'].forEach(function (id) { actualizarCard(id, 0); });
        actualizarCard('totalInvertido', '$0.00');
    });
}

function actualizarCard(id, valor) {
    var el = document.getElementById(id);
    if (!el) return;
    el.style.opacity = '0';
    setTimeout(function () { el.textContent = valor; el.style.opacity = '1'; }, 150);
}

/* ── UTILIDADES ─────────────────────────────────────────────── */
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;')
              .replace(/</g,'&lt;')
              .replace(/>/g,'&gt;')
              .replace(/"/g,'&quot;')
              .replace(/'/g,'&#039;');
}
</script>
@endpush