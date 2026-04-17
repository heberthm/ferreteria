@extends('layouts.app')
@section('content')

<style>
/* ================================================
   ESTILOS SELECT2 PERSONALIZADOS
================================================ */
.select2-container { width: 100% !important; }
.select2-container--open,
.select2-dropdown  { z-index: 99999 !important; }
.select2-selection {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    min-height: 38px !important;
    padding: 4px 12px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    background-color: #fff !important;
}
.select2-selection--single          { height: 38px !important; }
.select2-selection__rendered        { line-height: 28px !important; padding-left: 0 !important; color: #495057 !important; }
.select2-selection__placeholder     { color: #6c757d !important; }
.select2-selection__clear {
    position: absolute !important; right: 25px !important; top: 50% !important;
    transform: translateY(-50%) !important; font-size: 18px !important;
    font-weight: bold !important; color: #dc3545 !important;
    cursor: pointer !important; z-index: 10 !important;
}
.select2-selection__arrow {
    position: absolute !important; right: 8px !important; top: 50% !important;
    transform: translateY(-50%) !important; height: auto !important; width: 20px !important;
}
.select2-selection__arrow b {
    border-color: #6c757d transparent transparent transparent !important;
    border-width: 5px 4px 0 4px !important; margin-left: -8px !important;
}
.select2-container--open .select2-selection {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}
.select2-dropdown {
    border: 1px solid #ced4da !important; border-radius: 4px !important;
    font-size: 14px !important; box-shadow: 0 2px 4px rgba(0,0,0,.1) !important;
}
.select2-results__option                { padding: 8px 12px !important; color: #212529 !important; }
.select2-results__option--highlighted   { background-color: #e9ecef !important; color: #212529 !important; }
.select2-results__option[aria-selected=true] { background-color: #007bff !important; color: white !important; }

/* Mejoras para Select2 dentro del modal */
#overlayCrearOrden .select2-container--default .select2-results__option {
    padding: 8px 12px !important;
}

#overlayCrearOrden .select2-container--default .select2-results__option--highlighted {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

.select2-results__option .d-block {
    font-size: 14px;
    margin-bottom: 4px;
}

.select2-results__option small {
    font-size: 11px;
}

/* Animación para nueva fila */
.fila-producto {
    transition: background-color 0.3s ease;
}

@keyframes highlightFade {
    0% { background-color: #d4edda; }
    100% { background-color: transparent; }
}

/* Mejoras en la tabla de productos */
#tabla-productos input[type="number"] {
    text-align: center;
}

#tabla-productos .input-group-sm .input-group-text {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* ── Tabla productos ── */
#tabla-productos td { vertical-align: middle !important; padding: 5px 6px !important; }
#tabla-productos input { font-size: 12px; }
#tabla-productos input[type=number]::-webkit-inner-spin-button,
#tabla-productos input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
#tabla-productos input[type=number] { -moz-appearance: textfield; appearance: textfield; }

/* ── Badges ── */
.badge-success   { background-color: #28a745; color: white; }
.badge-danger    { background-color: #dc3545; color: white; }
.badge-warning   { background-color: #ffc107; color: #212529; }
.badge-primary   { background-color: #007bff; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-info      { background-color: #17a2b8; color: white; }

/* ── Filtros ── */
.filtros-ordenes {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.modal-lg-custom { max-width: 920px; }

/* ================================================
   SISTEMA DE MODALES PROPIO
================================================ */
.modal-custom-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9000;
    overflow-y: auto;
    padding: 30px 15px;
}
.modal-custom-overlay.activo {
    display: flex;
    align-items: flex-start;
    justify-content: center;
}
.modal-custom-overlay .modal-dialog {
    width: 100%;
    max-width: 920px;
    margin: auto;
    position: relative;
}
.modal-custom-overlay.modal-sm .modal-dialog { max-width: 500px; }

/* Desborde visible del Select2 dentro del modal */
#overlayCrearOrden .modal-content { overflow: visible !important; }

/* ── Select2 dentro del overlay principal ── */
#overlayCrearOrden .select2-container--default .select2-selection--single { height: 38px !important; position: relative !important; }
#overlayCrearOrden .select2-container--default .select2-selection__rendered {
    line-height: 36px !important; padding-right: 50px !important;
    overflow: hidden !important; text-overflow: ellipsis !important; white-space: nowrap !important;
}
#overlayCrearOrden .select2-container--default .select2-selection__clear {
    position: absolute !important; right: 30px !important; top: 50% !important;
    transform: translateY(-50%) !important; font-size: 20px !important;
    font-weight: bold !important; color: #dc3545 !important;
    cursor: pointer !important; z-index: 1000 !important;
    background: transparent !important; border: none !important;
}
#overlayCrearOrden .select2-container--default .select2-selection__arrow {
    position: absolute !important; right: 8px !important; top: 50% !important;
    transform: translateY(-50%) !important; height: auto !important; width: 20px !important;
}

/* SweetAlert sobre modales */
.swal-sobre-modal { z-index: 99999 !important; }

/* Botones de acción */
.btn-group .btn { margin: 0 2px; border-radius: 4px !important; }

/* Asegurar que SweetAlert esté por encima de los modales */
.swal2-container {
    z-index: 99999 !important;
}

.swal2-popup {
    z-index: 99999 !important;
}

@media print {
    .modal-custom-overlay {
        position: relative !important;
        display: block !important;
        background: white !important;
        padding: 0 !important;
    }
    .modal-custom-overlay .modal-dialog {
        max-width: 100% !important;
        margin: 0 !important;
    }
    .modal-custom-overlay .modal-content {
        border: none !important;
        box-shadow: none !important;
    }
    .modal-custom-overlay .modal-header,
    .modal-custom-overlay .modal-footer {
        display: none !important;
    }
    .modal-custom-overlay .modal-body {
        padding: 0 !important;
    }
    #contenidoVistaPrevia {
        margin: 0 !important;
        padding: 20px !important;
    }
    .btn-imprimir, .btn-descargar {
        display: none !important;
    }
}

</style>

<br>

<!-- ================================================
     TARJETA PRINCIPAL
================================================ -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart"></i> Gestión de Órdenes de Compra
        </h5>
        <button type="button" id="btnNuevaOrden" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Orden de Compra
        </button>
    </div>

    <div class="card-body">

        <!-- Filtros -->
        <div class="filtros-ordenes">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="filtro_estado">
                            <option value="">Todos</option>
                            <option value="borrador">Borrador</option>
                            <option value="enviada">Enviada</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="recibida_parcial">Rec. Parcial</option>
                            <option value="recibida">Recibida</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha desde</label>
                        <input type="date" class="form-control" id="filtro_fecha_desde">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha hasta</label>
                        <input type="date" class="form-control" id="filtro_fecha_hasta">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Proveedor</label>
                        <input type="text" class="form-control" id="filtro_proveedor"
                               placeholder="Buscar por proveedor">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-primary btn-sm" id="btnFiltrar">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <button class="btn btn-secondary btn-sm" id="btnLimpiarFiltros">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-hover" id="tablaOrdenes" style="width:100%; font-size:12.5px;">
                <thead>
                    <tr>
                        <th>N° Orden</th>
                        <th>Fecha</th>
                        <th>Entrega esperada</th>
                        <th>Proveedor</th>
                        <th>Responsable</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Total general:</th>
                        <th id="totalGeneral">$0</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>


<!-- ================================================
     MODAL CREAR / EDITAR ORDEN
================================================ -->
<div class="modal-custom-overlay" id="overlayCrearOrden">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="tituloModalOrden">
                    <i class="fas fa-shopping-cart"></i> Nueva Orden de Compra
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCrearOrden">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formOrden">
                @csrf
                <div class="modal-body">

                    <!-- Número, fecha orden, fecha entrega, método pago -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>N° Orden <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_orden"
                                       name="numero_orden"
                                       value="{{ $numero_orden ?? 'OC-'.date('Ymd').'-00001' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha de orden <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_orden"
                                       name="fecha_orden" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha entrega esperada</label>
                                <input type="date" class="form-control" id="fecha_entrega_esperada"
                                       name="fecha_entrega_esperada" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Método de pago</label>
                                <select class="form-control" id="metodo_pago" name="metodo_pago">
                                    <option value="">Seleccione...</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="credito_30">Crédito 30 días</option>
                                    <option value="credito_60">Crédito 60 días</option>
                                    <option value="credito_90">Crédito 90 días</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Proveedor -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-truck"></i> Información del Proveedor
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Buscar proveedor registrado</label>
                                <select class="form-control" id="id_proveedor" name="id_proveedor">
                                    <option value=""></option>
                                </select>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre / Razón social <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="proveedor_nombre"
                                               name="proveedor_nombre"
                                               placeholder="Nombre del proveedor" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NIT / Cédula</label>
                                        <input type="text" class="form-control" id="proveedor_nit"
                                               name="proveedor_nit" placeholder="NIT o cédula">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nombre de contacto</label>
                                        <input type="text" class="form-control" id="proveedor_contacto"
                                            name="proveedor_contacto" placeholder="Persona de contacto">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" id="proveedor_telefono"
                                            name="proveedor_telefono" placeholder="Teléfono">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="proveedor_email"
                                            name="proveedor_email" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Dirección</label>
                                        <input type="text" class="form-control" id="proveedor_direccion"
                                               name="proveedor_direccion" placeholder="Dirección">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-boxes"></i> Productos a ordenar
                            </h6>
                        </div>
                        <div class="card-body p-2">

                            <!-- Buscador de productos -->
                            <div class="row mb-3">
                                <div class="col-md-12" style="position:relative;">
                                    <select class="form-control" id="select-buscar-producto">
                                        <option value=""></option>
                                    </select>
                                    <button type="button" id="btn-limpiar-producto"
                                            title="Limpiar búsqueda"
                                            style="display:none; position:absolute; right:30px; top:50%;
                                                   transform:translateY(-50%); z-index:9999;
                                                   background:transparent; border:none;
                                                   color:#dc3545; font-size:18px; font-weight:bold;
                                                   cursor:pointer; padding:0 6px; line-height:1;">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <!-- Mensaje vacío -->
                            <div class="text-center text-muted py-3" id="sin-productos-msg">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">No hay productos. Busque y seleccione un producto arriba.</p>
                            </div>

                            <!-- Tabla de productos seleccionados -->
                            <div id="tabla-productos-container" style="display:none;">
                                <table class="table table-sm table-bordered mb-0" id="tabla-productos">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:35%">Producto</th>
                                            <th style="width:10%" class="text-center">Cantidad</th>
                                            <th style="width:20%">Precio Unit.</th>
                                            <th style="width:18%">Descuento</th>
                                            <th style="width:12%" class="text-right">Total línea</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-productos"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Totales -->
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Los valores se actualizan automáticamente.
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-right"><strong>Subtotal:</strong></td>
                                            <td class="text-right" id="subtotal" style="min-width:120px; font-size:14px;">$0</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong>Descuento:</strong></td>
                                            <td class="text-right text-danger" id="descuento_total" style="font-size:14px;">$0</td>
                                        </tr>
                                        <tr id="fila_iva">
                                            <td class="text-right">
                                                <select class="form-control form-control-sm d-inline-block"
                                                        id="tipo_iva" name="tipo_iva"
                                                        style="width:auto; font-size:12px; padding:2px 6px; height:28px; vertical-align:middle;">
                                                    <option value="0">Seleccione IVA</option>
                                                    <option value="0">0% — Exento</option>
                                                    <option value="5">5%</option>
                                                    <option value="16">16%</option>
                                                    <option value="19" selected>19%</option>
                                                    <option value="-1">No aplica</option>
                                                </select>
                                            </td>
                                            <td class="text-right text-info" id="iva_total" style="font-size:14px;">$0</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-right"><strong style="font-size:16px;">TOTAL:</strong></td>
                                            <td class="text-right text-primary" id="total" style="font-size:16px; font-weight:bold;">$0</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones y términos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="observaciones"
                                          name="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Términos y condiciones</label>
                                <textarea class="form-control" id="terminos_condiciones"
                                          name="terminos_condiciones" rows="3">Orden válida por 30 días. Precios sujetos a confirmación del proveedor.</textarea>
                            </div>
                        </div>
                    </div>

                </div>{{-- /modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cerrar-modal"
                            data-overlay="overlayCrearOrden">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarOrden">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_guardar"></span>
                        <span id="texto_btn_guardar">Guardar Orden</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL VER ORDEN
================================================ -->
<div class="modal-custom-overlay" id="overlayVerOrden">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    Detalle de Orden:
                    <span id="ver_numero_orden" style="color:#007bff;"></span>
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerOrden">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row mb-3">
                    <!-- Datos de la orden -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Información de la Orden</h6>
                            </div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="45%"><strong>Número:</strong></td>        <td><span id="ver_numero"></span></td></tr>
                                    <tr><td><strong>Fecha orden:</strong></td>                <td><span id="ver_fecha_orden"></span></td></tr>
                                    <tr><td><strong>Entrega esperada:</strong></td>           <td><span id="ver_fecha_entrega"></span></td></tr>
                                    <tr><td><strong>Método de pago:</strong></td>             <td><span id="ver_metodo_pago"></span></td></tr>
                                    <tr><td><strong>Estado:</strong></td>                     <td><span id="ver_estado"></span></td></tr>
                                    <tr><td><strong>Responsable:</strong></td>                <td><span id="ver_responsable"></span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Datos del proveedor -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Información del Proveedor</h6>
                            </div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="40%"><strong>Nombre:</strong></td>     <td><span id="ver_proveedor_nombre"></span></td></tr>
                                    <tr><td><strong>NIT:</strong></td>                    <td><span id="ver_proveedor_nit"></span></td></tr>
                                    <tr><td><strong>Teléfono:</strong></td>               <td><span id="ver_proveedor_telefono"></span></td></tr>
                                    <tr><td><strong>Email:</strong></td>                  <td><span id="ver_proveedor_email"></span></td></tr>
                                    <tr><td><strong>Dirección:</strong></td>              <td><span id="ver_proveedor_direccion"></span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">Productos Ordenados</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-center">Rec.</th>
                                        <th class="text-right">P. Unitario</th>
                                        <th class="text-right">Descuento</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_detalle_productos"></tbody>
                                <tfoot>
                                    <tr><th colspan="5"></th><th class="text-right">Subtotal:</th>  <th class="text-right" id="ver_subtotal"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">Descuento:</th> <th class="text-right" id="ver_descuento"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">IVA:</th>       <th class="text-right" id="ver_iva"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">TOTAL:</th>     <th class="text-right" id="ver_total"></th></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div id="ver_observaciones_container" style="display:none;">
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">Observaciones</h6>
                        </div>
                        <div class="card-body py-2">
                            <p id="ver_observaciones" class="mb-0"></p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerOrden">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnPdfDesdeVer" data-id="">
                    <i class="fas fa-file-pdf"></i> Ver PDF
                </button>
                <button type="button" class="btn btn-primary" id="btnCambiarEstado" data-id="">
                    <i class="fas fa-exchange-alt"></i> Cambiar Estado
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL CAMBIAR ESTADO
================================================ -->
<div class="modal-custom-overlay modal-sm" id="overlayCambiarEstado">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Cambiar Estado de Orden</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCambiarEstado">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cambiar_estado_id">
                <div class="form-group">
                    <label>Nuevo estado:</label>
                    <select class="form-control" id="nuevo_estado">
                        <option value="borrador">Borrador</option>
                        <option value="enviada">Enviada</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="recibida_parcial">Recibida Parcialmente</option>
                        <option value="recibida">Recibida completa</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayCambiarEstado">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCambioEstado">
                    Actualizar Estado
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL VER PDF
================================================ -->
<!-- MODAL VISTA PREVIA ORDEN DE COMPRA -->
<div class="modal-custom-overlay" id="overlayVerPDF">
    <div class="modal-dialog" style="max-width: 90%; max-height: 90vh;">
        <div class="modal-content" style="height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice text-danger"></i> Vista Previa — Orden de Compra
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerPDF">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="flex: 1; overflow-y: auto;">
                <div id="contenidoVistaPrevia" style="max-width: 900px; margin: 0 auto; padding: 30px 40px; background: white;">
                    <!-- Aquí se cargará el HTML de la orden -->
                    <div style="text-align: center; padding: 50px;">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3">Cargando orden de compra...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerPDF">Cerrar</button>
                <button type="button" id="btnGuardarPDF" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar PDF
                </button>
                <button type="button" id="btnImprimirVista" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var productoIndex = 0;
    var productosActuales = new Map(); // Para rastrear productos y evitar duplicados
    var APP_URL = "{{ url('/') }}";

    var APP_URL = "{{ url('/') }}";
    var ROUTE_VISTA_PREVIA = "{{ url('/ordenes-compra') }}/{id}/vista-previa";
    var ROUTE_PDF          = "{{ url('/ordenes-compra') }}/{id}/pdf";

    // ================================================
    // SISTEMA DE MODALES PROPIO
    // ================================================
    function abrirOverlay(id) {
        $('#' + id).addClass('activo');
        $('body').addClass('modal-open');
    }

    function cerrarOverlay(id) {
        $('#' + id).removeClass('activo');
        setTimeout(function() {
            if ($('.modal-custom-overlay.activo').length === 0) {
                $('body').removeClass('modal-open');
            }
        }, 100);
    }

    $(document).on('click', '.btn-cerrar-modal', function () {
        var overlay = $(this).data('overlay');
        if (overlay === 'overlayCrearOrden') {
            productosActuales.clear();  // <-- agrega esta línea
        }
        cerrarOverlay(overlay);
    });

    $(document).on('click', '.modal-custom-overlay', function (e) {
        if ($(e.target).hasClass('modal-custom-overlay')) {
            cerrarOverlay($(this).attr('id'));
        }
    });

    // ================================================
    // DATATABLE
    // ================================================
    var tablaOrdenes = $('#tablaOrdenes').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('ordenes-compra.data') }}",
            type: 'GET',
            data: function (d) {
                d.estado      = $('#filtro_estado').val();
                d.fecha_desde = $('#filtro_fecha_desde').val();
                d.fecha_hasta = $('#filtro_fecha_hasta').val();
                d.proveedor   = $('#filtro_proveedor').val();
            },
            error: function(xhr, error, thrown) {
                console.log('Error en DataTable:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    thrown: thrown
                });
                
                let errorMsg = 'Error al cargar los datos';
                if (xhr.status === 500) {
                    errorMsg = 'Error del servidor. Revisa la consola para más detalles.';
                } else if (xhr.status === 404) {
                    errorMsg = 'La ruta no existe. Verifica la configuración de rutas.';
                }
                
                $('#tablaOrdenes tbody').html('<tr><td colspan="8" class="text-center text-danger">' + errorMsg + '</td></tr>');
            }
        },
        columns: [
            { data: 'numero_orden', defaultContent: '—' },
            { data: 'fecha_orden_fmt', defaultContent: '—' },
            { data: 'fecha_entrega_fmt', defaultContent: '—' },
            { data: 'proveedor_display', defaultContent: '—' },
            { data: 'responsable', defaultContent: '—' },
            { data: 'total_fmt', defaultContent: '$0' },
            { data: 'estado_badge', defaultContent: '—', orderable: false },
            { data: 'acciones', defaultContent: '—', orderable: false, searchable: false }
        ],
        language: {
            emptyTable:    "No hay órdenes de compra registradas.",
            info:          "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty:     "Mostrando 0 a 0 de 0 registros",
            infoFiltered:  "(Filtrado de _MAX_ total registros)",
            lengthMenu:    "Mostrar _MENU_ registros",
            loadingRecords:"Cargando...",
            processing:    "Procesando...",
            search:        "Buscar:",
            zeroRecords:   "Sin resultados encontrados",
            paginate: { 
                first: "Primero", 
                last: "Último", 
                next: "Siguiente", 
                previous: "Anterior" 
            }
        },
        footerCallback: function (row, data) {
            var totalPagina = 0;
            if (data && data.length) {
                data.forEach(function (fila) {
                    var raw = fila.total_raw !== undefined
                        ? parseFloat(fila.total_raw)
                        : parseFloat(String(fila.total_fmt || '0').replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
                    totalPagina += raw;
                });
            }
            $('#totalGeneral').text('$' + totalPagina.toLocaleString('es-CO'));
        }
    });

    // ================================================
    // FILTROS
    // ================================================
    $('#filtro_estado, #filtro_fecha_desde, #filtro_fecha_hasta').on('change', function () {
        tablaOrdenes.ajax.reload();
    });

    $('#filtro_proveedor').on('keyup', function () {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function () { tablaOrdenes.ajax.reload(); }, 500);
    });

    $('#btnFiltrar').on('click', function () { tablaOrdenes.ajax.reload(); });

    $('#btnLimpiarFiltros').on('click', function () {
        $('#filtro_estado').val('');
        $('#filtro_fecha_desde, #filtro_fecha_hasta, #filtro_proveedor').val('');
        tablaOrdenes.ajax.reload();
    });

    // ================================================
    // SELECT2 HELPERS
    // ================================================
    function destroySelect2(selector) {
        var $el = $(selector);
        if ($el.length && $el.hasClass('select2-hidden-accessible')) {
            try { 
                $el.select2('destroy'); 
            } catch (e) {
                console.log('Error destroying select2:', e);
            }
        }
    }

    $(document).on('select2:open', function () {
        setTimeout(function () {
            $('body').find('.select2-container--open .select2-search--dropdown .select2-search__field')
                     .get(0)?.focus();
        }, 200);
    });

    // ================================================
    // SELECT2 PROVEEDOR
    // ================================================
    function initSelect2Proveedor() {
        destroySelect2('#id_proveedor');
        $('#id_proveedor').empty().append('<option value=""></option>');

        $('#id_proveedor').select2({
            placeholder: '🔍 Buscar proveedor por razón social, NIT o nombre de contacto...',
            allowClear: true,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearOrden .modal-content'),
            width: '100%',
            language: {
                inputTooShort: function () { return 'Ingrese al menos 1 caracter'; },
                searching:     function () { return 'Buscando...'; },
                noResults:     function () { return 'No se encontraron proveedores'; }
            },
            ajax: {
                url: APP_URL + '/buscar-proveedores',
                type: 'GET',
                dataType: 'json',
                delay: 400,
                data: function (params) { 
                    return { q: params.term }; 
                },
                processResults: function (data) { 
                    return { results: data.results || [] }; 
                },
                cache: false
            },
            templateResult: function(item) {
                if (item.loading) return 'Buscando...';
                if (!item.razon_social && !item.nombre) return item.text;
                
                var html = '<div class="p-2">' +
                    '<strong class="d-block">' + (item.razon_social || item.nombre) + '</strong>';
                
                if (item.nombre_contacto) {
                    html += '<small class="text-info">👤 Contacto: ' + item.nombre_contacto + '</small><br>';
                }
                
                html += '<small class="text-muted">' + 
                    (item.nit ? 'NIT: ' + item.nit : '') + 
                    (item.telefono ? ' | Tel: ' + item.telefono : '') + 
                    '</small>' +
                    '</div>';
                
                return $(html);
            },
            templateSelection: function(item) {
                if (item.razon_social) {
                    return item.razon_social + (item.nombre_contacto ? ' (' + item.nombre_contacto + ')' : '');
                }
                return item.text || item.id;
            }
        });

        $('#id_proveedor').on('select2:select', function (e) {
            var d = e.params.data;
            
            $('#proveedor_nombre').val(d.razon_social || d.nombre || d.text);
            $('#proveedor_nit').val(d.nit || '');
            $('#proveedor_contacto').val(d.nombre_contacto || '');
            $('#proveedor_telefono').val(d.telefono || '');
            $('#proveedor_email').val(d.email || '');
            $('#proveedor_direccion').val(d.direccion || '');
        });

        $('#id_proveedor').on('select2:clear', function () {
            $('#proveedor_nombre, #proveedor_nit, #proveedor_contacto, #proveedor_telefono, #proveedor_email, #proveedor_direccion').val('');
        });
    }

    // ================================================
    // SELECT2 PRODUCTOS (VERSIÓN CORREGIDA)
    // ================================================
    function initSelect2Producto() {
        destroySelect2('#select-buscar-producto');
        $('#select-buscar-producto').empty().append('<option value=""></option>');
        $('#btn-limpiar-producto').hide();

        $('#select-buscar-producto').select2({
            width: '100%',
            placeholder: '🔍 Buscar producto por nombre, código o referencia...',
            allowClear: true,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearOrden .modal-content'),
            language: {
                inputTooShort: function () { return 'Ingrese al menos 1 caracter'; },
                searching:     function () { return 'Buscando productos...'; },
                noResults:     function () { return 'No se encontraron productos'; }
            },
            ajax: {
                url: APP_URL + '/buscar-productos-orden-compra',
                type: 'GET',
                dataType: 'json',
                delay: 500,
                data: function (params) { 
                    return { q: params.term }; 
                },
                processResults: function (data) { 
                    console.log('Productos recibidos:', data);
                    return { results: data.results || [] }; 
                },
                cache: true
            },
            templateResult: function(item) {
                if (item.loading) return 'Buscando...';
                if (!item.id) return item.text || 'Sin resultados';
                
                var nombreProducto = item.nombre || item.text || 'Producto sin nombre';
                var codigoProducto = item.codigo ? '[' + item.codigo + '] ' : '';
                var precioFormateado = new Intl.NumberFormat('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 0
                }).format(item.precio || 0);
                
                return $('<div class="p-2">' +
                    '<strong class="d-block">' + codigoProducto + nombreProducto + '</strong>' +
                    '<div class="d-flex justify-content-between mt-1">' +
                    '<small class="text-primary font-weight-bold">' + precioFormateado + '</small>' +
                    '<small class="text-muted">📦 Stock: ' + (item.stock || 0) + ' ' + (item.unidad_medida || 'und') + '</small>' +
                    '</div>' +
                    '</div>');
            },
            templateSelection: function(item) {
                if (!item.id) return item.text;
                var codigoProducto = item.codigo ? '[' + item.codigo + '] ' : '';
                var nombreProducto = item.nombre || item.text || '';
                return codigoProducto + nombreProducto;
            }
        });

  $('#select-buscar-producto').off('select2:select').on('select2:select', function (e) {
    var data = e.params.data;

    if (!data || !data.id) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo obtener la información del producto', timer: 2000, showConfirmButton: false });
        $(this).val(null).trigger('change');
        return;
    }

        // Verificar duplicado buscando directamente en el DOM (igual que remisiones)
        var yaExiste = false;
        $('#tbody-productos .fila-producto').each(function() {
            if ($(this).find('input[name*="[id_producto]"]').val() == data.id) {
                yaExiste = true;
                return false;
            }
        });

        $(this).val(null).trigger('change');
        $('#btn-limpiar-producto').hide();

        if (yaExiste) {
            Swal.fire({
                icon: 'warning',
                title: 'Producto duplicado',
                text: 'El producto "' + (data.codigo ? '[' + data.codigo + '] ' : '') + (data.nombre || data.text) + '" ya está en la lista.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        agregarProductoATabla(data);
    });
        
        $('#select-buscar-producto').on('select2:open', function() {
            setTimeout(function() {
                var searchField = $('.select2-search__field');
                if (searchField.length) {
                    searchField.off('input').on('input', function() {
                        if ($(this).val().length > 0) {
                            $('#btn-limpiar-producto').show();
                        } else {
                            $('#btn-limpiar-producto').hide();
                        }
                    });
                }
            }, 100);
        });
        
        $('#btn-limpiar-producto').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#select-buscar-producto').val(null).trigger('change');
            $(this).hide();
        });
    }

    // ================================================
    // FUNCIONES DE TABLA DE PRODUCTOS
    // ================================================
    function agregarProductoATabla(data) {
        $('#sin-productos-msg').hide();
        $('#tabla-productos-container').show();

        var idx = productoIndex++;
        var precio = parseFloat(data.precio) || 0;
        var stock = parseInt(data.stock) || 0;
        var codigo = data.codigo || '';
        var unidad = data.unidad_medida || 'und';
        var nombreProducto = data.nombre || data.text || 'Producto';

        var fila = `
            <tr class="fila-producto" data-index="${idx}" data-id="${data.id}">
                <td>
                    <div class="d-flex flex-column">
                        <span class="font-weight-bold" style="font-size:13px;">
                            ${codigo ? '<span class="text-muted">[' + codigo + ']</span> ' : ''}
                            ${nombreProducto}
                        </span>
                        <small class="text-success">
                            <i class="fas fa-cubes"></i> Stock: ${stock} unidades
                        </small>
                    </div>
                    <input type="hidden" name="productos[${idx}][id_producto]" value="${data.id}">
                    <input type="hidden" name="productos[${idx}][nombre_producto]" value="${nombreProducto}">
                    <input type="hidden" name="productos[${idx}][codigo_producto]" value="${codigo}">
                    <input type="hidden" name="productos[${idx}][unidad_medida]" value="${unidad}">
                </td>
                <td class="text-center" style="width: 100px;">
                    <input type="number" class="form-control form-control-sm cantidad text-center"
                           name="productos[${idx}][cantidad]"
                           step="1" min="1" value="1" required
                           ${stock > 0 ? 'max="' + stock + '"' : ''}
                           style="width: 80px; margin: 0 auto;">
                </td>
                <td style="width: 150px;">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" class="form-control precio-unitario"
                               name="productos[${idx}][precio_unitario]"
                               step="0.01" min="0" value="${precio}" required
                               style="text-align: right;">
                    </div>
                </td>
                <td style="width: 150px;">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" class="form-control descuento"
                               name="productos[${idx}][descuento]"
                               step="0.01" min="0" value="0"
                               style="text-align: right;">
                    </div>
                </td>
                <td class="text-right" style="width: 120px;">
                    <strong class="text-primary item-total" style="font-size:14px;">
                        ${new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(precio)}
                    </strong>
                </td>
                <td class="text-center" style="width: 50px;">
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" 
                            title="Quitar producto">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;

        $('#tbody-productos').append(fila);
        
        // Guardar en el Map para detectar duplicados
       
        productosActuales.set(data.id.toString(), true);
        
        var $nuevaFila = $('#tbody-productos .fila-producto').last();
        $nuevaFila.find('.cantidad').focus().select();

               
        calcularTotales();
    }

    $(document).on('click', '.btn-eliminar-fila', function () {
        var $fila = $(this).closest('.fila-producto');
        var productoId = $fila.data('id');

          if (productoId) productosActuales.delete(productoId.toString());
        
        if (productoId) {
            productosActuales.delete(productoId.toString());
        }
        
        $fila.remove();
        
        if ($('#tbody-productos .fila-producto').length === 0) {
            $('#sin-productos-msg').show();
            $('#tabla-productos-container').hide();
        }
        calcularTotales();
    });

    $(document).on('input', '#tbody-productos .cantidad, #tbody-productos .precio-unitario, #tbody-productos .descuento', function () {
        calcularFila($(this).closest('.fila-producto'));
    });

    function calcularFila($fila) {
        var cant = parseFloat($fila.find('.cantidad').val()) || 0;
        var prec = parseFloat($fila.find('.precio-unitario').val()) || 0;
        var desc = parseFloat($fila.find('.descuento').val()) || 0;
        $fila.find('.item-total').text('$' + ((cant * prec) - desc).toLocaleString('es-CO'));
        calcularTotales();
    }

    function calcularTotales() {
        var subtotal = 0, descTotal = 0;
        $('#tbody-productos .fila-producto').each(function () {
            subtotal += (parseFloat($(this).find('.cantidad').val()) || 0) *
                        (parseFloat($(this).find('.precio-unitario').val()) || 0);
            descTotal += (parseFloat($(this).find('.descuento').val()) || 0);
        });

        var tipoIva = parseInt($('#tipo_iva').val());
        var base = subtotal - descTotal;
        var iva = 0;

        if (tipoIva === -1) {
            $('#fila_iva').hide();
        } else {
            $('#fila_iva').show();
            iva = tipoIva > 0 ? base * (tipoIva / 100) : 0;
        }

        var total = base + iva;
        $('#subtotal').text('$' + subtotal.toLocaleString('es-CO'));
        $('#descuento_total').text('$' + descTotal.toLocaleString('es-CO'));
        $('#iva_total').text('$' + iva.toLocaleString('es-CO'));
        $('#total').text('$' + total.toLocaleString('es-CO'));
    }

    $(document).on('change', '#tipo_iva', calcularTotales);

    // ================================================
    // NUEVA ORDEN
    // ================================================
    $('#btnNuevaOrden').off('click').on('click', function () {
        resetFormulario();
        cargarNumeroOrden();
        abrirOverlay('overlayCrearOrden');
        setTimeout(function () {
            initSelect2Proveedor();
            initSelect2Producto();
        }, 100);
    });

    function cargarNumeroOrden() {
        console.log('Cargando número de orden...');
        $.ajax({
            url: APP_URL + '/ordenes-compra/numero-siguiente',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta número de orden:', response);
                if (response && response.numero) {
                    $('#numero_orden').val(response.numero);
                } else if (response && response.success === false) {
                    console.warn('Error en respuesta:', response.error);
                    $('#numero_orden').val('OC-' + new Date().toISOString().slice(0,10).replace(/-/g, '') + '-00001');
                } else {
                    $('#numero_orden').val('OC-' + new Date().toISOString().slice(0,10).replace(/-/g, '') + '-00001');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar número de orden:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                var fecha = new Date();
                var fechaStr = fecha.getFullYear() + 
                              ('0' + (fecha.getMonth() + 1)).slice(-2) + 
                              ('0' + fecha.getDate()).slice(-2);
                $('#numero_orden').val('OC-' + fechaStr + '-00001');
                console.warn('Usando número de orden por defecto');
            }
        });
    }

    function resetFormulario() {
        $('#formOrden')[0].reset();
        $('#fecha_orden').val("{{ date('Y-m-d') }}");
        $('#sin-productos-msg').show();
        $('#tabla-productos-container').hide();
        $('#tbody-productos').empty();
       
        productoIndex = 0;
        calcularTotales();
        destroySelect2('#id_proveedor');
        destroySelect2('#select-buscar-producto');
        $('#formOrden').removeData('modo').removeData('id');
        $('#tituloModalOrden').html('<i class="fas fa-shopping-cart"></i> Nueva Orden de Compra');
        $('#texto_btn_guardar').text('Guardar Orden');
    }

    // ================================================
    // GUARDAR ORDEN
    // ================================================
    $('#formOrden').off('submit').on('submit', function (e) {
        e.preventDefault();

        if ($('#tbody-productos .fila-producto').length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un producto', 'warning');
            return;
        }

        var modo = $(this).data('modo') || 'crear';
        var id = $(this).data('id') || null;
        var url = (modo === 'editar' && id)
            ? APP_URL + '/ordenes-compra/' + id
            : "{{ route('ordenes-compra.store') }}";
        var data = $(this).serialize();
        if (modo === 'editar' && id) data += '&_method=PUT';

        $('#spinner_guardar').removeClass('d-none');
        $('#texto_btn_guardar').text('Guardando...');
        $('#btnGuardarOrden').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.success) {
                    cerrarOverlay('overlayCrearOrden');
                    Swal.fire({
                        icon: 'success',
                        title: modo === 'editar' ? 'Orden actualizada' : 'Orden creada',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    tablaOrdenes.ajax.reload();
                    resetFormulario();
                }
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                var msg = 'Error al guardar la orden';
                if (xhr.status === 422) {
                    var errors = [];
                    $.each(xhr.responseJSON.errors, function (k, v) {
                        errors.push(v[0]);
                    });
                    msg = errors.join('<br>');
                } else if (xhr.status === 500) {
                    msg = xhr.responseJSON?.message || 'Error interno del servidor';
                }
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error', 
                    html: msg
                });
            },
            complete: function () {
                $('#spinner_guardar').addClass('d-none');
                $('#texto_btn_guardar').text(
                    $('#formOrden').data('modo') === 'editar' ? 'Actualizar Orden' : 'Guardar Orden'
                );
                $('#btnGuardarOrden').prop('disabled', false);
            }
        });
    });

    // ================================================
    // VER ORDEN
    // ================================================
    $(document).on('click', '.btn-ver', function () {
        var id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/ordenes-compra/' + id,
            type: 'GET',
            success: function (o) {
                $('#ver_numero_orden').text(o.numero_orden);
                $('#ver_numero').text(o.numero_orden);
                $('#ver_fecha_orden').text(o.fecha_orden ? formatFecha(o.fecha_orden) : '—');
                $('#ver_fecha_entrega').text(o.fecha_entrega_esperada ? formatFecha(o.fecha_entrega_esperada) : '—');
                $('#ver_metodo_pago').text(formatMetodoPago(o.metodo_pago));
                
                var estadoTexto = getEstadoTexto(o.estado);
                var estadoColor = getEstadoColor(o.estado);
                $('#ver_estado').html('<span class="badge badge-' + estadoColor + '">' + estadoTexto + '</span>');
                $('#ver_responsable').text(o.usuario ? o.usuario.name : 'N/A');

                $('#ver_proveedor_nombre').text(o.proveedor_nombre || '—');
                $('#ver_proveedor_nit').text(o.proveedor_nit || '—');
                $('#ver_proveedor_telefono').text(o.proveedor_telefono || '—');
                $('#ver_proveedor_email').text(o.proveedor_email || '—');
                $('#ver_proveedor_direccion').text(o.proveedor_direccion || '—');

                var filas = '';
                var sub = 0, desc = 0;
                if (o.detalles && o.detalles.length) {
                    o.detalles.forEach(function (d) {
                        sub += d.cantidad * d.precio_unitario;
                        desc += parseFloat(d.descuento) || 0;
                        filas += '<tr>' +
                            '<td>' + (d.codigo_producto || '—') + '</td>' +
                            '<td>' + (d.nombre_producto || '—') + '</td>' +
                            '<td class="text-center">' + parseInt(d.cantidad) + '</td>' +
                            '<td class="text-right">$' + parseFloat(d.precio_unitario).toLocaleString('es-CO') + '</td>' +
                            '<td class="text-right">$' + parseFloat(d.descuento || 0).toLocaleString('es-CO') + '</td>' +
                            '<td class="text-right text-primary"><strong>$' + parseFloat(d.total_linea).toLocaleString('es-CO') + '</strong></td>' +
                            '</tr>';
                    });
                } else {
                    filas = '<tr><td colspan="6" class="text-center text-muted">Sin productos</td></tr>';
                }
                $('#ver_detalle_productos').html(filas);

                var iva = parseFloat(o.impuesto_valor) || 0;
                $('#ver_subtotal').text('$' + sub.toLocaleString('es-CO'));
                $('#ver_descuento').text('$' + desc.toLocaleString('es-CO'));
                $('#ver_iva').text('$' + iva.toLocaleString('es-CO'));
                $('#ver_total').text('$' + parseFloat(o.total).toLocaleString('es-CO'));

                if (o.observaciones) {
                    $('#ver_observaciones').text(o.observaciones);
                    $('#ver_observaciones_container').show();
                } else {
                    $('#ver_observaciones_container').hide();
                }

                $('#btnPdfDesdeVer').data('id', id);
                $('#btnCambiarEstado').data('id', id);

                abrirOverlay('overlayVerOrden');
            },
            error: function () {
                Swal.fire('Error', 'No se pudo cargar la orden', 'error');
            }
        });
    });

    // ================================================
    // EDITAR ORDEN
    // ================================================
    $(document).on('click', '.btn-editar', function () {
        var id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/ordenes-compra/' + id,
            type: 'GET',
            success: function (o) {
                resetFormulario();
                setTimeout(function() {
                    initSelect2Proveedor();
                    initSelect2Producto();
                }, 100);

                $('#tituloModalOrden').html('<i class="fas fa-edit"></i> Editar Orden de Compra');
                $('#formOrden').data('modo', 'editar').data('id', id);
                $('#texto_btn_guardar').text('Actualizar Orden');

                $('#numero_orden').val(o.numero_orden);
                $('#fecha_orden').val(o.fecha_orden ? o.fecha_orden.substring(0, 10) : '');
                $('#fecha_entrega_esperada').val(o.fecha_entrega_esperada ? o.fecha_entrega_esperada.substring(0, 10) : '');
                $('#metodo_pago').val(o.metodo_pago || '');
                $('#observaciones').val(o.observaciones || '');
                $('#terminos_condiciones').val(o.terminos_condiciones || '');

                if (o.id_proveedor) {
                    var opt = new Option(o.proveedor_nombre, o.id_proveedor, true, true);
                    $('#id_proveedor').append(opt).trigger('change');
                }
                $('#proveedor_nombre').val(o.proveedor_nombre || '');
                $('#proveedor_nit').val(o.proveedor_nit || '');
                $('#proveedor_telefono').val(o.proveedor_telefono || '');
                $('#proveedor_email').val(o.proveedor_email || '');
                $('#proveedor_direccion').val(o.proveedor_direccion || '');

                if (o.detalles && o.detalles.length) {
                    o.detalles.forEach(function (d) {
                        agregarProductoATabla({
                            id: d.id_producto,
                            text: d.nombre_producto,
                            nombre: d.nombre_producto,
                            precio: d.precio_unitario,
                            stock: d.producto ? d.producto.stock_actual : 0,
                            codigo: d.codigo_producto,
                            unidad_medida: d.unidad_medida
                        });
                        var $last = $('#tbody-productos .fila-producto').last();
                        $last.find('.cantidad').val(parseInt(d.cantidad));
                        $last.find('.descuento').val(d.descuento || 0);
                        calcularFila($last);
                    });
                }

                abrirOverlay('overlayCrearOrden');
            },
            error: function () {
                Swal.fire('Error', 'No se pudo cargar la orden para editar', 'error');
            }
        });
    });

    // ================================================
    // FUNCIONES PARA PDF Y VISTA PREVIA
    // ================================================
    function getEstadoTexto(estado) {
        const estados = {
            'borrador': 'Borrador',
            'enviada': 'Enviada',
            'confirmada': 'Confirmada',
            'recibida_parcial': 'Recibida Parcial',
            'recibida': 'Recibida',
            'cancelada': 'Cancelada'
        };
        return estados[estado] || estado;
    }

    function getEstadoColor(estado) {
        const colores = {
            'borrador': 'secondary',
            'enviada': 'info',
            'confirmada': 'primary',
            'recibida_parcial': 'warning',
            'recibida': 'success',
            'cancelada': 'danger'
        };
        return colores[estado] || 'secondary';
    }

    function mostrarVistaPrevia(id) {
        console.log('Mostrando vista previa para orden ID:', id);
        
        $('#overlayVerPDF').addClass('activo');
        $('body').addClass('modal-open');
        
        $('#contenidoVistaPrevia').html(`
            <div style="text-align: center; padding: 50px;">
                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                <p class="mt-3">Cargando orden de compra...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/ordenes-compra/' + id + '/vista-previa',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.ordenHTML = response.html;
                    window.ordenId = id;
                    $('#contenidoVistaPrevia').html(response.html);
                    
                    $('#btnGuardarPDF').off('click').on('click', function(e) {
                        e.preventDefault();
                        guardarPDF(id);
                    });
                    
                    $('#btnImprimirVista').off('click').on('click', function(e) {
                        e.preventDefault();
                        imprimirVistaPrevia();
                    });
                } else {
                    $('#contenidoVistaPrevia').html(`
                        <div style="text-align: center; padding: 50px; color: red;">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                            <p class="mt-3">Error al cargar la orden: ${response.message}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error al cargar vista previa:', xhr);
                $('#contenidoVistaPrevia').html(`
                    <div style="text-align: center; padding: 50px; color: red;">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                        <p class="mt-3">Error al cargar la orden de compra</p>
                        <p class="text-muted">Por favor, intente nuevamente</p>
                    </div>
                `);
            }
        });
    }

    function guardarPDF(id) {
        console.log('Guardando PDF para orden ID:', id);
        
        $('#btnGuardarPDF').html('<i class="fas fa-spinner fa-spin"></i> Generando PDF...');
        $('#btnGuardarPDF').prop('disabled', true);
        
        var pdfUrl = APP_URL + '/ordenes-compra/' + id + '/pdf';
        var link = document.createElement('a');
        link.href = pdfUrl;
        link.download = 'orden-compra-' + id + '.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setTimeout(function() {
            $('#btnGuardarPDF').html('<i class="fas fa-save"></i> Guardar PDF');
            $('#btnGuardarPDF').prop('disabled', false);
        }, 2000);
    }

    function imprimirVistaPrevia() {
        var contenidoHTML = $('#contenidoVistaPrevia').html();
        var ventanaImpresion = window.open('', '_blank');
        
        ventanaImpresion.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Orden de Compra</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 20px; background: white; }
                    @media print { body { padding: 0; margin: 0; } .no-print { display: none; } }
                </style>
            </head>
            <body>
                ${contenidoHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function() { window.close(); };
                    };
                <\/script>
            </body>
            </html>
        `);
        
        ventanaImpresion.document.close();
    }

    $(document).on('click', '.btn-pdf', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (id) mostrarVistaPrevia(id);
    });

    $('#btnPdfDesdeVer').off('click').on('click', function() {
        var id = $(this).data('id');
        if (id) {
            mostrarVistaPrevia(id);
            setTimeout(function() { cerrarOverlay('overlayVerOrden'); }, 100);
        }
    });

    $(document).on('click', '[data-overlay="overlayVerPDF"]', function() {
        setTimeout(function() { $('#contenidoVistaPrevia').html(''); }, 300);
    });

    // ================================================
    // CAMBIAR ESTADO
    // ================================================
    $('#btnCambiarEstado').on('click', function () {
        $('#cambiar_estado_id').val($(this).data('id'));
        abrirOverlay('overlayCambiarEstado');
    });

    $('#btnGuardarCambioEstado').on('click', function () {
        var id = $('#cambiar_estado_id').val();
        var estado = $('#nuevo_estado').val();
        $.ajax({
            url: APP_URL + '/ordenes-compra/' + id + '/cambiar-estado',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), estado: estado },
            success: function (r) {
                if (r.success) {
                    cerrarOverlay('overlayCambiarEstado');
                    cerrarOverlay('overlayVerOrden');
                    tablaOrdenes.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Estado actualizado', timer: 1200, showConfirmButton: false });
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            },
            error: function () { Swal.fire('Error', 'No se pudo actualizar el estado', 'error'); }
        });
    });

    // ================================================
    // ELIMINAR
    // ================================================
    $(document).on('click', '.btn-eliminar', function () {
        var id = $(this).data('id');
        var numero = $(this).data('numero');
        Swal.fire({
            title: '¿Eliminar orden?',
            html: 'La orden <strong>' + numero + '</strong> será eliminada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + '/ordenes-compra/' + id,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                    success: function (r) {
                        if (r.success) {
                            tablaOrdenes.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Eliminada', text: r.message, timer: 1500, showConfirmButton: false });
                        } else {
                            Swal.fire('No se puede eliminar', r.message, 'warning');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar', 'error');
                    }
                });
            }
        });
    });

    // ================================================
    // UTILIDADES
    // ================================================
    function formatFecha(str) {
        if (!str) return '—';
        var fecha = str.substring(0, 10).split('-');
        return fecha[2] + '/' + fecha[1] + '/' + fecha[0];
    }


    function formatMetodoPago(val) {
        var mapa = {
            efectivo: 'Efectivo', transferencia: 'Transferencia',
            cheque: 'Cheque', credito_30: 'Crédito 30 días',
            credito_60: 'Crédito 60 días', credito_90: 'Crédito 90 días'
        };
        return mapa[val] || val || '—';
    }

});

</script>
@endpush
@endsection