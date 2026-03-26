
@extends('layouts.app')

@section('content')
<style>
/* ================================================
   ESTILOS SELECT2 PERSONALIZADOS (CORREGIDO)
================================================ */
.select2-container {
    width: 100% !important;
}

/* Z-index alto para que el dropdown quede sobre el overlay */
.select2-container--open,
.select2-dropdown {
    z-index: 99999 !important;
}

/* Estilos base del select2 */
.select2-selection {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    min-height: 38px !important;
    padding: 4px 12px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    background-color: #fff !important;
}

.select2-selection--single {
    height: 38px !important;
}

.select2-selection__rendered {
    line-height: 28px !important;
    padding-left: 0 !important;
    color: #495057 !important;
}

.select2-selection__placeholder {
    color: #6c757d !important;
}

/* Estilo para el botón "X" de limpiar */
.select2-selection__clear {
    position: absolute !important;
    right: 25px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 18px !important;
    font-weight: bold !important;
    color: #dc3545 !important;
    cursor: pointer !important;
    z-index: 10 !important;
    background: transparent !important;
    border: none !important;
    padding: 0 5px !important;
    margin: 0 !important;
}

.select2-selection__clear:hover {
    color: #bd2130 !important;
    background: transparent !important;
}

/* Flecha del select */
.select2-selection__arrow {
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    height: auto !important;
    width: 20px !important;
}

.select2-selection__arrow b {
    border-color: #6c757d transparent transparent transparent !important;
    border-width: 5px 4px 0 4px !important;
    margin-left: -8px !important;
}

/* Estado focus */
.select2-container--open .select2-selection {
    border-color: #80bdff !important;
    outline: 0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}

/* Dropdown */
.select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    font-size: 14px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,.1) !important;
}

.select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
}

.select2-results__option {
    padding: 8px 12px !important;
    color: #212529 !important;
}

.select2-results__option--highlighted {
    background-color: #e9ecef !important;
    color: #212529 !important;
}

.select2-results__option[aria-selected=true] {
    background-color: #007bff !important;
    color: white !important;
}

/* Cuando el select está deshabilitado o vacío */
.select2-selection--single .select2-selection__clear:not([style*="display: none"]) {
    display: inline-block !important;
}

#tabla-productos td { vertical-align: middle !important; padding: 5px 6px !important; }
#tabla-productos input { font-size: 12px; }

.badge-success   { background-color: #28a745; color: white; }
.badge-danger    { background-color: #dc3545; color: white; }
.badge-warning   { background-color: #ffc107; color: #212529; }
.badge-primary   { background-color: #007bff; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-info      { background-color: #17a2b8; color: white; }

.filtros-devoluciones {
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
#overlayCrearDevolucion .modal-content {
    overflow: visible !important;
}

/* ================================================
   ESTILOS ESPECÍFICOS PARA SELECT2 DE PRODUCTOS
================================================ */
#overlayCrearDevolucion .select2-container--default .select2-selection--single {
    height: 38px !important;
    position: relative !important;
}

#overlayCrearDevolucion .select2-container--default .select2-selection__rendered {
    line-height: 36px !important;
    padding-right: 50px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

#overlayCrearDevolucion .select2-container--default .select2-selection__clear {
    position: absolute !important;
    right: 30px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 20px !important;
    font-weight: bold !important;
    color: #dc3545 !important;
    cursor: pointer !important;
    z-index: 1000 !important;
    background: transparent !important;
    border: none !important;
    padding: 0 5px !important;
    margin: 0 !important;
    display: inline-block !important;
    line-height: 1 !important;
    height: auto !important;
    width: auto !important;
    text-decoration: none !important;
    opacity: 1 !important;
    visibility: visible !important;
}

#overlayCrearDevolucion .select2-container--default .select2-selection__clear:hover {
    color: #bd2130 !important;
    background: transparent !important;
}

#overlayCrearDevolucion .select2-container--default .select2-selection__arrow {
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    height: auto !important;
    width: 20px !important;
}

#overlayCrearDevolucion .select2-container--default .select2-selection__arrow b {
    border-color: #6c757d transparent transparent transparent !important;
    border-width: 5px 4px 0 4px !important;
    margin-left: -8px !important;
}

/* Estilos para el dropdown de búsqueda */
#overlayCrearDevolucion .select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,.1) !important;
}

#overlayCrearDevolucion .select2-search--dropdown {
    padding: 8px !important;
    background: #f8f9fa !important;
    border-bottom: 1px solid #ced4da !important;
}

#overlayCrearDevolucion .select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    width: 100% !important;
    box-sizing: border-box !important;
    background: white !important;
}

#overlayCrearDevolucion .select2-search--dropdown .select2-search__field:focus {
    border-color: #80bdff !important;
    outline: 0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}

/* Resultados */
#overlayCrearDevolucion .select2-results__option {
    padding: 8px 12px !important;
    border-bottom: 1px solid #f0f0f0 !important;
}

#overlayCrearDevolucion .select2-results__option:last-child {
    border-bottom: none !important;
}

#overlayCrearDevolucion .select2-results__option--highlighted {
    background-color: #e9ecef !important;
    color: #212529 !important;
}

#overlayCrearDevolucion .select2-results__option[aria-selected=true] {
    background-color: #007bff !important;
    color: white !important;
}

/* Ocultar flechas del input number para cantidad */
#tabla-productos input[type=number].cantidad::-webkit-inner-spin-button,
#tabla-productos input[type=number].cantidad::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

#tabla-productos input[type=number].cantidad {
    -moz-appearance: textfield;
    appearance: textfield;
}

/* Estilos para los botones igual que en devoluciones */
.btn-group .btn {
    margin: 0 2px;
    border-radius: 4px !important;
}

.btn-group .btn i {
    font-size: 14px;
}

/* Para mantener consistencia en el espaciado */
.table td .btn-group {
    white-space: nowrap;
}

/* SweetAlert2 siempre encima de los modales */
.swal-sobre-modal {
    z-index: 99999 !important;
}

/* Estilos para el estado de condición del producto */
.condicion-nuevo { color: #28a745; }
.condicion-abierto { color: #ffc107; }
.condicion-usado { color: #17a2b8; }
.condicion-danado { color: #dc3545; }
.condicion-incompleto { color: #6c757d; }

/* Selector de condición en la tabla */
select.condicion-producto {
    width: 100%;
    font-size: 11px;
    padding: 2px;
}

</style>

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-undo-alt"></i> Gestión de Devoluciones</h5>
        <button type="button" class="btn btn-primary" id="btnNuevaDevolucion" data-toggle="modal" data-target="#modalDevolucion">
            <i class="fas fa-plus"></i> Nueva Devolución
        </button>

    </div>

    <div class="card-body">
        <!-- Filtros -->
        <div class="filtros-devoluciones">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="filtro_estado">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobada">Aprobada</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="completada">Completada</option>
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
            <table class="table table-hover" id="tablaDevoluciones" style="width:100%; font-size:12.5px;">
                <thead>
                    <tr>
                        <th>N° Devolución</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
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
     MODAL CREAR DEVOLUCIÓN
================================================ -->
<div class="modal-custom-overlay" id="overlayCrearDevolucion">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-undo-alt"></i> Nueva Devolución</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayCrearDevolucion">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formDevolucion">
                @csrf
                <div class="modal-body">

                    <!-- PRIMERA FILA: Número, fecha, tipo devolución -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="numero_devolucion">Número de Devolución</label>
                                <input type="text" 
                                    class="form-control" 
                                    id="numero_devolucion" 
                                    name="numero_devolucion" 
                                    value="{{ $siguienteNumero }}" 
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha de Devolución *</label>
                                <input type="date" class="form-control" id="fecha_devolucion"
                                       name="fecha_devolucion" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo de Devolución *</label>
                                <select class="form-control" id="tipo_devolucion" name="tipo_devolucion" required>
                                    <option value="parcial">Parcial</option>
                                    <option value="total">Total</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Venta relacionada y Motivo -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Venta relacionada (opcional)</label>
                                <select class="form-control" id="id_venta" name="id_venta">
                                    <option value=""></option>
                                </select>
                                <small class="text-muted">Seleccione una venta para cargar productos automáticamente</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Motivo de Devolución *</label>
                                <select class="form-control" id="motivo" name="motivo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="producto_danado">Producto dañado</option>
                                    <option value="producto_equivocado">Producto equivocado</option>
                                    <option value="cambio_de_producto">Cambio de producto</option>
                                    <option value="insatisfaccion">Insatisfacción</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción motivo -->
                    <div class="row" id="motivo_otro_container" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Especifique el motivo</label>
                                <input type="text" class="form-control" id="motivo_descripcion" 
                                       name="motivo_descripcion" placeholder="Describa el motivo de la devolución">
                            </div>
                        </div>
                    </div>

                    <!-- Método de reembolso -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Método de Reembolso *</label>
                                <select class="form-control" id="metodo_reembolso" name="metodo_reembolso" required>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="credito_en_cuenta">Crédito en cuenta</option>
                                    <option value="no_aplica">No aplica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox mt-4">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="reingresar_inventario" name="reingresar_inventario" value="1" checked>
                                    <label class="custom-control-label" for="reingresar_inventario">
                                        Reingresar productos al inventario
                                    </label>
                                </div>
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
                            <h6 class="mb-0">Productos a devolver</h6>
                        </div>
                        <div class="card-body p-2">
                            <!-- Buscador -->
                            <div class="row mb-3">
                                <div class="col-md-12" style="position:relative;">
                                    <select class="form-control" id="select-buscar-producto">
                                        <option value=""></option>
                                    </select>
                                    <!-- Botón X independiente, siempre visible -->
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

                            <!-- Sin productos -->
                            <div class="text-center text-muted py-3" id="sin-productos-msg">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">No hay productos. Busque y seleccione un producto arriba o cargue una venta.</p>
                            </div>

                            <!-- Tabla productos -->
                            <div id="tabla-productos-container" style="display:none;">
                                <table class="table table-sm table-bordered mb-0" id="tabla-productos">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:25%">Producto</th>
                                            <th style="width:8%" class="text-center">Cant.</th>
                                            <th style="width:12%">Precio</th>
                                            <th style="width:12%">Descuento</th>
                                            <th style="width:15%">Condición</th>
                                            <th style="width:10%" class="text-right">Total</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-productos"></tbody>
                                </table>
                            </div>
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
                                        <td class="text-right">
                                            <span>IVA 19%:</span>
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

                    <!-- Observaciones -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-cerrar-modal"
                            data-overlay="overlayCrearDevolucion">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarDevolucion">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_guardar"></span>
                        <span id="texto_btn_guardar">Guardar Devolución</span>
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
     MODAL VER DEVOLUCIÓN
================================================ -->
<div class="modal-custom-overlay" id="overlayVerDevolucion">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    Detalle de Devolución:
                    <span id="ver_numero_devolucion" style="color:red"></span>
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerDevolucion">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Información de la Devolución</h6></div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless">
                                    <tr><td width="40%"><strong>Número:</strong></td><td><span id="ver_numero"></span></td></tr>
                                    <tr><td><strong>Fecha:</strong></td><td><span id="ver_fecha"></span></td></tr>
                                    <tr><td><strong>Tipo:</strong></td><td><span id="ver_tipo"></span></td></tr>
                                    <tr><td><strong>Motivo:</strong></td><td><span id="ver_motivo"></span></td></tr>
                                    <tr><td><strong>Método reembolso:</strong></td><td><span id="ver_metodo"></span></td></tr>
                                    <tr><td><strong>Estado:</strong></td><td><span id="ver_estado"></span></td></tr>
                                    <tr><td><strong>Creado por:</strong></td><td><span id="ver_creador"></span></td></tr>
                                    <tr><td><strong>Fecha creación:</strong></td><td><span id="ver_created_at"></span></td></tr>
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

                <!-- Información de aprobación (si existe) -->
                <div class="row mb-4" id="ver_aprobacion_container" style="display:none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Información de Aprobación</h6></div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless">
                                    <tr><td width="15%"><strong>Aprobado por:</strong></td><td><span id="ver_aprobador"></span></td>
                                        <td width="15%"><strong>Fecha aprobación:</strong></td><td><span id="ver_fecha_aprobacion"></span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><h6 class="mb-0">Productos Devueltos</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Código</th><th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-right">P.Unitario</th>
                                        <th class="text-right">Descuento</th>
                                        <th>Condición</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_detalle_productos"></tbody>
                                <tfoot>
                                    <tr><th colspan="5"></th><th class="text-right">Subtotal:</th><th class="text-right" id="ver_subtotal"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">Descuento:</th><th class="text-right" id="ver_descuento"></th></tr>
                                    <tr><th colspan="5"></th><th class="text-right">IVA:</th><th class="text-right" id="ver_iva"></th></tr>
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

                <div class="row" id="ver_notas_internas_container" style="display:none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Notas Internas</h6></div>
                            <div class="card-body py-2"><p id="ver_notas_internas" class="mb-0"></p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayVerDevolucion">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnPdfDesdeVer" data-id="">
                    <i class="fas fa-file-pdf"></i> Ver PDF
                </button>
                <div id="ver_acciones_estado"></div>
            </div>
        </div>
    </div>
</div>


<!-- ================================================
     MODAL APROBAR/RECHAZAR
================================================ -->
<div class="modal-custom-overlay modal-sm" id="overlayAprobarRechazar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modal_accion_titulo">Aprobar Devolución</h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayAprobarRechazar">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="accion_id">
                <input type="hidden" id="accion_tipo">
                
                <div class="form-group" id="motivo_rechazo_group">
                    <label>Motivo del rechazo:</label>
                    <textarea class="form-control" id="motivo_rechazo" rows="3"></textarea>
                </div>

                <div class="form-group" id="confirmacion_aprobar_group" style="display:none;">
                    <p>¿Está seguro de aprobar esta devolución?</p>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> 
                        Si está marcado "Reingresar inventario", los productos volverán al stock.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-modal"
                        data-overlay="overlayAprobarRechazar">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarAccion">
                    Confirmar
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
                    <i class="fas fa-file-pdf text-danger"></i> Vista Previa PDF Devolución
                </h5>
                <button type="button" class="close btn-cerrar-modal" data-overlay="overlayVerPDF">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div style="max-width: 900px; margin: 30px auto; padding: 30px 40px; background: white;">
                    <iframe id="pdfIframe" src="" 
                        style="width:100%; height:75vh; border:none;"
                        id="pdfIframe">
                    </iframe>
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
    var ROUTE_PRODUCTOS = APP_URL + "/buscar-productos-devolucion";

    // Mapeo de textos para condición de producto
    var condicionTextos = {
        'nuevo_sin_uso': 'Nuevo sin uso',
        'abierto_sin_uso': 'Abierto sin uso',
        'usado_buen_estado': 'Usado buen estado',
        'danado': 'Dañado',
        'incompleto': 'Incompleto'
    };

    // Mapeo de textos para motivo
    var motivoTextos = {
        'producto_danado': 'Producto dañado',
        'producto_equivocado': 'Producto equivocado',
        'cambio_de_producto': 'Cambio de producto',
        'insatisfaccion': 'Insatisfacción',
        'otro': 'Otro'
    };

    // Mapeo de textos para tipo
    var tipoTextos = {
        'total': 'Total',
        'parcial': 'Parcial'
    };

    // ================================================
    // SISTEMA DE MODALES PROPIO
    // ================================================
    function abrirOverlay(id) {
        $('#' + id).addClass('activo');
        $('body').addClass('modal-open');
    }

    function cerrarOverlay(id) {
        $('#' + id).removeClass('activo');
        if ($('.modal-custom-overlay.activo').length === 0) {
            $('body').removeClass('modal-open');
        }
    }

    $(document).on('click', '.btn-cerrar-modal', function() {
        var overlayId = $(this).data('overlay');
        cerrarOverlay(overlayId);
    });

    $(document).on('click', '.modal-custom-overlay', function(e) {
        if ($(e.target).hasClass('modal-custom-overlay')) {
            cerrarOverlay($(this).attr('id'));
        }
    });

    // ================================================
    // DATATABLES
    // ================================================
    var tablaDevoluciones = $('#tablaDevoluciones').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('devoluciones.data') }}",
            type: 'GET',
            data: function(d) {
                d.estado       = $('#filtro_estado').val();
                d.fecha_desde  = $('#filtro_fecha_desde').val();
                d.fecha_hasta  = $('#filtro_fecha_hasta').val();
                d.cliente      = $('#filtro_cliente').val();
            }
        },
        columns: [
            { data: 'numero_devolucion' },
            { data: 'fecha' },
            { data: 'cliente_nombre' },
            { data: 'tipo_devolucion', render: function(data) { return tipoTextos[data] || data; } },
            { data: 'motivo', render: function(data) { return motivoTextos[data] || data; } },
            { data: 'total_formateado' },
            { 
                data: null,
                render: function(data) {
                    return '<span class="badge badge-' + data.estado_color + '">' + 
                           (data.estado_texto || data.estado) + '</span>';
                }
            },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            emptyTable:    "No hay devoluciones registradas.",
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
            var totalPagina = 0;
            data.forEach(function(fila) {
                var raw = fila.total_raw !== undefined
                    ? parseFloat(fila.total_raw)
                    : parseFloat(String(fila.total_formateado).replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
                totalPagina += raw;
            });
            $('#totalGeneral').text('$' + totalPagina.toLocaleString('es-CO'));
        }
    });

    // Filtros automáticos
    $('#filtro_estado, #filtro_fecha_desde, #filtro_fecha_hasta').on('change', function() {
        tablaDevoluciones.ajax.reload();
    });

    $('#filtro_cliente').on('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() {
            tablaDevoluciones.ajax.reload();
        }, 500);
    });

    $('#filtro_cliente').on('keypress', function(e) { 
        if (e.which === 13) {
            clearTimeout(window.searchTimeout);
            tablaDevoluciones.ajax.reload();
        }
    });

    $('#btnFiltrar').on('click', function() {
        tablaDevoluciones.ajax.reload();
    });

    $('#btnLimpiarFiltros').on('click', function() {
        $('#filtro_estado').val('');
        $('#filtro_fecha_desde').val('');
        $('#filtro_fecha_hasta').val('');
        $('#filtro_cliente').val('');
        tablaDevoluciones.ajax.reload();
    });

    // ================================================
    // SELECT2 helpers
    // ================================================
    function destroySelect2(selector) {
        var $el = $(selector);
        if ($el.length && $el.hasClass('select2-hidden-accessible')) {
            try { $el.select2('destroy'); } catch(e) {}
        }
    }

    // FOCO para Select2
    $(document).on('select2:open', function(e) {
        setTimeout(function() {
            var $input = $('body').find(
                '.select2-container--open .select2-search--dropdown .select2-search__field'
            );
            if ($input.length) {
                $input.get(0).focus();
            }
        }, 200);
    });

    // ================================================
    // SELECT2 CLIENTES
    // ================================================
    function initSelect2Cliente() {
        destroySelect2('#id_cliente');
        $('#id_cliente').empty().append('<option value=""></option>');

        $('#id_cliente').select2({
            placeholder: 'Buscar cliente por nombre o cédula...',
            allowClear: true,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearDevolucion .modal-content'),
            width: '100%',
            language: {
                inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
                searching:     function() { return 'Buscando...'; },
                noResults:     function() { return 'No se encontraron clientes'; }
            },
            ajax: {
                url: "{{ route('clientes.buscar') }}",
                type: 'GET',
                dataType: 'json',
                delay: 400,
                data: function(params) {
                    return { q: params.term, _token: $('meta[name="csrf-token"]').attr('content') };
                },
                processResults: function(data) {
                    return { results: data.results || [] };
                },
                cache: false
            }
        });

        $('#id_cliente').on('select2:select', function(e) {
            var d = e.params.data;
            $('#cliente_nombre').val(d.nombre || d.text).prop('readonly', true);
            $('#cliente_cedula').val(d.cedula   || '').prop('readonly', true);
            $('#cliente_telefono').val(d.telefono || '').prop('readonly', false);
            $('#cliente_email').val(d.email     || '').prop('readonly', false);
            $('#cliente_general').prop('checked', false);
        });

        $('#id_cliente').on('select2:clear', function() {
            if (!$('#cliente_general').is(':checked')) {
                limpiarCamposCliente(false);
            }
        });
    }

    // ================================================
    // SELECT2 VENTAS
    // ================================================
    function initSelect2Venta() {
        destroySelect2('#id_venta');
        $('#id_venta').empty().append('<option value=""></option>');

        $('#id_venta').select2({
            placeholder: 'Buscar venta por número o cliente...',
            allowClear: true,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearDevolucion .modal-content'),
            width: '100%',
            language: {
                inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
                searching:     function() { return 'Buscando...'; },
                noResults:     function() { return 'No se encontraron ventas'; }
            },
            ajax: {
                url: "{{ route('devoluciones.buscar-ventas') }}",
                type: 'GET',
                dataType: 'json',
                delay: 400,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data.results || [] };
                },
                cache: false
            }
        });

        $('#id_venta').on('select2:select', function(e) {
            var d = e.params.data;
            
            // Rellenar datos del cliente
            $('#id_cliente').val(null).trigger('change');
            $('#cliente_nombre').val(d.cliente_nombre).prop('readonly', true);
            $('#cliente_cedula').val(d.cliente_cedula).prop('readonly', true);
            $('#cliente_telefono').val(d.cliente_telefono || '').prop('readonly', false);
            $('#cliente_email').val(d.cliente_email || '').prop('readonly', false);
            $('#cliente_general').prop('checked', false);

            // Cargar productos de la venta
            cargarProductosVenta(d.id);
        });
    }

    function cargarProductosVenta(ventaId) {
        $.ajax({
            url: APP_URL + '/devoluciones/detalles-venta/' + ventaId,
            type: 'GET',
            success: function(data) {
                // Limpiar productos actuales
                $('#tbody-productos').empty();
                productoIndex = 0;
                
                if (data.productos && data.productos.length) {
                    $('#sin-productos-msg').hide();
                    $('#tabla-productos-container').show();
                    
                    data.productos.forEach(function(prod) {
                        agregarRenglonProducto({
                            id: prod.id_producto,
                            text: prod.nombre,
                            precio: prod.precio_unitario,
                            stock: prod.stock_actual,
                            cantidad_original: prod.cantidad_original,
                            codigo: prod.codigo
                        }, true);
                    });
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudieron cargar los productos de la venta', 'error');
            }
        });
    }

    // ================================================
    // SELECT2 PRODUCTOS
    // ================================================
    function initSelect2Producto() {
        destroySelect2('#select-buscar-producto');
        $('#select-buscar-producto').empty().append('<option value=""></option>');
        $('#btn-limpiar-producto').hide();

        $('#select-buscar-producto').select2({
            width: '100%',
            theme: 'bootstrap',
            placeholder: 'Buscar producto por nombre o código...',
            allowClear: false,
            minimumInputLength: 1,
            dropdownParent: $('#overlayCrearDevolucion .modal-content'),
            language: {
                inputTooShort: function() { return 'Ingrese al menos 1 caracter'; },
                searching:     function() { return 'Buscando...'; },
                noResults:     function() { return 'No se encontraron productos'; }
            },
            ajax: {
                url: ROUTE_PRODUCTOS,
                type: 'GET',
                dataType: 'json',
                delay: 400,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data.results || [] };
                },
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

        $('#select-buscar-producto').off('select2:select').on('select2:select', function(e) {
            var data = e.params.data;

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
                    text: 'El producto "' + data.text + '" ya está en la lista.',
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { container: 'swal-sobre-modal' }
                });
                return;
            }

            agregarRenglonProducto(data, false);
        });
    }

    // ================================================
    // PRODUCTOS — TABLA
    // ================================================
    function agregarRenglonProducto(data, desdeVenta) {
        $('#sin-productos-msg').hide();
        $('#tabla-productos-container').show();

        var idx    = productoIndex++;
        var precio = parseFloat(data.precio) || 0;
        var stock  = data.stock || 0;

        var fila = `
            <tr class="fila-producto" data-index="${idx}">
                <td>
                    <span class="d-block font-weight-bold" style="font-size:12px;">${data.text}</span>
                    <small class="text-muted">Código: ${data.codigo || ''}</small>
                    <small class="text-success d-block"><i class="fas fa-cubes"></i> Stock: ${stock} unidades</small>
                    <input type="hidden" name="productos[${idx}][id_producto]"     value="${data.id}">
                    <input type="hidden" name="productos[${idx}][nombre_producto]" value="${data.text}">
                    <input type="hidden" name="productos[${idx}][codigo_producto]" value="${data.codigo || ''}">
                    <input type="hidden" name="productos[${idx}][cantidad_original]" value="${data.cantidad_original || ''}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm cantidad text-center"
                           name="productos[${idx}][cantidad_devuelta]"
                           step="1" min="1" value="1" required 
                           onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                           style="width:60px; -moz-appearance: textfield;" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" class="form-control precio-unitario"
                               name="productos[${idx}][precio_unitario]"
                               step="0.01" min="0" value="${precio}" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" class="form-control descuento"
                               name="productos[${idx}][descuento]"
                               step="0.01" min="0" value="0">
                    </div>
                </td>
                <td>
                    <select class="form-control form-control-sm condicion-producto" 
                            name="productos[${idx}][condicion_producto]" required>
                        <option value="nuevo_sin_uso">Nuevo sin uso</option>
                        <option value="abierto_sin_uso">Abierto sin uso</option>
                        <option value="usado_buen_estado">Usado buen estado</option>
                        <option value="danado">Dañado</option>
                        <option value="incompleto">Incompleto</option>
                    </select>
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

        var base    = subtotal - descTotal;
        var iva     = base * 0.19; // 19% IVA fijo para devoluciones
        var total   = base + iva;

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
        $(this).closest('tr').remove();
        if ($('#tbody-productos tr').length === 0) {
            $('#sin-productos-msg').show();
            $('#tabla-productos-container').hide();
        }
        calcularTotales();
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
    // MOTIVO OTRO
    // ================================================
    $('#motivo').on('change', function() {
        if ($(this).val() === 'otro') {
            $('#motivo_otro_container').show();
            $('#motivo_descripcion').prop('required', true);
        } else {
            $('#motivo_otro_container').hide();
            $('#motivo_descripcion').prop('required', false).val('');
        }
    });

    // ================================================
    // UTILIDADES
    // ================================================
    function resetForm() {
        $('#formDevolucion')[0].reset();
        $('#fecha_devolucion').val(new Date().toISOString().slice(0,10));
        $('#motivo_otro_container').hide();
        $('#sin-productos-msg').show();
        $('#tabla-productos-container').hide();
        $('#tbody-productos').empty();
        productoIndex = 0;
        calcularTotales();
        $('#cliente_general').prop('checked', false);
        limpiarCamposCliente(false);
        destroySelect2('#id_cliente');
        destroySelect2('#id_venta');
        destroySelect2('#select-buscar-producto');
        cargarNumeroDevolucion();
    }

    function limpiarCamposCliente(readonly) {
        $('#cliente_nombre, #cliente_cedula, #cliente_telefono, #cliente_email')
            .val('').prop('readonly', readonly).prop('disabled', false);
    }

    function cargarNumeroDevolucion() {
        $.ajax({
            url: APP_URL + '/devoluciones/numero-siguiente',
            type: 'GET',
            success: function(r) {
                if (r && r.numero) {
                    $('#numero_devolucion').val(r.numero);
                }
            }
        });
    }


    // ================================================
    // ABRIR MODAL DEVOLUCIÓN
    // ================================================
    $('#btnNuevaDevolucion').on('click', function() {
        resetForm();
        abrirOverlay('overlayCrearDevolucion');
        setTimeout(function() {
            initSelect2Cliente();
            initSelect2Venta();
            initSelect2Producto();
        }, 100);
    });

    // ================================================
    // MODAL NUEVO CLIENTE
    // ================================================
    $('#btnAbrirNuevoCliente').on('click', function() {
        $('#formNuevoCliente')[0].reset();
        abrirOverlay('overlayNuevoCliente');
        setTimeout(function() { $('#nuevo_cliente_nombre').focus(); }, 100);
    });

    $('#formNuevoCliente').off('submit').on('submit', function(e) {
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
                    var clienteId  = response.data.id_cliente || response.data.id;
                    var clienteNom = response.data.nombre;
                    var clienteCed = response.data.cedula;

                    var opt = new Option(
                        clienteNom + ' - ' + clienteCed,
                        clienteId, true, true
                    );
                    $('#id_cliente').append(opt).trigger('change');

                    $('#cliente_nombre').val(clienteNom).prop('readonly', true);
                    $('#cliente_cedula').val(clienteCed).prop('readonly', true);
                    $('#cliente_telefono').val(response.data.telefono || '').prop('readonly', false);
                    $('#cliente_email').val(response.data.email || '').prop('readonly', false);
                    $('#cliente_general').prop('checked', false);

                    cerrarOverlay('overlayNuevoCliente');

                    Swal.fire({
                        icon: 'success',
                        title: '¡Cliente creado!',
                        text: 'El cliente ' + clienteNom + ' fue guardado correctamente.',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        customClass: { container: 'swal-sobre-modal' } 
                    });

                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'No se pudo guardar el cliente' });
                }
            },
            error: function(xhr) {
                var msg = 'Error al guardar el cliente';
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
                $('#spinner_cliente').addClass('d-none');
                $('#texto_btn_cliente').text('Guardar Cliente');
                $('#btnGuardarCliente').prop('disabled', false);
            }
        });
    });


   
function precargarNumeroDevolucion() {
    // Opcional: precargar para usar cuando se abra el modal
    $.ajax({
        url: '{{ route("devoluciones.next-number") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Almacenar en un data attribute o variable global
            $('#modalDevolucion').data('next-number', response.numero);
        }
    });
}

   // ================================================
// GUARDAR DEVOLUCIÓN
// ================================================
$('#formDevolucion').on('submit', function(e) {
    e.preventDefault();

    // Validar que haya al menos un producto
    if ($('#tbody-productos .fila-producto').length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin productos',
            text: 'Debe agregar al menos un producto a devolver.',
            customClass: { container: 'swal-sobre-modal' }
        });
        return;
    }

    // Mostrar spinner
    $('#spinner_guardar').removeClass('d-none');
    $('#texto_btn_guardar').text('Guardando...');
    $('#btnGuardarDevolucion').prop('disabled', true);

    var modo = $(this).data('modo');  // 'editar' o undefined
    var id   = $(this).data('id');

    var url    = modo === 'editar'
                    ? APP_URL + '/devoluciones/' + id
                    : '{{ route("devoluciones.store") }}';
    var method = 'POST';
    var formData = $(this).serialize();

    // Para editar usamos PUT via _method
    if (modo === 'editar') {
        formData += '&_method=PUT';
    }

    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                // Cerrar modal
                cerrarOverlay('overlayCrearDevolucion');

                // Recargar DataTable sin recargar página
                tablaDevoluciones.ajax.reload(null, false);

                // Actualizar número siguiente en el campo (por si se reabre)
                if (response.numero_siguiente) {
                    $('#numero_devolucion').val(response.numero_siguiente);
                }

                Swal.fire({
                    icon: 'success',
                    title: modo === 'editar' ? '¡Actualizada!' : '¡Guardada!',
                    text: response.message || 'Devolución guardada exitosamente.',
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo guardar la devolución.',
                    customClass: { container: 'swal-sobre-modal' }
                });
            }
        },
        error: function(xhr) {
            var msg = 'Error al guardar la devolución.';
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors || {};
                var lista  = [];
                $.each(errors, function(k, v) { lista.push(v[0]); });
                msg = lista.join('<br>');
                Swal.fire({
                    icon: 'error',
                    title: 'Errores de validación',
                    html: msg,
                    customClass: { container: 'swal-sobre-modal' }
                });
            } else {
                var serverMsg = xhr.responseJSON ? xhr.responseJSON.message : null;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: serverMsg || msg,
                    customClass: { container: 'swal-sobre-modal' }
                });
            }
        },
        complete: function() {
            // Siempre restaurar el botón
            $('#spinner_guardar').addClass('d-none');
            $('#texto_btn_guardar').text(
                $('#formDevolucion').data('modo') === 'editar'
                    ? 'Actualizar Devolución'
                    : 'Guardar Devolución'
            );
            $('#btnGuardarDevolucion').prop('disabled', false);
        }
    });
});
    

   
    // ================================================
    // VER DEVOLUCIÓN
    // ================================================
    $(document).on('click', '.btn-ver', function() {
        var id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/devoluciones/' + id,
            type: 'GET',
            success: function(d) {
                $('#ver_numero_devolucion').text(d.numero_devolucion);
                $('#ver_numero').text(d.numero_devolucion);
                $('#ver_fecha').text(d.fecha_devolucion ? new Date(d.fecha_devolucion).toLocaleDateString('es-CO') : '—');
                $('#ver_tipo').text(tipoTextos[d.tipo_devolucion] || d.tipo_devolucion);
                $('#ver_motivo').text(motivoTextos[d.motivo] || d.motivo + (d.motivo_descripcion ? ': ' + d.motivo_descripcion : ''));
                $('#ver_metodo').text(d.metodo_reembolso || '—');
                $('#ver_estado').html(
                    '<span class="badge badge-' + (d.estado === 'pendiente' ? 'warning' : 
                                                    d.estado === 'aprobada' ? 'success' :
                                                    d.estado === 'rechazada' ? 'danger' :
                                                    d.estado === 'completada' ? 'info' : 'secondary') + '">' +
                    (d.estado_texto || d.estado) + '</span>'
                );
                $('#ver_creador').text(d.creador ? d.creador.name : 'N/A');
                $('#ver_created_at').text(d.created_at ? new Date(d.created_at).toLocaleString('es-CO') : '—');

                // Datos del cliente
                var cli = d.cliente;
                $('#ver_cliente_nombre').text(cli ? cli.nombre : (d.cliente_nombre || 'Cliente General'));
                $('#ver_cliente_cedula').text(cli ? cli.cedula : (d.cliente_cedula || '—'));
                $('#ver_cliente_telefono').text(cli ? cli.telefono : (d.cliente_telefono || '—'));
                $('#ver_cliente_email').text(cli ? cli.email : (d.cliente_email || '—'));

                // Info aprobación
                if (d.aprobador) {
                    $('#ver_aprobador').text(d.aprobador.name);
                    $('#ver_fecha_aprobacion').text(d.fecha_aprobacion ? new Date(d.fecha_aprobacion).toLocaleString('es-CO') : '—');
                    $('#ver_aprobacion_container').show();
                } else {
                    $('#ver_aprobacion_container').hide();
                }

                // Tabla de productos
                var filas = '';
                var subtotal = 0, descTotal = 0;
                if (d.detalles && d.detalles.length) {
                    d.detalles.forEach(function(det) {
                        var tot = (det.cantidad_devuelta * det.precio_unitario) - (det.descuento || 0);
                        subtotal  += det.cantidad_devuelta * det.precio_unitario;
                        descTotal += parseFloat(det.descuento) || 0;
                        filas += '<tr>'
                            + '<td>' + (det.codigo_producto || (det.producto ? det.producto.codigo : '') || '—') + '</td>'
                            + '<td>' + (det.nombre_producto || (det.producto ? det.producto.nombre : '—')) + '</td>'
                            + '<td class="text-center">' + parseInt(det.cantidad_devuelta) + '</td>'
                            + '<td class="text-right">$' + parseFloat(det.precio_unitario).toLocaleString('es-CO') + '</td>'
                            + '<td class="text-right">$' + parseFloat(det.descuento || 0).toLocaleString('es-CO') + '</td>'
                            + '<td>' + (condicionTextos[det.condicion_producto] || det.condicion_producto) + '</td>'
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
                $('#ver_iva').text('$' + iva.toLocaleString('es-CO'));
                $('#ver_total').text('$' + total.toLocaleString('es-CO'));

                // Observaciones
                if (d.observaciones) {
                    $('#ver_observaciones').text(d.observaciones);
                    $('#ver_observaciones_container').show();
                } else {
                    $('#ver_observaciones_container').hide();
                }

                // Notas internas
                if (d.notas_internas) {
                    $('#ver_notas_internas').text(d.notas_internas);
                    $('#ver_notas_internas_container').show();
                } else {
                    $('#ver_notas_internas_container').hide();
                }

                // Botones de acción según estado
                var accionesHtml = '';
                if (d.estado === 'pendiente') {
                    accionesHtml += '<button type="button" class="btn btn-success btn-aprobar" data-id="' + d.id_devolucion + '"><i class="fas fa-check"></i> Aprobar</button> ';
                    accionesHtml += '<button type="button" class="btn btn-danger btn-rechazar" data-id="' + d.id_devolucion + '"><i class="fas fa-times"></i> Rechazar</button> ';
                }
                if (d.estado === 'aprobada') {
                    accionesHtml += '<button type="button" class="btn btn-info btn-completar" data-id="' + d.id_devolucion + '"><i class="fas fa-check-double"></i> Completar</button> ';
                }
                if (d.estado === 'pendiente' || d.estado === 'aprobada') {
                    accionesHtml += '<button type="button" class="btn btn-warning btn-cancelar" data-id="' + d.id_devolucion + '"><i class="fas fa-ban"></i> Cancelar</button> ';
                }
                $('#ver_acciones_estado').html(accionesHtml);

                // Guardar id para botones del footer
                $('#btnPdfDesdeVer').data('id', d.id_devolucion);

                abrirOverlay('overlayVerDevolucion');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la devolución', 'error');
            }
        });
    });

    // ================================================
    // EDITAR DEVOLUCIÓN
    // ================================================
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/devoluciones/' + id,
            type: 'GET',
            success: function(d) {
                resetForm();
                initSelect2Cliente();
                initSelect2Venta();
                initSelect2Producto();

                $('.modal-title').first().html('<i class="fas fa-edit"></i> Editar Devolución');
                $('#formDevolucion').data('modo', 'editar').data('id', id);
                $('#btnGuardarDevolucion').find('#texto_btn_guardar').text('Actualizar Devolución');

                $('#numero_devolucion').val(d.numero_devolucion);
                $('#fecha_devolucion').val(d.fecha_devolucion ? d.fecha_devolucion.substring(0, 10) : '');
                $('#tipo_devolucion').val(d.tipo_devolucion);
                $('#motivo').val(d.motivo).trigger('change');
                if (d.motivo === 'otro') {
                    $('#motivo_descripcion').val(d.motivo_descripcion);
                }
                $('#metodo_reembolso').val(d.metodo_reembolso);
                $('#reingresar_inventario').prop('checked', d.reingresar_inventario);
                $('#observaciones').val(d.observaciones || '');

                if (d.cliente) {
                    var opt = new Option(d.cliente.nombre + ' - ' + d.cliente.cedula, d.cliente.id_cliente, true, true);
                    $('#id_cliente').append(opt).trigger('change');
                    $('#cliente_nombre').val(d.cliente.nombre).prop('readonly', true);
                    $('#cliente_cedula').val(d.cliente.cedula).prop('readonly', true);
                    $('#cliente_telefono').val(d.cliente.telefono || '').prop('readonly', false);
                    $('#cliente_email').val(d.cliente.email || '').prop('readonly', false);
                } else if (d.cliente_nombre) {
                    $('#cliente_general').prop('checked', true).trigger('change');
                    $('#cliente_nombre').val(d.cliente_nombre).prop('readonly', false);
                    $('#cliente_cedula').val(d.cliente_cedula || '0000000000').prop('readonly', false);
                    $('#cliente_telefono').val(d.cliente_telefono || '').prop('readonly', false);
                    $('#cliente_email').val(d.cliente_email || '').prop('readonly', false);
                }

                if (d.detalles && d.detalles.length) {
                    d.detalles.forEach(function(det) {
                        agregarRenglonProducto({
                            id:     det.id_producto,
                            text:   det.nombre_producto || (det.producto ? det.producto.nombre : ''),
                            precio: det.precio_unitario,
                            stock:  det.producto ? det.producto.stock_actual : 0,
                            codigo: det.codigo_producto || (det.producto ? det.producto.codigo : '')
                        }, true);
                        var $lastRow = $('#tbody-productos .fila-producto').last();
                        $lastRow.find('.cantidad').val(parseInt(det.cantidad_devuelta));
                        $lastRow.find('.descuento').val(det.descuento || 0);
                        $lastRow.find('.condicion-producto').val(det.condicion_producto);
                        calcularFila($lastRow);
                    });
                }

                abrirOverlay('overlayCrearDevolucion');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la devolución', 'error');
            }
        });
    });

    // ================================================
    // APROBAR / RECHAZAR / COMPLETAR / CANCELAR
    // ================================================
    $(document).on('click', '.btn-aprobar', function() {
        var id = $(this).data('id');
        $('#accion_id').val(id);
        $('#accion_tipo').val('aprobar');
        $('#modal_accion_titulo').text('Aprobar Devolución');
        $('#motivo_rechazo_group').hide();
        $('#confirmacion_aprobar_group').show();
        abrirOverlay('overlayAprobarRechazar');
    });

    $(document).on('click', '.btn-rechazar', function() {
        var id = $(this).data('id');
        $('#accion_id').val(id);
        $('#accion_tipo').val('rechazar');
        $('#modal_accion_titulo').text('Rechazar Devolución');
        $('#motivo_rechazo_group').show();
        $('#confirmacion_aprobar_group').hide();
        $('#motivo_rechazo').val('');
        abrirOverlay('overlayAprobarRechazar');
    });

    $(document).on('click', '.btn-completar', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Completar Devolución',
            text: '¿Está seguro de marcar esta devolución como completada?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, completar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + '/devoluciones/' + id + '/completar',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(r) {
                        if (r.success) {
                            cerrarOverlay('overlayVerDevolucion');
                            tablaDevoluciones.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Completada', text: r.message, timer: 1500, showConfirmButton: false });
                        } else {
                            Swal.fire('Error', r.message, 'error');
                        }
                    },
                    error: function() { Swal.fire('Error', 'No se pudo completar la devolución', 'error'); }
                });
            }
        });
    });

    $(document).on('click', '.btn-cancelar', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Cancelar Devolución',
            text: '¿Está seguro de cancelar esta devolución?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + '/devoluciones/' + id + '/cancelar',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(r) {
                        if (r.success) {
                            cerrarOverlay('overlayVerDevolucion');
                            tablaDevoluciones.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Cancelada', text: r.message, timer: 1500, showConfirmButton: false });
                        } else {
                            Swal.fire('Error', r.message, 'error');
                        }
                    },
                    error: function() { Swal.fire('Error', 'No se pudo cancelar la devolución', 'error'); }
                });
            }
        });
    });

    $('#btnConfirmarAccion').on('click', function() {
        var id     = $('#accion_id').val();
        var tipo   = $('#accion_tipo').val();
        var motivo = $('#motivo_rechazo').val();

        if (tipo === 'rechazar' && !motivo) {
            Swal.fire('Atención', 'Debe indicar el motivo del rechazo', 'warning');
            return;
        }

        var url = tipo === 'aprobar' 
            ? APP_URL + '/devoluciones/' + id + '/aprobar'
            : APP_URL + '/devoluciones/' + id + '/rechazar';

        var data = { _token: $('meta[name="csrf-token"]').attr('content') };
        if (tipo === 'rechazar') {
            data.motivo_rechazo = motivo;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(r) {
                if (r.success) {
                    cerrarOverlay('overlayAprobarRechazar');
                    cerrarOverlay('overlayVerDevolucion');
                    tablaDevoluciones.ajax.reload();
                    Swal.fire({ icon: 'success', title: tipo === 'aprobar' ? 'Aprobada' : 'Rechazada', 
                               text: r.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire('Error', r.message, 'error');
                }
            },
            error: function() { 
                Swal.fire('Error', 'No se pudo ' + (tipo === 'aprobar' ? 'aprobar' : 'rechazar') + ' la devolución', 'error'); 
            }
        });
    });

    // ================================================
    // BOTÓN PDF
    // ================================================
    function abrirPDF(id) {
        var url = APP_URL + '/devoluciones/' + id + '/pdf';
        $('#pdfIframe').attr('src', url);
        $('#btnDescargarPDF').attr('href', url + '?download=1');
        abrirOverlay('overlayVerPDF');
    }

    $(document).on('click', '.btn-pdf', function() {
        abrirPDF($(this).data('id'));
    });

    $('#btnPdfDesdeVer').on('click', function() {
        var id = $(this).data('id');
        if (id) abrirPDF(id);
    });

    $(document).on('click', '[data-overlay="overlayVerPDF"]', function() {
        setTimeout(function() { $('#pdfIframe').attr('src', ''); }, 300);
    });

    // ================================================
    // ELIMINAR DEVOLUCIÓN
    // ================================================
    $(document).on('click', '.btn-eliminar', function() {
        var id     = $(this).data('id');
        var numero = $(this).data('numero');
        Swal.fire({
            title: '¿Eliminar devolución?',
            html: 'La devolución <strong>' + numero + '</strong> será eliminada permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + '/devoluciones/' + id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(r) {
                        if (r.success) {
                            tablaDevoluciones.ajax.reload();
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