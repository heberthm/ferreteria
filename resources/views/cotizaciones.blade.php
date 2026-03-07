@extends('layouts.app')
@section('content')

<style>
/* ================================================
   ESTILOS GENERALES
================================================ */
.select2-container { width: 100% !important; }

/* Z-index alto para que el dropdown quede sobre el overlay */
.select2-container--open,
.select2-dropdown {
    z-index: 99999 !important;
}

/* ── Tema Bootstrap: bordes y colores coherentes ── */
.select2-container--bootstrap .select2-selection {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    height: 38px !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
    color: #495057 !important;
    background-color: #fff !important;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075) !important;
}
.select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
    color: #495057 !important;
    line-height: 24px !important;
    padding: 0 !important;
}
.select2-container--bootstrap .select2-selection--single .select2-selection__placeholder {
    color: #6c757d !important;
}
.select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
    top: 6px !important;
    right: 8px !important;
}
.select2-container--bootstrap.select2-container--focus .select2-selection,
.select2-container--bootstrap.select2-container--open .select2-selection {
    border-color: #80bdff !important;
    outline: 0 !important;
    box-shadow: 0 0 0 .2rem rgba(0,123,255,.25) !important;
}
.select2-container--bootstrap .select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    font-size: 14px !important;
}
.select2-container--bootstrap .select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 4px 8px !important;
    font-size: 13px !important;
}
.select2-container--bootstrap .select2-results__option {
    padding: 7px 12px !important;
    color: #212529 !important;
}
.select2-container--bootstrap .select2-results__option--highlighted {
    background-color: #e9ecef !important;
    color: #212529 !important;
}

#tabla-productos td { vertical-align: middle !important; padding: 5px 6px !important; }
#tabla-productos input { font-size: 12px; }

.badge-success   { background-color: #28a745; color: white; }
.badge-danger    { background-color: #dc3545; color: white; }
.badge-warning   { background-color: #ffc107; color: #212529; }
.badge-primary   { background-color: #007bff; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }

.filtros-cotizaciones {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.modal-lg-custom { max-width: 900px; }

/* ================================================
   SISTEMA DE MODALES PROPIO (sin Bootstrap JS)
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
    max-width: 900px;
    margin: auto;
    position: relative;
}
.modal-custom-overlay.modal-sm .modal-dialog {
    max-width: 500px;
}

/* Permite que el dropdown de Select2 desborde el modal-content */
#overlayCrearCotizacion .modal-content {
    overflow: visible !important;
}
</style>

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Gestión de Cotizaciones</h5>
        <button type="button" id="btnNuevaCotizacion" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Cotización
        </button>
    </div>

    <div class="card-body">
        <!-- Filtros -->
        <div class="filtros-cotizaciones">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="filtro_estado">
                            <option value="">Todos</option>
                            <option value="activa">Activa</option>
                            <option value="vencida">Vencida</option>
                            <option value="aceptada">Aceptada</option>
                            <option value="rechazada">Rechazada</option>
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
            <table class="table table-hover" id="tablaCotizaciones" style="width:100%; font-size:12.5px;">
                <thead>
                    <tr>
                        <th>N° Cotización</th>
                        <th>Fecha</th>
                        <th>Válido hasta</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
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
     MODAL CREAR COTIZACIÓN
================================================ -->
<div class="modal-custom-overlay" id="overlayCrearCotizacion">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Nueva Cotización</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCrearCotizacion">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formCotizacion">
                @csrf
                <div class="modal-body">

                    <!-- Número, fecha, método pago -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de Cotización *</label>
                                <input type="text" class="form-control" id="numero_cotizacion"
                                       name="numero_cotizacion"
                                      value="{{ $numero_cotizacion ?? 'COT-'.date('Ymd').'-00001' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Válido hasta</label>
                                <input type="date" class="form-control" id="fecha_validez"
                                       name="fecha_validez" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Método de pago sugerido</label>
                                <select class="form-control" id="metodo_pago_sugerido" name="metodo_pago_sugerido">
                                    <option value="">Seleccione...</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="contra_entrega">Contra entrega</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Información del Cliente</h6>
                            <button type="button" class="btn btn-success btn-sm" id="btnAbrirNuevoCliente">
                                <i class="fas fa-user-plus"></i> Nuevo Cliente
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Seleccionar cliente existente</label>
                                <!-- Select2 AJAX -->
                                <select class="form-control" id="id_cliente" name="id_cliente">
                                    <option value=""></option>
                                </select>
                            </div>
                            <hr>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="cliente_general" name="cliente_general">
                                <label class="form-check-label" for="cliente_general">
                                    <strong>Cliente General</strong> (sin registro)
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="cliente_nombre"
                                               name="cliente_nombre" placeholder="Nombre del cliente">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cédula/NIT</label>
                                        <input type="text" class="form-control" id="cliente_cedula"
                                               name="cliente_cedula" placeholder="Cédula o NIT">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" id="cliente_telefono"
                                               name="cliente_telefono" placeholder="Teléfono">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="cliente_email"
                                               name="cliente_email" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">Productos para cotizar</h6>
                        </div>
                        <div class="card-body p-2">
                            <!-- Buscador -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <!-- Select2 AJAX productos -->
                                    <select class="form-control" id="select-buscar-producto">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <!-- Sin productos -->
                            <div class="text-center text-muted py-3" id="sin-productos-msg">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">No hay productos. Busque y seleccione un producto arriba.</p>
                            </div>

                            <!-- Tabla productos -->
                            <div id="tabla-productos-container" style="display:none;">
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
                                        <tr>
                                            <td class="text-right"><strong>IVA (19%):</strong></td>
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

                    <!-- Observaciones -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Términos y condiciones</label>
                                <textarea class="form-control" id="terminos_condiciones" name="terminos_condiciones" rows="3">Cotización válida por 30 días. Precios sujetos a cambios sin previo aviso.</textarea>
                            </div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cerrar-modal"
                            data-overlay="overlayCrearCotizacion">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarCotizacion">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_guardar"></span>
                        <span id="texto_btn_guardar">Guardar Cotización</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL NUEVO CLIENTE
================================================ -->
<div class="modal-custom-overlay modal-sm" id="overlayNuevoCliente">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Cliente</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayNuevoCliente">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formNuevoCliente">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" id="nuevo_cliente_nombre"
                               name="nombre" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Cédula/NIT *</label>
                        <input type="text" class="form-control" id="nuevo_cliente_cedula"
                               name="cedula" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" id="nuevo_cliente_telefono"
                               name="telefono" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="nuevo_cliente_email"
                               name="email" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea class="form-control" id="nuevo_cliente_direccion"
                                  name="direccion" rows="2" autocomplete="off"></textarea>
                    </div>
                    <input type="hidden" name="userId" value="{{ auth()->id() }}">
                    <input type="hidden" name="estado" value="activo">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cerrar-modal"
                            data-overlay="overlayNuevoCliente">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardarCliente">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_cliente"></span>
                        <span id="texto_btn_cliente">Guardar Cliente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL VER COTIZACIÓN
================================================ -->
<div class="modal-custom-overlay" id="overlayVerCotizacion">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    Detalle de Cotización:
                    <span id="ver_numero_cotizacion" style="color:red"></span>
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerCotizacion">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Información de la Cotización</h6></div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless">
                                    <tr><td width="40%"><strong>Número:</strong></td><td><span id="ver_numero"></span></td></tr>
                                    <tr><td><strong>Fecha:</strong></td><td><span id="ver_fecha"></span></td></tr>
                                    <tr><td><strong>Válido hasta:</strong></td><td><span id="ver_fecha_validez"></span></td></tr>
                                    <tr><td><strong>Estado:</strong></td><td><span id="ver_estado"></span></td></tr>
                                    <tr><td><strong>Vendedor:</strong></td><td><span id="ver_vendedor"></span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Información del Cliente</h6></div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless">
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
                    <div class="card-header bg-light py-2"><h6 class="mb-0">Productos Cotizados</h6></div>
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
                                <tbody id="ver_detalle_productos"></tbody>
                                <tfoot>
                                    <tr><th colspan="5"></th><th class="text-right">Subtotal:</th><th class="text-right" id="ver_subtotal"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">Descuento:</th><th class="text-right" id="ver_descuento"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">TOTAL:</th><th class="text-right" id="ver_total"></th></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row" id="ver_observaciones_container" style="display:none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Observaciones</h6></div>
                            <div class="card-body py-2"><p id="ver_observaciones" class="mb-0"></p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerCotizacion">Cerrar</button>
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
                <h5 class="modal-title">Cambiar Estado de Cotización</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCambiarEstado">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cambiar_estado_id">
                <div class="form-group">
                    <label>Nuevo estado:</label>
                    <select class="form-control" id="nuevo_estado">
                        <option value="activa">Activa</option>
                        <option value="vencida">Vencida</option>
                        <option value="aceptada">Aceptada</option>
                        <option value="rechazada">Rechazada</option>
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
<div class="modal-custom-overlay" id="overlayVerPDF">
    <div class="modal-dialog" style="max-width:90%;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf text-danger"></i> Vista Previa PDF
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerPDF">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfIframe" src="" style="width:100%; height:600px; border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerPDF">Cerrar</button>
                <a href="#" id="btnDescargarPDF" class="btn btn-success" target="_blank">
                    <i class="fas fa-download"></i> Descargar PDF
                </a>
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
    var APP_URL       = "{{ url('/') }}";
    var ROUTE_CLIENTES  = APP_URL + "/buscar-clientes";
    var ROUTE_PRODUCTOS = APP_URL + "/buscar-productos-cotizacion";

    // ================================================
    // SISTEMA DE MODALES PROPIO
    // No depende de Bootstrap JS en absoluto
    // ================================================
    function abrirOverlay(id) {
        $('#' + id).addClass('activo');
        $('body').addClass('modal-open');
    }

    function cerrarOverlay(id) {
        $('#' + id).removeClass('activo');
        // Si no hay ningún overlay abierto, liberar el body
        if ($('.modal-custom-overlay.activo').length === 0) {
            $('body').removeClass('modal-open');
        }
    }

    // Botón X y botón Cancelar — cualquier .btn-cerrar-modal
    $(document).on('click', '.btn-cerrar-modal', function() {
        var overlayId = $(this).data('overlay');
        cerrarOverlay(overlayId);
    });

    // Clic en el fondo oscuro también cierra
    $(document).on('click', '.modal-custom-overlay', function(e) {
        if ($(e.target).hasClass('modal-custom-overlay')) {
            cerrarOverlay($(this).attr('id'));
        }
    });

    // ================================================
    // DATATABLES
    // ================================================
    var tablaCotizaciones = $('#tablaCotizaciones').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cotizaciones.data') }}",
            type: 'GET',
            data: function(d) {
                d.estado       = $('#filtro_estado').val();
                d.fecha_desde  = $('#filtro_fecha_desde').val();
                d.fecha_hasta  = $('#filtro_fecha_hasta').val();
                d.cliente      = $('#filtro_cliente').val();
            }
        },
        columns: [
            { data: 'numero_cotizacion' },
            { data: 'fecha_cotizacion' },
            { data: 'fecha_validez' },
            { data: 'cliente_nombre' },
            { data: 'vendedor' },
            { data: 'total' },
            { data: 'estado' },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            emptyTable:    "No hay cotizaciones registradas.",
            info:          "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            infoEmpty:     "Mostrando 0 a 0 de 0 Entradas",
            infoFiltered:  "(Filtrado de _MAX_ total entradas)",
            thousands:     ",",
            lengthMenu:    "Mostrar _MENU_ Entradas",
            loadingRecords:"Cargando...",
            processing:    "Procesando...",
            search:        "Buscar:",
            zeroRecords:   "Sin resultados encontrados",
            paginate: { first:"Primero", last:"Ultimo", next:"Siguiente", previous:"Anterior" }
        },
        footerCallback: function(row, data, start, end, display) {
            // Sumar totales de la página visible (los datos ya vienen formateados)
            var totalPagina = 0;
            data.forEach(function(fila) {
                // El campo total viene como string HTML "$1.234" — extraer número
                var raw = fila.total_raw !== undefined
                    ? parseFloat(fila.total_raw)
                    : parseFloat(String(fila.total).replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
                totalPagina += raw;
            });
            $('#totalGeneral').text('$' + totalPagina.toLocaleString('es-CO'));
        }
    });

    // Botón Filtrar
    $('#btnFiltrar').on('click', function() {
        tablaCotizaciones.ajax.reload();
    });

    // Limpiar filtros
    $('#btnLimpiarFiltros').on('click', function() {
        $('#filtro_estado').val('');
        $('#filtro_fecha_desde').val('');
        $('#filtro_fecha_hasta').val('');
        $('#filtro_cliente').val('');
        tablaCotizaciones.ajax.reload();
    });

    // Filtrar también al presionar Enter en el campo cliente
    $('#filtro_cliente').on('keypress', function(e) {
        if (e.which === 13) tablaCotizaciones.ajax.reload();
    });

    // ================================================
    // SELECT2 — helpers
    // ================================================
    function destroySelect2(selector) {
        var $el = $(selector);
        if ($el.length && $el.hasClass('select2-hidden-accessible')) {
            try { $el.select2('destroy'); } catch(e) {}
        }
    }

    function initSelect2Cliente() {
        destroySelect2('#id_cliente');
        $('#id_cliente').select2({
            width: '100%',
            theme: 'bootstrap',
            placeholder: 'Buscar cliente por nombre o cédula...',
            allowClear: true,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearCotizacion .modal-content'),
            language: {
                inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
                searching:     function() { return 'Buscando...'; },
                noResults:     function() { return 'No se encontraron clientes'; }
            },
            ajax: {
                url: ROUTE_CLIENTES,
                type: 'POST',
                dataType: 'json',
                delay: 400,
                data: function(params) { return { q: params.term, _token: $('meta[name="csrf-token"]').attr('content') }; },
                processResults: function(data) { return { results: data.results || [] }; },
                cache: false
            }
        });

        $('#id_cliente').on('select2:select', function(e) {
            var d = e.params.data;
            $('#cliente_nombre').val(d.nombre   || d.text).prop('readonly', true);
            $('#cliente_cedula').val(d.cedula   || '').prop('readonly', true);
            $('#cliente_telefono').val(d.telefono || '').prop('readonly', false);
            $('#cliente_email').val(d.email     || '').prop('readonly', false);
            $('#cliente_general').prop('checked', false);
        });

        $('#id_cliente').on('select2:clear', function() {
            if (!$('#cliente_general').is(':checked')) limpiarCamposCliente(false);
        });
    }

    function initSelect2Producto() {
        destroySelect2('#select-buscar-producto');
        $('#select-buscar-producto').select2({
            width: '100%',
            theme: 'bootstrap',
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearCotizacion .modal-content'),
            language: {
                inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
                searching:     function() { return 'Buscando...'; },
                noResults:     function() { return 'No se encontraron productos'; }
            },
            ajax: {
                url: ROUTE_PRODUCTOS,
                type: 'POST',
                dataType: 'json',
                delay: 400,
                data: function(params) { return { q: params.term, _token: $('meta[name="csrf-token"]').attr('content') }; },
                processResults: function(data) { return { results: data.results || [] }; },
                cache: false
            },
            templateResult: function(item) {
                if (item.loading) return 'Buscando...';
                if (!item.precio) return item.text;
                return $('<div style="padding:3px 0;"><strong>' + item.text + '</strong><br>' +
                    '<small class="text-muted">$' + parseFloat(item.precio).toLocaleString('es-CO') +
                    ' | Stock: ' + item.stock + ' unidades</small></div>');
            },
            templateSelection: function(item) { return item.text || item.id; }
        });

        $('#select-buscar-producto').on('select2:select', function(e) {
            var data = e.params.data;
            $(this).val(null).trigger('change');
            agregarRenglonProducto(data);
        });
    }

    // ================================================
    // ABRIR MODAL COTIZACIÓN
    // ================================================
    $('#btnNuevaCotizacion').on('click', function() {
        resetForm();
        // ── CAMBIO 1: cargar número correcto desde el servidor ──
        cargarNumeroCotizacion();
        abrirOverlay('overlayCrearCotizacion');
        // Select2 necesita que el contenedor sea visible antes de inicializar
        setTimeout(function() {
            initSelect2Cliente();
            initSelect2Producto();
        }, 100);
    });

    // ── CAMBIO 1: función que obtiene el siguiente número del servidor ──
    function cargarNumeroCotizacion() {
        $.ajax({
            url: APP_URL + '/cotizaciones/numero-siguiente',
            type: 'GET',
            success: function(r) {
                if (r && r.numero) {
                    $('#numero_cotizacion').val(r.numero);
                }
            }
            // Si falla, conserva el valor que ya tiene el input (generado por Blade)
        });
    }

    // ================================================
    // MODAL NUEVO CLIENTE
    // ================================================
    $('#btnAbrirNuevoCliente').on('click', function() {
        $('#formNuevoCliente')[0].reset();
        abrirOverlay('overlayNuevoCliente');
        setTimeout(function() { $('#nuevo_cliente_nombre').focus(); }, 100);
    });

    $('#formNuevoCliente').on('submit', function(e) {
        e.preventDefault();

        $('#spinner_cliente').removeClass('d-none');
        $('#texto_btn_cliente').text('Guardando...');
        $('#btnGuardarCliente').prop('disabled', true);

        $.ajax({
            url: "{{ route('clientes.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Agregar al Select2 de clientes y seleccionarlo
                    var opt = new Option(
                        response.data.nombre + ' - ' + response.data.cedula,
                        response.data.id_cliente, true, true
                    );
                    $('#id_cliente').append(opt).trigger('change');

                    $('#cliente_nombre').val(response.data.nombre).prop('readonly', true);
                    $('#cliente_cedula').val(response.data.cedula).prop('readonly', true);
                    $('#cliente_telefono').val(response.data.telefono || '').prop('readonly', false);
                    $('#cliente_email').val(response.data.email || '').prop('readonly', false);

                    cerrarOverlay('overlayNuevoCliente');

                    Swal.fire({
                        icon: 'success', title: 'Cliente creado',
                        text: 'El cliente se guardó correctamente',
                        timer: 1500, showConfirmButton: false
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
                $('#spinner_cliente').addClass('d-none');
                $('#texto_btn_cliente').text('Guardar Cliente');
                $('#btnGuardarCliente').prop('disabled', false);
            }
        });
    });

    // ================================================
    // PRODUCTOS — TABLA
    // ================================================
    function agregarRenglonProducto(data) {
        $('#sin-productos-msg').hide();
        $('#tabla-productos-container').show();

        var idx    = productoIndex++;
        var precio = parseFloat(data.precio) || 0;
        var stock  = data.stock || 0;

        var fila = `
            <tr class="fila-producto" data-index="${idx}">
                <td>
                    <span class="d-block font-weight-bold" style="font-size:12px;">${data.text}</span>
                    <small class="text-success"><i class="fas fa-cubes"></i> Stock: ${stock}</small>
                    <input type="hidden" name="productos[${idx}][id_producto]"     value="${data.id}">
                    <input type="hidden" name="productos[${idx}][nombre_producto]" value="${data.text}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm cantidad text-center"
                           name="productos[${idx}][cantidad]"
                           step="1"    min="1"    value="1" required style="width:70px;">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" class="form-control precio-unitario"
                               name="productos[${idx}][precio_unitario]"
                               step="1" min="0" value="${precio}" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" class="form-control descuento"
                               name="productos[${idx}][descuento]"
                               step="1" min="0" value="0">
                    </div>
                </td>
                <td class="text-right">
                    <strong class="text-primary item-total" style="font-size:13px;">
                        $${precio.toLocaleString('es-CO')}
                    </strong>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" title="Quitar">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;

        $('#tbody-productos').append(fila);
        $('#tbody-productos .fila-producto').last().find('.cantidad').focus().select();
        calcularTotales();
    }

    function calcularFila($fila) {
        var cant = parseFloat($fila.find('.cantidad').val())        || 0;
        var prec = parseFloat($fila.find('.precio-unitario').val()) || 0;
        var desc = parseFloat($fila.find('.descuento').val())       || 0;
        $fila.find('.item-total').text('$' + ((cant * prec) - desc).toLocaleString('es-CO'));
        calcularTotales();
    }

    function calcularTotales() {
        var subtotal = 0, descTotal = 0;
        $('#tbody-productos .fila-producto').each(function() {
            subtotal  += (parseFloat($(this).find('.cantidad').val())        || 0) *
                         (parseFloat($(this).find('.precio-unitario').val()) || 0);
            descTotal += (parseFloat($(this).find('.descuento').val())       || 0);
        });
        var iva   = subtotal * 0.19;
        var total = (subtotal - descTotal) + iva;
        $('#subtotal').text('$'        + subtotal.toLocaleString('es-CO'));
        $('#descuento_total').text('$' + descTotal.toLocaleString('es-CO'));
        $('#iva_total').text('$'       + iva.toLocaleString('es-CO'));
        $('#total').text('$'           + total.toLocaleString('es-CO'));
    }

    $(document).on('input',
        '#tbody-productos .cantidad, #tbody-productos .precio-unitario, #tbody-productos .descuento',
        function() { calcularFila($(this).closest('.fila-producto')); }
    );

    $(document).on('click', '.btn-eliminar-fila', function() {
        $(this).closest('.fila-producto').fadeOut(200, function() {
            $(this).remove();
            if ($('#tbody-productos .fila-producto').length === 0) {
                $('#sin-productos-msg').show();
                $('#tabla-productos-container').hide();
            }
            calcularTotales();
        });
    });

    // ================================================
    // CHECKBOX CLIENTE GENERAL
    // ================================================
    $('#cliente_general').on('change', function() {
        if ($(this).is(':checked')) {
            if ($('#id_cliente').hasClass('select2-hidden-accessible')) {
                $('#id_cliente').val(null).trigger('change');
            }
            $('#cliente_nombre').val('CLIENTE GENERAL').prop('readonly', true);
            $('#cliente_cedula').val('0000000000').prop('readonly', true);
            $('#cliente_telefono, #cliente_email').val('').prop('readonly', false);
        } else {
            if (!$('#id_cliente').val()) limpiarCamposCliente(false);
        }
    });

    // ================================================
    // UTILIDADES
    // ================================================
    function resetForm() {
        $('#formCotizacion')[0].reset();
        $('#sin-productos-msg').show();
        $('#tabla-productos-container').hide();
        $('#tbody-productos').empty();
        productoIndex = 0;
        calcularTotales();
        $('#cliente_general').prop('checked', false);
        limpiarCamposCliente(false);
        // Limpiar Select2 si ya estaban inicializados
        destroySelect2('#id_cliente');
        destroySelect2('#select-buscar-producto');
        // Nota: el número se actualiza en cargarNumeroCotizacion()
        // que se llama desde btnNuevaCotizacion justo después de resetForm()
    }

    function limpiarCamposCliente(readonly) {
        $('#cliente_nombre, #cliente_cedula, #cliente_telefono, #cliente_email')
            .val('').prop('readonly', readonly).prop('disabled', false);
    }

    // ================================================
    // BOTÓN VER — carga datos en overlayVerCotizacion
    // ================================================
    $(document).on('click', '.btn-ver', function() {
        var id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/cotizaciones/' + id,
            type: 'GET',
            success: function(c) {
                // Datos generales
                $('#ver_numero_cotizacion').text(c.numero_cotizacion);
                $('#ver_numero').text(c.numero_cotizacion);
                $('#ver_fecha').text(c.fecha_cotizacion
                    ? new Date(c.fecha_cotizacion).toLocaleDateString('es-CO') : '—');
                $('#ver_fecha_validez').text(c.fecha_validez
                    ? new Date(c.fecha_validez).toLocaleDateString('es-CO') : '—');
                $('#ver_estado').html(
                    '<span class="badge badge-' + (c.estado_color || 'secondary') + '">'
                    + (c.estado_texto || c.estado) + '</span>'
                );
                $('#ver_vendedor').text(c.vendedor ? c.vendedor.name : 'N/A');

                // Datos del cliente
                var cli = c.cliente;
                $('#ver_cliente_nombre').text(cli ? cli.nombre   : (c.cliente_nombre   || 'Cliente General'));
                $('#ver_cliente_cedula').text(cli ? cli.cedula   : (c.cliente_cedula   || '—'));
                $('#ver_cliente_telefono').text(cli ? cli.telefono : (c.cliente_telefono || '—'));
                $('#ver_cliente_email').text(cli ? cli.email    : (c.cliente_email    || '—'));

                // Tabla de productos
                var filas = '';
                var subtotal = 0, descTotal = 0;
                if (c.detalles && c.detalles.length) {
                    c.detalles.forEach(function(d) {
                        var tot = (d.cantidad * d.precio_unitario) - (d.descuento || 0);
                        subtotal  += d.cantidad * d.precio_unitario;
                        descTotal += parseFloat(d.descuento) || 0;
                        filas += '<tr>'
                            + '<td>' + (d.producto ? (d.producto.codigo || '—') : '—') + '</td>'
                            + '<td>' + (d.nombre_producto || (d.producto ? d.producto.nombre : '—')) + '</td>'
                            + '<td class="text-center">' + d.cantidad + '</td>'
                            + '<td>' + (d.unidad_medida || '—') + '</td>'
                            + '<td class="text-right">$' + parseFloat(d.precio_unitario).toLocaleString('es-CO') + '</td>'
                            + '<td class="text-right">$' + parseFloat(d.descuento || 0).toLocaleString('es-CO') + '</td>'
                            + '<td class="text-right text-primary"><strong>$' + tot.toLocaleString('es-CO') + '</strong></td>'
                            + '</tr>';
                    });
                } else {
                    filas = '<tr><td colspan="7" class="text-center text-muted">Sin productos</td></tr>';
                }
                $('#ver_detalle_productos').html(filas);

                var iva   = (subtotal - descTotal) * 0.19;
                var total = (subtotal - descTotal) + iva;
                $('#ver_subtotal').text('$' + subtotal.toLocaleString('es-CO'));
                $('#ver_descuento').text('$' + descTotal.toLocaleString('es-CO'));
                $('#ver_total').text('$' + total.toLocaleString('es-CO'));

                // Observaciones
                if (c.observaciones) {
                    $('#ver_observaciones').text(c.observaciones);
                    $('#ver_observaciones_container').show();
                } else {
                    $('#ver_observaciones_container').hide();
                }

                // Guardar id para botones del footer
                $('#btnPdfDesdeVer').data('id', id);
                $('#btnCambiarEstado').data('id', id);

                abrirOverlay('overlayVerCotizacion');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la cotización', 'error');
            }
        });
    });

    // ================================================
    // BOTÓN EDITAR — precarga formulario crear con datos existentes
    // ================================================
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/cotizaciones/' + id,
            type: 'GET',
            success: function(c) {
                // Resetear form y reinicializar Select2
                resetForm();
                initSelect2Cliente();
                initSelect2Producto();

                // Cambiar título y modo del formulario
                $('.modal-title').first().html('<i class="fas fa-edit"></i> Editar Cotización');
                $('#formCotizacion').data('modo', 'editar').data('id', id);
                $('#btnGuardarCotizacion').find('#texto_btn_guardar').text('Actualizar Cotización');

                // Rellenar campos generales
                $('#numero_cotizacion').val(c.numero_cotizacion);
                $('#fecha_validez').val(c.fecha_validez ? c.fecha_validez.substring(0, 10) : '');
                $('#metodo_pago').val(c.metodo_pago || '');
                $('#observaciones').val(c.observaciones || '');
                $('#terminos_condiciones').val(c.terminos_condiciones || '');

                // Rellenar cliente
                if (c.cliente) {
                    var opt = new Option(c.cliente.nombre + ' - ' + c.cliente.cedula, c.cliente.id_cliente, true, true);
                    $('#id_cliente').append(opt).trigger('change');
                    $('#cliente_nombre').val(c.cliente.nombre).prop('readonly', true);
                    $('#cliente_cedula').val(c.cliente.cedula).prop('readonly', true);
                    $('#cliente_telefono').val(c.cliente.telefono || '').prop('readonly', false);
                    $('#cliente_email').val(c.cliente.email || '').prop('readonly', false);
                } else if (c.cliente_nombre) {
                    $('#cliente_general').prop('checked', true).trigger('change');
                    $('#cliente_nombre').val(c.cliente_nombre).prop('readonly', false);
                }

                // Rellenar productos
                if (c.detalles && c.detalles.length) {
                    c.detalles.forEach(function(d) {
                        agregarRenglonProducto({
                            id:     d.id_producto,
                            text:   d.nombre_producto || (d.producto ? d.producto.nombre : ''),
                            precio: d.precio_unitario,
                            stock:  d.producto ? d.producto.stock : 0
                        });
                        // Actualizar cantidad y descuento de la última fila agregada
                        var $lastRow = $('#tbody-productos .fila-producto').last();
                        $lastRow.find('.cantidad').val(d.cantidad);
                        $lastRow.find('.descuento').val(d.descuento || 0);
                        calcularFila($lastRow);
                    });
                }

                abrirOverlay('overlayCrearCotizacion');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la cotización', 'error');
            }
        });
    });

    // Al guardar: detectar si es crear o editar y usar la ruta correcta
    $('#formCotizacion').off('submit').on('submit', function(e) {
        e.preventDefault();

        if ($('#tbody-productos .fila-producto').length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un producto', 'warning');
            return;
        }

        var modo = $(this).data('modo') || 'crear';
        var id   = $(this).data('id')   || null;
        var url  = (modo === 'editar' && id)
            ? APP_URL + '/cotizaciones/' + id
            : "{{ route('cotizaciones-guardar') }}";
        var method = (modo === 'editar' && id) ? 'POST' : 'POST';
        var data   = $(this).serialize();
        if (modo === 'editar' && id) data += '&_method=PUT';

        $('#spinner_guardar').removeClass('d-none');
        $('#texto_btn_guardar').text('Guardando...');
        $('#btnGuardarCotizacion').prop('disabled', true);

        $.ajax({
            url: url, type: method, data: data,
            success: function(response) {
                if (response.success) {
                    cerrarOverlay('overlayCrearCotizacion');
                    Swal.fire({
                        icon: 'success',
                        title: modo === 'editar' ? 'Cotización actualizada' : 'Cotización guardada',
                        text: response.message,
                        timer: 1500, showConfirmButton: false
                    });
                    tablaCotizaciones.ajax.reload();
                    if (response.numero_siguiente) {
                        $('#numero_cotizacion').val(response.numero_siguiente);
                    }
                    // Resetear modo a crear para la próxima
                    $('#formCotizacion').removeData('modo').removeData('id');
                    $('.modal-title').first().html('<i class="fas fa-file-invoice"></i> Nueva Cotización');
                    $('#texto_btn_guardar').text('Guardar Cotización');
                }
            },
            error: function(xhr) {
                var msg = 'Error al guardar la cotización';
                if (xhr.status === 422) {
                    var errors = [];
                    $.each(xhr.responseJSON.errors, function(k, v) { errors.push(v[0]); });
                    msg = errors.join('<br>');
                }
                Swal.fire({ icon: 'error', title: 'Error', html: msg });
            },
            complete: function() {
                $('#spinner_guardar').addClass('d-none');
                $('#texto_btn_guardar').text(
                    $('#formCotizacion').data('modo') === 'editar' ? 'Actualizar Cotización' : 'Guardar Cotización'
                );
                $('#btnGuardarCotizacion').prop('disabled', false);
            }
        });
    });

    // Al cerrar el overlay de crear, resetear modo
    $(document).on('click', '[data-overlay="overlayCrearCotizacion"]', function() {
        setTimeout(function() {
            $('#formCotizacion').removeData('modo').removeData('id');
            $('.modal-title').first().html('<i class="fas fa-file-invoice"></i> Nueva Cotización');
            $('#texto_btn_guardar').text('Guardar Cotización');
        }, 300);
    });

    // ================================================
    // BOTÓN PDF (desde tabla y desde modal Ver)
    // ================================================
    function abrirPDF(id) {
        var url = APP_URL + '/cotizaciones/' + id + '/pdf';
        $('#pdfIframe').attr('src', url);
        $('#btnDescargarPDF').attr('href', url);
        abrirOverlay('overlayVerPDF');
    }

    $(document).on('click', '.btn-pdf', function() {
        abrirPDF($(this).data('id'));
    });

    $('#btnPdfDesdeVer').on('click', function() {
        var id = $(this).data('id');
        if (id) abrirPDF(id);
    });

    // Limpiar iframe al cerrar el overlay del PDF
    $(document).on('click', '[data-overlay="overlayVerPDF"]', function() {
        setTimeout(function() { $('#pdfIframe').attr('src', ''); }, 300);
    });

    // ================================================
    // BOTÓN CAMBIAR ESTADO (desde modal Ver)
    // ================================================
    $('#btnCambiarEstado').on('click', function() {
        var id = $(this).data('id');
        $('#cambiar_estado_id').val(id);
        abrirOverlay('overlayCambiarEstado');
    });

    $('#btnGuardarCambioEstado').on('click', function() {
        var id     = $('#cambiar_estado_id').val();
        var estado = $('#nuevo_estado').val();
        $.ajax({
            url: APP_URL + '/cotizaciones/' + id + '/cambiar-estado',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), estado: estado },
            success: function(r) {
                if (r.success) {
                    cerrarOverlay('overlayCambiarEstado');
                    cerrarOverlay('overlayVerCotizacion');
                    tablaCotizaciones.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Estado actualizado', timer: 1200, showConfirmButton: false });
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            },
            error: function() { Swal.fire('Error', 'No se pudo actualizar el estado', 'error'); }
        });
    });

    // ================================================
    // BOTÓN ELIMINAR
    // ================================================
    $(document).on('click', '.btn-eliminar', function() {
        var id     = $(this).data('id');
        var numero = $(this).data('numero');
        Swal.fire({
            title: '¿Eliminar cotización?',
            html: 'La cotización <strong>' + numero + '</strong> será eliminada permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + '/cotizaciones/' + id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(r) {
                        if (r.success) {
                            tablaCotizaciones.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Eliminada', text: r.message, timer: 1500, showConfirmButton: false });
                        } else {
                            Swal.fire('No se puede eliminar', r.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al eliminar';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });

});
</script>
@endpush
@endsection