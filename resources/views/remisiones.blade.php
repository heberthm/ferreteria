@extends('layouts.app')
@section('content')

<style>
/* ================================================
   SELECT2
================================================ */
.select2-container { width: 100% !important; }
.select2-container--open, .select2-dropdown { z-index: 99999 !important; }
.select2-selection {
    border: 1px solid #ced4da !important; border-radius: 4px !important;
    min-height: 38px !important; padding: 4px 12px !important;
    font-size: 14px !important; background-color: #fff !important;
}
.select2-selection--single { height: 38px !important; }
.select2-selection__rendered { line-height: 28px !important; padding-left: 0 !important; color: #495057 !important; }
.select2-selection__placeholder { color: #6c757d !important; }
.select2-selection__clear {
    position: absolute !important; right: 25px !important; top: 50% !important;
    transform: translateY(-50%) !important; font-size: 18px !important;
    font-weight: bold !important; color: #dc3545 !important; cursor: pointer !important;
    z-index: 10 !important; background: transparent !important; border: none !important;
}
.select2-selection__arrow {
    position: absolute !important; right: 8px !important; top: 50% !important;
    transform: translateY(-50%) !important; height: auto !important; width: 20px !important;
}
.select2-dropdown {
    border: 1px solid #ced4da !important; border-radius: 4px !important;
    font-size: 14px !important; box-shadow: 0 2px 4px rgba(0,0,0,.1) !important;
}
.select2-results__option { padding: 8px 12px !important; color: #212529 !important; }
.select2-results__option--highlighted { background-color: #e9ecef !important; color: #212529 !important; }
.select2-results__option[aria-selected=true] { background-color: #007bff !important; color: white !important; }

/* ================================================
   BADGES
================================================ */
.badge-success   { background-color: #28a745; color: white; }
.badge-danger    { background-color: #dc3545; color: white; }
.badge-warning   { background-color: #ffc107; color: #212529; }
.badge-primary   { background-color: #007bff; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }

/* ================================================
   FILTROS
================================================ */
.filtros-remisiones {
    background-color: #f8f9fa; padding: 15px;
    border-radius: 5px; margin-bottom: 20px;
}

/* ================================================
   TABLA PRODUCTOS
================================================ */
#tabla-productos td { vertical-align: middle !important; padding: 5px 6px !important; }
#tabla-productos input { font-size: 12px; }
#tabla-productos input[type=number].cantidad::-webkit-inner-spin-button,
#tabla-productos input[type=number].cantidad::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
#tabla-productos input[type=number].cantidad { -moz-appearance: textfield; appearance: textfield; }

/* ================================================
   MODALES PROPIOS
================================================ */
.modal-custom-overlay {
    display: none; position: fixed; top: 0; left: 0;
    width: 100%; height: 100%; background: rgba(0,0,0,0.5);
    z-index: 9000; overflow-y: auto; padding: 30px 15px;
}
.modal-custom-overlay.activo { display: flex; align-items: flex-start; justify-content: center; }
.modal-custom-overlay .modal-dialog { width: 100%; max-width: 900px; margin: auto; position: relative; }
.modal-custom-overlay.modal-sm .modal-dialog { max-width: 500px; }
.modal-lg-custom { max-width: 900px; }
#overlayCrearRemision .modal-content { overflow: visible !important; }

/* SweetAlert sobre modales */
.swal-sobre-modal { z-index: 99999 !important; }
</style>

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-truck"></i> Gestión de Remisiones</h5>
        <button type="button" id="btnNuevaRemision" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Remisión
        </button>
    </div>

    <div class="card-body">
        <!-- Filtros -->
        <div class="filtros-remisiones">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="filtro_estado">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_transito">En Tránsito</option>
                            <option value="entregada">Entregada</option>
                            <option value="parcial">Parcial</option>
                            <option value="anulada">Anulada</option>
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
                        <label>Cliente</label>
                        <input type="text" class="form-control" id="filtro_cliente" placeholder="Buscar por cliente">
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
            <table class="table table-hover" id="tablaRemisiones" style="width:100%; font-size:12.5px;">
                <thead>
                    <tr>
                        <th>N° Remisión</th>
                        <th>Fecha</th>
                        <th>Entrega Est.</th>
                        <th>Cliente</th>
                        <th>Conductor</th>
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
     MODAL CREAR / EDITAR REMISIÓN
================================================ -->
<div class="modal-custom-overlay" id="overlayCrearRemision">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-truck"></i> Nueva Remisión</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCrearRemision">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formRemision">
                @csrf
                <div class="modal-body">

                    <!-- Número, fecha entrega, placa, conductor -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>N° Remisión *</label>
                                <input type="text" class="form-control" id="numero_remision"
                                       name="numero_remision"
                                       value="{{ $numero_remision ?? 'REM-'.date('Ymd').'-00001' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha entrega estimada</label>
                                <input type="date" class="form-control" id="fecha_entrega_estimada"
                                       name="fecha_entrega_estimada" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Conductor</label>
                                <input type="text" class="form-control" id="conductor"
                                       name="conductor" placeholder="Nombre del conductor">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Vehículo / Placa</label>
                                <input type="text" class="form-control" id="vehiculo_placa"
                                       name="vehiculo_placa" placeholder="Ej: ABC-123">
                            </div>
                        </div>
                    </div>

                    <!-- Dirección de entrega -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Dirección de entrega</label>
                                <input type="text" class="form-control" id="direccion_entrega"
                                       name="direccion_entrega" placeholder="Dirección completa de entrega">
                            </div>
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Información del Cliente</h6>
                            <button type="button" class="btn btn-success btn-sm" id="btnAbrirNuevoClienteRem">
                                <i class="fas fa-user-plus"></i> Nuevo Cliente
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Seleccionar cliente existente</label>
                                <select class="form-control" id="id_cliente_rem" name="id_cliente">
                                    <option value=""></option>
                                </select>
                            </div>
                            <hr>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="cliente_general_rem" name="cliente_general">
                                <label class="form-check-label" for="cliente_general_rem">
                                    <strong>Cliente General</strong> (sin registro)
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="rem_cliente_nombre"
                                               name="cliente_nombre" placeholder="Nombre del cliente">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cédula/NIT</label>
                                        <input type="text" class="form-control" id="rem_cliente_cedula"
                                               name="cliente_cedula" placeholder="Cédula o NIT">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" id="rem_cliente_telefono"
                                               name="cliente_telefono" placeholder="Teléfono">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="rem_cliente_email"
                                               name="cliente_email" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">Productos a remitir</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="row mb-3">
                                <div class="col-md-12" style="position:relative;">
                                    <select class="form-control" id="select-buscar-producto-rem">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center text-muted py-3" id="sin-productos-msg-rem">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">No hay productos. Busque y seleccione un producto arriba.</p>
                            </div>

                            <div id="tabla-productos-container-rem" style="display:none;">
                                <table class="table table-sm table-bordered mb-0" id="tabla-productos">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:35%">Producto</th>
                                            <th style="width:12%" class="text-center">Cantidad</th>
                                            <th style="width:18%">Precio Unit.</th>
                                            <th style="width:18%">Descuento</th>
                                            <th style="width:12%" class="text-right">Total</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-productos-rem"></tbody>
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
                                            <td class="text-right" id="rem_subtotal" style="min-width:120px; font-size:14px;">$0</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong>Descuento:</strong></td>
                                            <td class="text-right text-danger" id="rem_descuento_total" style="font-size:14px;">$0</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-right"><strong style="font-size:16px;">TOTAL:</strong></td>
                                            <td class="text-right text-primary" id="rem_total" style="font-size:16px; font-weight:bold;">$0</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="rem_observaciones"
                                          name="observaciones" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cerrar-modal"
                            data-overlay="overlayCrearRemision">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarRemision">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_guardar_rem"></span>
                        <span id="texto_btn_guardar_rem">Guardar Remisión</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL VER REMISIÓN
================================================ -->
<div class="modal-custom-overlay" id="overlayVerRemision">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    Detalle de Remisión:
                    <span id="ver_numero_remision" style="color:#007bff;"></span>
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerRemision">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Información de la Remisión</h6></div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="45%"><strong>Número:</strong></td><td><span id="ver_numero"></span></td></tr>
                                    <tr><td><strong>Fecha:</strong></td><td><span id="ver_fecha"></span></td></tr>
                                    <tr><td><strong>Entrega Est.:</strong></td><td><span id="ver_fecha_entrega"></span></td></tr>
                                    <tr><td><strong>Estado:</strong></td><td><span id="ver_estado"></span></td></tr>
                                    <tr><td><strong>Vendedor:</strong></td><td><span id="ver_vendedor"></span></td></tr>
                                    <tr><td><strong>Conductor:</strong></td><td><span id="ver_conductor"></span></td></tr>
                                    <tr><td><strong>Vehículo:</strong></td><td><span id="ver_vehiculo"></span></td></tr>
                                    <tr><td><strong>Dirección:</strong></td><td><span id="ver_direccion"></span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Información del Cliente</h6></div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="40%"><strong>Nombre:</strong></td><td><span id="ver_cliente_nombre"></span></td></tr>
                                    <tr><td><strong>Cédula/NIT:</strong></td><td><span id="ver_cliente_cedula"></span></td></tr>
                                    <tr><td><strong>Teléfono:</strong></td><td><span id="ver_cliente_telefono"></span></td></tr>
                                    <tr><td><strong>Email:</strong></td><td><span id="ver_cliente_email"></span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><h6 class="mb-0">Productos Remitidos</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Código</th><th>Producto</th>
                                        <th class="text-center">Cant.</th><th>U.M.</th>
                                        <th class="text-right">P.Unitario</th>
                                        <th class="text-right">Descuento</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_detalle_productos_rem"></tbody>
                                <tfoot>
                                    <tr><th colspan="5"></th><th class="text-right">Subtotal:</th><th class="text-right" id="ver_subtotal_rem"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">Descuento:</th><th class="text-right" id="ver_descuento_rem"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">TOTAL:</th><th class="text-right" id="ver_total_rem"></th></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row" id="ver_obs_container" style="display:none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Observaciones</h6></div>
                            <div class="card-body py-2"><p id="ver_observaciones_rem" class="mb-0"></p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerRemision">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnCambiarEstadoRem" data-id="">
                    <i class="fas fa-exchange-alt"></i> Cambiar Estado
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL CAMBIAR ESTADO
================================================ -->
<div class="modal-custom-overlay modal-sm" id="overlayCambiarEstadoRem">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Cambiar Estado de Remisión</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCambiarEstadoRem">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cambiar_estado_id_rem">
                <div class="form-group">
                    <label>Nuevo estado:</label>
                    <select class="form-control" id="nuevo_estado_rem">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_transito">En Tránsito</option>
                        <option value="entregada">Entregada</option>
                        <option value="parcial">Parcial</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayCambiarEstadoRem">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCambioEstadoRem">
                    Actualizar Estado
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL NUEVO CLIENTE
================================================ -->
<div class="modal-custom-overlay modal-sm" id="overlayNuevoClienteRem">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Cliente</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayNuevoClienteRem">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formNuevoClienteRem">
                @csrf
                <input type="hidden" name="id_cliente" id="id_cliente" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" name="nombre" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Cédula/NIT *</label>
                        <input type="text" class="form-control" name="cedula" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" name="telefono" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2" autocomplete="off"></textarea>
                    </div>
                    <input type="hidden" name="userId" value="{{ auth()->id() }}">
                    <input type="hidden" name="estado" value="activo">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cerrar-modal"
                            data-overlay="overlayNuevoClienteRem">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardarClienteRem">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_cliente_rem"></span>
                        <span id="texto_btn_cliente_rem">Guardar Cliente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================================
     MODAL VER PDF (IGUAL QUE EN COTIZACIONES)
================================================ -->
<div class="modal-custom-overlay" id="overlayVerPDF">
    <div class="modal-dialog" style="max-width:90%;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf text-danger"></i> Vista Previa PDF Remisión
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerPDF">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div style="max-width: 900px; margin: 30px auto; padding: 30px 40px; background: white;">
                    <iframe id="pdfIframe" src="" 
                        style="width:100%; height:75vh; border:none;">
                    </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerPDF">Cerrar</button>
                <a href="#" id="btnDescargarPDF" class="btn btn-success" target="_blank">
                    <i class="fas fa-download"></i> Descargar PDF
                </a>
                <button type="button" onclick="$('#pdfIframe')[0].contentWindow.print()" 
                        class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var productoIndexRem = 0;
    var APP_URL = "{{ url('/') }}";
    var ROUTE_PRODUCTOS_REM = APP_URL + "/buscar-productos-remision";

    // ================================================
    // MODALES
    // ================================================
    function abrirOverlay(id) { $('#' + id).addClass('activo'); $('body').addClass('modal-open'); }
    function cerrarOverlay(id) {
        $('#' + id).removeClass('activo');
        if ($('.modal-custom-overlay.activo').length === 0) $('body').removeClass('modal-open');
    }
    $(document).on('click', '.btn-cerrar-modal', function() { cerrarOverlay($(this).data('overlay')); });
    $(document).on('click', '.modal-custom-overlay', function(e) {
        if ($(e.target).hasClass('modal-custom-overlay')) cerrarOverlay($(this).attr('id'));
    });

    // ================================================
    // FOCO SELECT2
    // ================================================
    $(document).on('select2:open', function() {
        setTimeout(function() {
            var $input = $('body').find('.select2-container--open .select2-search--dropdown .select2-search__field');
            if ($input.length) $input.get(0).focus();
        }, 200);
    });

    // ================================================
    // UTILIDADES
    // ================================================
    function destroySelect2(selector) {
        var $el = $(selector);
        if ($el.length && $el.hasClass('select2-hidden-accessible')) {
            try { $el.select2('destroy'); } catch(e) {}
        }
    }

    function limpiarCamposClienteRem(readonly) {
        $('#rem_cliente_nombre, #rem_cliente_cedula, #rem_cliente_telefono, #rem_cliente_email')
            .val('').prop('readonly', readonly);
    }

    function resetFormRem() {
        $('#formRemision')[0].reset();
        $('#tbody-productos-rem').empty();
        $('#sin-productos-msg-rem').show();
        $('#tabla-productos-container-rem').hide();
        productoIndexRem = 0;
        calcularTotalesRem();
        $('#cliente_general_rem').prop('checked', false);
        limpiarCamposClienteRem(false);
        destroySelect2('#id_cliente_rem');
        destroySelect2('#select-buscar-producto-rem');
    }

    function cargarNumeroRemision() {
        $.get(APP_URL + '/remisiones/numero-siguiente', function(r) {
            if (r && r.numero) $('#numero_remision').val(r.numero);
        });
    }

    // ================================================
    // CÁLCULOS
    // ================================================
    function calcularTotalesRem() {
        var subtotal = 0, descTotal = 0;
        $('#tbody-productos-rem .fila-producto-rem').each(function() {
            subtotal  += (parseInt($(this).find('.cantidad-rem').val())         || 0) *
                         (parseFloat($(this).find('.precio-unitario-rem').val()) || 0);
            descTotal += (parseFloat($(this).find('.descuento-rem').val())       || 0);
        });
        var total = subtotal - descTotal;
        $('#rem_subtotal').text('$' + subtotal.toLocaleString('es-CO'));
        $('#rem_descuento_total').text('$' + descTotal.toLocaleString('es-CO'));
        $('#rem_total').text('$' + total.toLocaleString('es-CO'));
    }

    function calcularFilaRem($fila) {
        var cant = parseInt($fila.find('.cantidad-rem').val())         || 0;
        var prec = parseFloat($fila.find('.precio-unitario-rem').val()) || 0;
        var desc = parseFloat($fila.find('.descuento-rem').val())       || 0;
        $fila.find('.item-total-rem').text('$' + ((cant * prec) - desc).toLocaleString('es-CO'));
        calcularTotalesRem();
    }

    // ================================================
    // SELECT2 CLIENTES
    // ================================================
    function initSelect2ClienteRem() {
        destroySelect2('#id_cliente_rem');
        $('#id_cliente_rem').empty().append('<option value=""></option>');
        $('#id_cliente_rem').select2({
            placeholder: 'Buscar cliente por nombre o cédula...',
            allowClear: true,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearRemision .modal-content'),
            width: '100%',
            language: {
                inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
                searching:     function() { return 'Buscando...'; },
                noResults:     function() { return 'No se encontraron clientes'; }
            },
            ajax: {
                url: "{{ route('buscar-clientes-cotizacion') }}",
                type: 'GET', dataType: 'json', delay: 400,
                data: function(p) { return { q: p.term }; },
                processResults: function(d) { return { results: d.results || [] }; },
                cache: false
            }
        });
        $('#id_cliente_rem').on('select2:select', function(e) {
            var d = e.params.data;
            $('#rem_cliente_nombre').val(d.nombre || d.text).prop('readonly', true);
            $('#rem_cliente_cedula').val(d.cedula   || '').prop('readonly', true);
            $('#rem_cliente_telefono').val(d.telefono || '').prop('readonly', false);
            $('#rem_cliente_email').val(d.email     || '').prop('readonly', false);
            $('#cliente_general_rem').prop('checked', false);
        });
        $('#id_cliente_rem').on('select2:clear', function() {
            if (!$('#cliente_general_rem').is(':checked')) limpiarCamposClienteRem(false);
        });
    }

    // ================================================
    // SELECT2 PRODUCTOS
    // ================================================
  function initSelect2ProductoRem() {
    destroySelect2('#select-buscar-producto-rem');
    $('#select-buscar-producto-rem').empty().append('<option value=""></option>');

    $('#select-buscar-producto-rem').select2({
        width: '100%',
        theme: 'bootstrap',
        placeholder: 'Buscar producto por nombre o código...',
        allowClear: false,
        minimumInputLength: 1,
        dropdownParent: $('#overlayCrearRemision .modal-content'),
        language: {
            inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
            searching:     function() { return 'Buscando...'; },
            noResults:     function() { return 'No se encontraron productos'; }
        },
        ajax: {
            url: ROUTE_PRODUCTOS_REM,
            type: 'GET',
            dataType: 'json',
            delay: 400,
            data: function(params) { return { q: params.term }; },
            processResults: function(data) { return { results: data.results || [] }; },
            cache: false
        },
        templateResult: function(item) {
            if (item.loading) return 'Buscando...';
            if (!item.precio) return item.text;
            return $('<div style="padding:3px 0;"><strong>' + item.text + '</strong><br>' +
                '<small class="text-muted">$' + parseFloat(item.precio).toLocaleString('es-CO') +
                ' | Stock: ' + item.stock + '</small></div>');
        },
        templateSelection: function(item) { return item.text || item.id; }
    });

    // ← .off() antes de .on() para evitar listeners acumulados
    $('#select-buscar-producto-rem').off('select2:select').on('select2:select', function(e) {
        var data = e.params.data;

        var yaExiste = false;
        $('#tbody-productos-rem .fila-producto-rem').each(function() {
            if ($(this).find('input[name*="[id_producto]"]').val() == data.id) {
                yaExiste = true;
                return false;
            }
        });

        $(this).val(null).trigger('change');

        if (yaExiste) {
            Swal.fire({
                icon: 'warning',
                title: 'Producto duplicado',
                text: 'El producto "' + data.text + '" ya está en la lista. Ajusta la cantidad si necesitas más.',
                timer: 2500,
                showConfirmButton: false,
                customClass: { container: 'swal-sobre-modal' }
            });
            return;
        }

        agregarFilaProductoRem(data);
    });
}
  
    // ================================================
    // TABLA PRODUCTOS
    // ================================================
    function agregarFilaProductoRem(data) {
        $('#sin-productos-msg-rem').hide();
        $('#tabla-productos-container-rem').show();

        var idx    = productoIndexRem++;
        var precio = parseFloat(data.precio) || 0;
        var stock  = parseInt(data.stock)    || 0;

        var fila = `
            <tr class="fila-producto-rem" data-index="${idx}">
                <td>
                    <span class="d-block font-weight-bold" style="font-size:12px;">${data.text}</span>
                    <small class="text-success"><i class="fas fa-cubes"></i> Stock: ${stock}</small>
                    <input type="hidden" name="productos[${idx}][id_producto]"     value="${data.id}">
                    <input type="hidden" name="productos[${idx}][nombre_producto]" value="${data.text}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm cantidad-rem text-center"
                           name="productos[${idx}][cantidad]"
                           step="1" min="1" value="1" required
                           style="width:70px; -moz-appearance:textfield;"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" class="form-control precio-unitario-rem"
                               name="productos[${idx}][precio_unitario]"
                               step="1" min="0" value="${precio}" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" class="form-control descuento-rem"
                               name="productos[${idx}][descuento]"
                               step="1" min="0" value="0">
                    </div>
                </td>
                <td class="text-right">
                    <strong class="text-primary item-total-rem" style="font-size:13px;">
                        $${precio.toLocaleString('es-CO')}
                    </strong>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar-fila-rem" title="Quitar">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;

        $('#tbody-productos-rem').append(fila);
        $('#tbody-productos-rem .fila-producto-rem').last().find('.cantidad-rem').focus().select();
        calcularTotalesRem();
    }

    $(document).on('input',
        '#tbody-productos-rem .cantidad-rem, #tbody-productos-rem .precio-unitario-rem, #tbody-productos-rem .descuento-rem',
        function() { calcularFilaRem($(this).closest('.fila-producto-rem')); }
    );

    $(document).on('click', '.btn-eliminar-fila-rem', function() {
        $(this).closest('.fila-producto-rem').fadeOut(200, function() {
            $(this).remove();
            if ($('#tbody-productos-rem .fila-producto-rem').length === 0) {
                $('#sin-productos-msg-rem').show();
                $('#tabla-productos-container-rem').hide();
            }
            calcularTotalesRem();
        });
    });

    // ================================================
    // CHECKBOX CLIENTE GENERAL
    // ================================================
    $('#cliente_general_rem').on('change', function() {
        if ($(this).is(':checked')) {
            if ($('#id_cliente_rem').hasClass('select2-hidden-accessible')) $('#id_cliente_rem').val(null).trigger('change');
            $('#rem_cliente_nombre').val('CLIENTE GENERAL').prop('readonly', true);
            $('#rem_cliente_cedula').val('0000000000').prop('readonly', true);
            $('#rem_cliente_telefono, #rem_cliente_email').val('').prop('readonly', false);
        } else {
            if (!$('#id_cliente_rem').val()) limpiarCamposClienteRem(false);
        }
    });

   
   
// ================================================
// DATATABLES CON FILTROS AUTOMÁTICOS
// ================================================
var tablaRemisiones = $('#tablaRemisiones').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('remisiones.data') }}",
        type: 'GET',
        data: function(d) {
            d.estado      = $('#filtro_estado').val();
            d.fecha_desde = $('#filtro_fecha_desde').val();
            d.fecha_hasta = $('#filtro_fecha_hasta').val();
            d.cliente     = $('#filtro_cliente').val();
        }
    },
    columns: [
        { data: 'numero_remision' },
        { data: 'fecha_remision' },
        { data: 'fecha_entrega_estimada' },
        { data: 'cliente_nombre' },
        { data: 'conductor' },
        { data: 'total_fmt' },
        { data: 'estado' },
        { data: 'acciones', orderable: false, searchable: false }
    ],
    language: {
        emptyTable:    "No hay remisiones registradas.",
        info:          "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
        infoEmpty:     "Mostrando 0 a 0 de 0 Entradas",
        infoFiltered:  "(Filtrado de _MAX_ total entradas)",
        lengthMenu:    "Mostrar _MENU_ Entradas",
        loadingRecords:"Cargando...",
        processing:    "Procesando...",
        search:        "Buscar:",
        zeroRecords:   "Sin resultados encontrados",
        paginate: { first:"Primero", last:"Ultimo", next:"Siguiente", previous:"Anterior" }
    },
    footerCallback: function(row, data) {
        var total = 0;
        data.forEach(function(f) {
            total += f.total_raw !== undefined
                ? parseFloat(f.total_raw)
                : parseFloat(String(f.total_fmt).replace(/[^0-9,.-]/g,'').replace(/\./g,'').replace(',','.')) || 0;
        });
        $('#totalGeneral').text('$' + total.toLocaleString('es-CO'));
    }
});

// ================================================
// FILTROS AUTOMÁTICOS (SIN BOTÓN)
// ================================================

// Filtros que se activan al cambiar el valor (selects y fechas)
$('#filtro_estado, #filtro_fecha_desde, #filtro_fecha_hasta').on('change', function() {
    tablaRemisiones.ajax.reload();
});

// Filtro de cliente con búsqueda mientras se escribe (delay de 500ms)
$('#filtro_cliente').on('keyup', function() {
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(function() {
        tablaRemisiones.ajax.reload();
    }, 500);
});

// También soportar Enter para búsqueda inmediata
$('#filtro_cliente').on('keypress', function(e) { 
    if (e.which === 13) {
        clearTimeout(window.searchTimeout);
        tablaRemisiones.ajax.reload();
    }
});

// Botón filtrar (opcional, puede mantenerlo o eliminarlo)
$('#btnFiltrar').on('click', function() {
    tablaRemisiones.ajax.reload();
});

// Botón limpiar filtros
$('#btnLimpiarFiltros').on('click', function() {
    $('#filtro_estado, #filtro_fecha_desde, #filtro_fecha_hasta').val('');
    $('#filtro_cliente').val('');
    tablaRemisiones.ajax.reload();
});

    // ================================================
    // BOTÓN NUEVA REMISIÓN
    // ================================================
    $('#btnNuevaRemision').on('click', function() {
        resetFormRem();
        cargarNumeroRemision();
        abrirOverlay('overlayCrearRemision');
        setTimeout(function() {
            initSelect2ClienteRem();
            initSelect2ProductoRem();
        }, 200);
    });

    $(document).on('click', '[data-overlay="overlayCrearRemision"]', function() {
        setTimeout(function() {
            $('#formRemision').removeData('modo').removeData('id');
            $('.modal-title').first().html('<i class="fas fa-truck"></i> Nueva Remisión');
            $('#texto_btn_guardar_rem').text('Guardar Remisión');
        }, 300);
    });

    // ================================================
    // GUARDAR / ACTUALIZAR
    // ================================================
    $('#formRemision').off('submit').on('submit', function(e) {
        e.preventDefault();

        if ($('#tbody-productos-rem .fila-producto-rem').length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un producto', 'warning');
            return;
        }

        var modo = $(this).data('modo') || 'crear';
        var id   = $(this).data('id')   || null;
        var url  = (modo === 'editar' && id)
            ? APP_URL + '/remisiones/' + id
            : "{{ route('remisiones.store') }}";
        var data = $(this).serialize();
        if (modo === 'editar' && id) data += '&_method=PUT';

        $('#spinner_guardar_rem').removeClass('d-none');
        $('#texto_btn_guardar_rem').text('Guardando...');
        $('#btnGuardarRemision').prop('disabled', true);

        $.ajax({
            url: url, type: 'POST', data: data,
            success: function(r) {
                if (r.success) {
                    cerrarOverlay('overlayCrearRemision');
                    Swal.fire({
                        icon: 'success',
                        title: modo === 'editar' ? 'Remisión actualizada' : '¡Remisión creada!',
                        text: r.message,
                        timer: 1800, showConfirmButton: false,
                        customClass: { container: 'swal-sobre-modal' }
                    });
                    tablaRemisiones.ajax.reload();
                    if (r.numero_siguiente) $('#numero_remision').val(r.numero_siguiente);
                    $('#formRemision').removeData('modo').removeData('id');
                    $('.modal-title').first().html('<i class="fas fa-truck"></i> Nueva Remisión');
                    $('#texto_btn_guardar_rem').text('Guardar Remisión');
                }
            },
            error: function(xhr) {
                var msg = 'Error al guardar la remisión';
                if (xhr.status === 422) {
                    var errors = [];
                    $.each(xhr.responseJSON.errors, function(k, v) { errors.push(v[0]); });
                    msg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', html: msg });
            },
            complete: function() {
                $('#spinner_guardar_rem').addClass('d-none');
                $('#texto_btn_guardar_rem').text(
                    $('#formRemision').data('modo') === 'editar' ? 'Actualizar Remisión' : 'Guardar Remisión'
                );
                $('#btnGuardarRemision').prop('disabled', false);
            }
        });
    });

    // ================================================
    // BOTÓN VER
    // ================================================
    $(document).on('click', '.btn-ver', function() {
        var id = $(this).data('id');
        $.get(APP_URL + '/remisiones/' + id, function(r) {
            $('#ver_numero_remision').text(r.numero_remision);
            $('#ver_numero').text(r.numero_remision);
            $('#ver_fecha').text(r.fecha_remision ? new Date(r.fecha_remision).toLocaleDateString('es-CO') : '—');
            $('#ver_fecha_entrega').text(r.fecha_entrega_estimada ? new Date(r.fecha_entrega_estimada).toLocaleDateString('es-CO') : '—');
            $('#ver_estado').html('<span class="badge badge-' + (r.estado_color || 'secondary') + '">' + (r.estado_texto || r.estado) + '</span>');
            $('#ver_vendedor').text(r.vendedor ? r.vendedor.name : 'N/A');
            $('#ver_conductor').text(r.conductor || '—');
            $('#ver_vehiculo').text(r.vehiculo_placa || '—');
            $('#ver_direccion').text(r.direccion_entrega || '—');

            var cli = r.cliente;
            $('#ver_cliente_nombre').text(cli ? cli.nombre : (r.cliente_nombre || 'Cliente General'));
            $('#ver_cliente_cedula').text(cli ? cli.cedula : (r.cliente_cedula || '—'));
            $('#ver_cliente_telefono').text(cli ? cli.telefono : (r.cliente_telefono || '—'));
            $('#ver_cliente_email').text(cli ? cli.email : (r.cliente_email || '—'));

            var filas = '', subtotal = 0, descTotal = 0;
            if (r.detalles && r.detalles.length) {
                r.detalles.forEach(function(d) {
                    var tot = (d.cantidad * d.precio_unitario) - (d.descuento || 0);
                    subtotal  += d.cantidad * d.precio_unitario;
                    descTotal += parseFloat(d.descuento) || 0;
                    filas += '<tr>'
                        + '<td>' + (d.codigo_producto || '—') + '</td>'
                        + '<td>' + (d.nombre_producto || '—') + '</td>'
                        + '<td class="text-center">' + parseInt(d.cantidad) + '</td>'
                        + '<td>' + (d.unidad_medida || '—') + '</td>'
                        + '<td class="text-right">$' + parseFloat(d.precio_unitario).toLocaleString('es-CO') + '</td>'
                        + '<td class="text-right">$' + parseFloat(d.descuento || 0).toLocaleString('es-CO') + '</td>'
                        + '<td class="text-right text-primary"><strong>$' + tot.toLocaleString('es-CO') + '</strong></td>'
                        + '</tr>';
                });
            } else {
                filas = '<tr><td colspan="7" class="text-center text-muted">Sin productos</td></tr>';
            }
            $('#ver_detalle_productos_rem').html(filas);
            $('#ver_subtotal_rem').text('$' + subtotal.toLocaleString('es-CO'));
            $('#ver_descuento_rem').text('$' + descTotal.toLocaleString('es-CO'));
            $('#ver_total_rem').text('$' + (subtotal - descTotal).toLocaleString('es-CO'));

            if (r.observaciones) {
                $('#ver_observaciones_rem').text(r.observaciones);
                $('#ver_obs_container').show();
            } else {
                $('#ver_obs_container').hide();
            }

            $('#btnCambiarEstadoRem').data('id', id);
            abrirOverlay('overlayVerRemision');
        }).fail(function() { Swal.fire('Error', 'No se pudo cargar la remisión', 'error'); });
    });

    // ================================================
    // BOTÓN EDITAR
    // ================================================
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        $.get(APP_URL + '/remisiones/' + id, function(r) {
            resetFormRem();
            initSelect2ClienteRem();
            initSelect2ProductoRem();

            $('.modal-title').first().html('<i class="fas fa-edit"></i> Editar Remisión');
            $('#formRemision').data('modo', 'editar').data('id', id);
            $('#texto_btn_guardar_rem').text('Actualizar Remisión');

            $('#numero_remision').val(r.numero_remision);
            $('#fecha_entrega_estimada').val(r.fecha_entrega_estimada ? r.fecha_entrega_estimada.substring(0, 10) : '');
            $('#conductor').val(r.conductor || '');
            $('#vehiculo_placa').val(r.vehiculo_placa || '');
            $('#direccion_entrega').val(r.direccion_entrega || '');
            $('#rem_observaciones').val(r.observaciones || '');

            if (r.cliente) {
                var opt = new Option(r.cliente.nombre + ' - ' + r.cliente.cedula, r.cliente.id_cliente, true, true);
                $('#id_cliente_rem').append(opt).trigger('change');
                $('#rem_cliente_nombre').val(r.cliente.nombre).prop('readonly', true);
                $('#rem_cliente_cedula').val(r.cliente.cedula).prop('readonly', true);
                $('#rem_cliente_telefono').val(r.cliente.telefono || '').prop('readonly', false);
                $('#rem_cliente_email').val(r.cliente.email || '').prop('readonly', false);
            } else if (r.cliente_nombre) {
                $('#cliente_general_rem').prop('checked', true).trigger('change');
                $('#rem_cliente_nombre').val(r.cliente_nombre).prop('readonly', false);
            }

            if (r.detalles && r.detalles.length) {
                r.detalles.forEach(function(d) {
                    agregarFilaProductoRem({
                        id:     d.id_producto,
                        text:   d.nombre_producto,
                        precio: d.precio_unitario,
                        stock:  d.producto ? d.producto.stock_actual : 0
                    });
                    var $last = $('#tbody-productos-rem .fila-producto-rem').last();
                    $last.find('.cantidad-rem').val(parseInt(d.cantidad));
                    $last.find('.descuento-rem').val(d.descuento || 0);
                    calcularFilaRem($last);
                });
            }

            abrirOverlay('overlayCrearRemision');
        }).fail(function() { Swal.fire('Error', 'No se pudo cargar la remisión', 'error'); });
    });

    // ================================================
    // CAMBIAR ESTADO
    // ================================================
    $('#btnCambiarEstadoRem').on('click', function() {
        $('#cambiar_estado_id_rem').val($(this).data('id'));
        abrirOverlay('overlayCambiarEstadoRem');
    });

    $('#btnGuardarCambioEstadoRem').on('click', function() {
        var id     = $('#cambiar_estado_id_rem').val();
        var estado = $('#nuevo_estado_rem').val();
        $.ajax({
            url: APP_URL + '/remisiones/' + id + '/cambiar-estado',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), estado: estado },
            success: function(r) {
                if (r.success) {
                    cerrarOverlay('overlayCambiarEstadoRem');
                    cerrarOverlay('overlayVerRemision');
                    tablaRemisiones.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Estado actualizado', timer: 1200, showConfirmButton: false });
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            },
            error: function() { Swal.fire('Error', 'No se pudo actualizar el estado', 'error'); }
        });
    });

    // ================================================
    // ELIMINAR
    // ================================================
    $(document).on('click', '.btn-eliminar', function() {
        var id     = $(this).data('id');
        var numero = $(this).data('numero');
        Swal.fire({
            title: '¿Eliminar remisión?',
            html: 'La remisión <strong>' + numero + '</strong> será eliminada permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + '/remisiones/' + id,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                    success: function(r) {
                        if (r.success) {
                            tablaRemisiones.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Eliminada', text: r.message, timer: 1500, showConfirmButton: false });
                        } else {
                            Swal.fire('No se puede eliminar', r.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error al eliminar', 'error');
                    }
                });
            }
        });
    });

    // ================================================
    // NUEVO CLIENTE
    // ================================================
    $('#btnAbrirNuevoClienteRem').on('click', function() {
        $('#formNuevoClienteRem')[0].reset();
        abrirOverlay('overlayNuevoClienteRem');
    });

    $('#formNuevoClienteRem').off('submit').on('submit', function(e) {
        e.preventDefault();
        $('#spinner_cliente_rem').removeClass('d-none');
        $('#texto_btn_cliente_rem').text('Guardando...');
        $('#btnGuardarClienteRem').prop('disabled', true);

        $.ajax({
            url: "{{ route('clientes.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    var cId  = response.data.id_cliente || response.data.id;
                    var cNom = response.data.nombre;
                    var cCed = response.data.cedula;
                    $('#id_cliente_rem').append(new Option(cNom + ' - ' + cCed, cId, true, true)).trigger('change');
                    $('#rem_cliente_nombre').val(cNom).prop('readonly', true);
                    $('#rem_cliente_cedula').val(cCed).prop('readonly', true);
                    $('#rem_cliente_telefono').val(response.data.telefono || '').prop('readonly', false);
                    $('#rem_cliente_email').val(response.data.email || '').prop('readonly', false);
                    cerrarOverlay('overlayNuevoClienteRem');
                    Swal.fire({
                        icon: 'success', title: '¡Cliente creado!',
                        text: 'El cliente ' + cNom + ' fue guardado correctamente.',
                        timer: 2000, showConfirmButton: false,
                        customClass: { container: 'swal-sobre-modal' }
                    });
                }
            },
            error: function(xhr) {
                var msg = 'Error al guardar el cliente';
                if (xhr.status === 422) {
                    var errors = [];
                    $.each(xhr.responseJSON.errors, function(k, v) { errors.push(v[0]); });
                    msg = errors.join('<br>');
                }
                Swal.fire({ icon: 'error', title: 'Error', html: msg });
            },
            complete: function() {
                $('#spinner_cliente_rem').addClass('d-none');
                $('#texto_btn_cliente_rem').text('Guardar Cliente');
                $('#btnGuardarClienteRem').prop('disabled', false);
            }
        });
    });

        // ================================================
    // FUNCIÓN PARA ABRIR PDF (IGUAL QUE EN COTIZACIONES)
    // ================================================
    function abrirPDF(id) {
        var url = APP_URL + '/remisiones/' + id + '/pdf';
        $('#pdfIframe').attr('src', url);
        // Botón descargar apunta a la misma ruta pero con ?download=1
        $('#btnDescargarPDF').attr('href', url + '?download=1');
        abrirOverlay('overlayVerPDF');
    }

    // Evento para el botón PDF en la tabla
    $(document).on('click', '.btn-pdf', function() {
        abrirPDF($(this).data('id'));
    });

    // Limpiar iframe al cerrar el overlay del PDF
    $(document).on('click', '[data-overlay="overlayVerPDF"]', function() {
        setTimeout(function() { $('#pdfIframe').attr('src', ''); }, 300);
    });

});

</script>
@endpush
@endsection