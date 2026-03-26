@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cash-register"></i> Punto de Venta</h1>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary btn-sm" id="btnAtajos">
                <i class="fas fa-keyboard"></i> Atajos (F1)
            </button>
            <span class="badge bg-info text-lg">Factura # <span id="numeroFactura">F-00001</span></span>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- COLUMNA IZQUIERDA: Búsqueda y Productos -->
    <div class="col-lg-7">
     <!-- Información del Cliente -->
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-user"></i> Cliente</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool text-danger" id="btnQuitarCliente" title="Quitar Cliente" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                       <!-- Select de Clientes -->
                        <div class="form-group">
                           
                            <select id="selectCliente" name="cliente_id" class="form-control" required>
                                <option value="">-- Seleccionar Cliente --</option>
                            </select>
                            <!-- El div con la información se creará automáticamente aquí -->                            
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modalNuevoCliente">
                            <i class="fas fa-user-plus"></i> Nuevo
                        </button>
                    </div>
                </div>
                
               
            </div>
        </div>

        <!-- Búsqueda de Productos -->
        <div class="card card-success card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-search"></i> Buscar Productos</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-outline-primary" id="btnOpenScanner">
                        <i class="fas fa-camera"></i> Escanear
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="busquedaRapida" placeholder="Escribe código, nombre o categoría..." autocomplete="off">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="btnBuscarRapido">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Filtros Rápidos por Categoría -->
                <div class="row mb-3" id="filtrosCategoria">
                    <div class="col-12">
                        <div class="btn-group btn-group-sm flex-wrap" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-categoria="todas">Todas</button>
                            <button type="button" class="btn btn-outline-secondary" data-categoria="Herramientas">Herramientas</button>
                            <button type="button" class="btn btn-outline-secondary" data-categoria="Materiales">Materiales</button>
                            <button type="button" class="btn btn-outline-secondary" data-categoria="Fijaciones">Fijaciones</button>
                            <button type="button" class="btn btn-outline-secondary" data-categoria="Pinturas">Pinturas</button>
                            <button type="button" class="btn btn-outline-secondary" data-categoria="Electricidad">Electricidad</button>
                        </div>
                    </div>
                </div>
                
                <!-- Resultados en Tiempo Real -->
                <div class="table-responsive mt-3" style="max-height: 300px;">
                    <table class="table table-sm table-hover" id="tablaProductosBusqueda">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="resultadosProductos">
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-search"></i> Escribe para buscar productos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Productos Frecuentes -->
        <div class="card card-info card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-star"></i> Productos Frecuentes</h3>
                <button class="btn btn-sm btn-outline-secondary" onclick="recargarFrecuentes()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row" id="productosFrecuentes">
                    <!-- Los productos frecuentes se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA: Carrito y Totales -->
   <div class="col-lg-5">
    
    <div class="card card-warning card-outline card-carrito-contenedor">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Carrito</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-danger" id="btnLimpiarCarrito" title="Limpiar Carrito">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 250px;">
                <table class="table table-sm table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th width="40%">Producto</th>
                            <th width="20%">Cant.</th>
                            <th width="20%">Total</th>
                            <th width="20%"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsCarrito">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>
                                Carrito vacío
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>

        <!-- Totales y Pago -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator"></i> Resumen de Venta</h3>
            </div>
            <div class="card-body">
                <!-- Selección de IVA -->
                <div class="form-group">
                    <label>IVA</label>
                    <select class="form-control" id="selectIva">
                        <option value="0">Sin IVA (0%)</option>
                        <option value="8">IVA Reducido (8%)</option>
                        <option value="16" selected>IVA Normal (16%)</option>
                        <option value="19">IVA Colombia (19%)</option>
                    </select>
                </div>

          <!-- Totales - CORREGIDO: Sin signo $ y alineados a la derecha -->
                <table class="table table-sm">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-right"><span id="subtotalVenta">0</span></td>
                    </tr>
                    <tr>
                        <td><strong>IVA (<span id="porcentajeIva">16</span>%):</strong></td>
                        <td class="text-right"><span id="ivaVenta">0</span></td>
                    </tr>
                    <tr class="table-success">
                        <td><h4><strong>TOTAL:</strong></h4></td>
                        <td class="text-right"><h3><strong id="totalVenta">0</strong></h3></td>
                    </tr>
                </table>
                <!-- Método de Pago -->
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select class="form-control" id="metodoPago">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="mixto">Pago Mixto</option>
                        <option value="credito">Crédito</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <!-- Pago en Efectivo -->
                <div id="pagoEfectivo" class="metodo-pago-detalle">
                    <div class="form-group">
                        <label>Efectivo Recibido</label>
                        <input type="number" class="form-control form-control-lg" id="efectivoRecibido" step="1" min="0" value="0">
                    </div>
                    <div class="alert alert-success">
                        <strong>Cambio:</strong> <span id="cambioVenta" class="h4">$0</span>
                    </div>
                </div>

                <!-- Pago con Tarjeta -->
                <div id="pagoTarjeta" class="metodo-pago-detalle d-none">
                    <div class="form-group">
                        <label>Número de Tarjeta</label>
                        <input type="text" class="form-control" id="numeroTarjeta" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input type="text" class="form-control" id="fechaVencimiento" placeholder="MM/AA" maxlength="5">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" class="form-control" id="cvvTarjeta" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nombre del Titular</label>
                        <input type="text" class="form-control" id="nombreTitular" placeholder="Como aparece en la tarjeta">
                    </div>
                </div>

                <!-- Pago Mixto -->
                <div id="pagoMixto" class="metodo-pago-detalle d-none">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Efectivo</label>
                                <input type="number" class="form-control" id="montoEfectivoMixto" step="1" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Tarjeta</label>
                                <input type="number" class="form-control" id="montoTarjetaMixto" step="1" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Total Mixto:</strong> <span id="totalMixto" class="h5">$0</span>
                    </div>
                </div>

                <!-- Referencia de Pago (Transferencia/Cheque) -->
                <div id="referenciaPago" class="metodo-pago-detalle d-none">
                    <div class="form-group">
                        <label>Referencia/Autorización</label>
                        <input type="text" class="form-control" id="referenciaTransaccion" placeholder="Número de autorización">
                    </div>
                </div>

                <!-- Tipo de Comprobante -->
                <div class="form-group">
                    <label>Tipo de Comprobante</label>
                    <select class="form-control" id="tipoComprobante">
                        <option value="ticket">Ticket (80mm)</option>
                        <option value="factura">Factura Carta</option>                        
                    </select>
                </div>

                <!-- Botones de Acción -->
                <div class="row mt-3">
                    <div class="col-4">
                        <button type="button" class="btn btn-danger btn-block" id="btnCancelar">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-info btn-block" id="btnImprimirDirecto">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-success btn-block btn-md" id="btnProcesarVenta">
                            <i class="fas fa-check"></i> COBRAR
                        </button>
                    </div>
                </div>

                <!-- Atajos Rápidos -->
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <kbd>F1</kbd> Ayuda | <kbd>F2</kbd> Buscar | <kbd>F3</kbd> Cobrar | <kbd>F9</kbd> Limpiar
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal atajos -->
<div class="modal fade" id="modalAtajos" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="fas fa-keyboard"></i> Atajos de Teclado</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Navegación</h6>
                        <ul class="list-unstyled">
                            <li><kbd>F1</kbd> - Mostrar atajos</li>
                            <li><kbd>F2</kbd> - Buscar producto</li>
                            <li><kbd>F3</kbd> - Procesar venta</li>
                            <li><kbd>F9</kbd> - Limpiar carrito</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Acciones Rápidas</h6>
                        <ul class="list-unstyled">
                            <li><kbd>Ctrl + N</kbd> - Nueva venta</li>
                            <li><kbd>Ctrl + B</kbd> - Buscar</li>
                            <li><kbd>Ctrl + P</kbd> - Imprimir</li>
                            <li><kbd>Esc</kbd> - Cancelar</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Scanner  -->
<div class="modal fade" id="modalScanner" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> Escanear Código
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="areaScanner" style="width: 100%; height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <div class="text-muted">
                        <i class="fas fa-camera fa-3x mb-2"></i>
                        <p>Área de escaneo</p>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <input type="text" 
                           class="form-control form-control-lg text-center" 
                           id="inputCodigoManual" 
                           placeholder="Ingresa código manualmente"
                           autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnProcesarCodigo">
                    <i class="fas fa-check"></i> Procesar Código
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente  -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                
                <form method="POST" id="form_guardar_cliente" action="{{ route('venta') }}">
                    @csrf
                    
                    <!-- Campo userId oculto -->
                    <input type="hidden" name="userId" value="{{ Auth::check() ? Auth::user()->id : 1 }}">
                     <input type="hidden" name="estado" value="activo">

                    <input type="hidden" id="cliente_nombre" name="cliente_nombre">
                    <input type="hidden" id="cliente_cedula" name="cliente_cedula">
                    <input type="hidden" id="cliente_email" name="cliente_email">
                    <input type="hidden" id="cliente_direccion" name="cliente_direccion">
                    <input type="hidden" id="cliente_telefono" name="cliente_telefono">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre/Razón Social *</label>
                                <input type="text" class="form-control" name="nombre" required 
                                       placeholder="Ingrese nombre completo o razón social">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cédula/NIT</label>
                                <input type="text" class="form-control" name="cedula"  id="cedula"
                                       placeholder="Ingrese cédula">
                                       <span id="error_cedula"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" 
                                       placeholder="correo@ejemplo.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" name="telefono" 
                                       placeholder="(555) 123-4567">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Dirección</label>
                                <textarea class="form-control" name="direccion" rows="2" 
                                          placeholder="Ingrese dirección completa"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mensaje de empresa -->
                    <div class="mt-3">
                       <p class="text-muted">
                        * Nombre de la empresa
                      </p>                    
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-success" id="BtnGuardar_cliente">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Vista Previa de Impresión  -->

<div class="modal fade" id="modalVistaPrevia" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-receipt mr-2"></i> Comprobante de Venta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- CONTENEDOR PARA EL COMPROBANTE -->
                <div id="vistaPreviaComprobante"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-success" id="btnImprimir">
                    <i class="fas fa-print mr-1"></i> Imprimir
                </button>
                <button type="button" class="btn btn-primary" id="btnNuevaVenta">
                    <i class="fas fa-plus-circle mr-1"></i> Nueva Venta
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .small-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .small-box .inner {
        text-align: center;
        width: 100%;
        padding: 5px;
    }

    .small-box .icon {
        font-size: 1.2rem;
        margin-bottom: 0.3rem;
    }

    .small-box h6 {
        font-size: 0.75rem;
        margin: 0;
        font-weight: 600;
    }

    .producto-card {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .producto-card:hover {
        border-color: #007bff;
        box-shadow: 0 2px 5px rgba(0,123,255,0.3);
    }

    .input-cantidad {
        width: 20px !important;
        text-align: center;
    }

    .btn-cantidad {
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .stock-bajo { color: #dc3545; font-weight: bold; }
    .stock-normal { color: #28a745; }
    .stock-critico { background-color: #f8d7da; color: #721c24; }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,0.1);
        border-radius: 0.25rem;
    }

    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
    }

    /* Select2 personalizado */
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        height: 38px;
        padding: 6px 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
    }

    /* Estilos para comprobantes */
    .comprobante-ticket {
        width: 80mm; 
        font-family: 'Courier New', monospace; 
        font-size: 12px;
        margin: 0 auto;
    }

    .comprobante-factura {
        font-family: Arial, sans-serif;
    }

    /* Alineación del carrito con cliente */
    .card.card-warning.card-outline {
        margin-top: 0 !important;
    }

/* CORRECCIÓN: Alinear carrito con cliente */
.card-carrito-contenedor {
    margin-top: 0 !important;
    align-self: flex-start;
}

.toast {
  opacity: 1 !important;
}

/* CORRECCIÓN: Alinear botones a la derecha */
.card-header .card-tools {
    margin-left: auto;
}

/* CORRECCIÓN: Alineación de columnas en factura */
.comprobante-factura table td:nth-child(1) { /* Cantidad */
    text-align: center !important;
}

.comprobante-factura table td:nth-child(3), /* P.Unit */
.comprobante-factura table td:nth-child(4) { /* Total */
    text-align: right !important;
}

/* CORRECCIÓN: Sin decimales en todo el sistema */
.input-cantidad,
.metodo-pago-detalle input,
.comprobante-factura td,
.comprobante-ticket td {
    font-feature-settings: "tnum";
    font-variant-numeric: tabular-nums;
}

/* CORRECCIÓN: Totales alineados a la derecha en factura */
.comprobante-factura .table-bordered td:last-child {
    text-align: right !important;
    font-weight: bold;
}
 

/* Remover colores de Select2 */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #f8f9fa !important;
    color: #495057 !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

.select2-result-cliente {
    padding: 6px 12px;
    border-bottom: 1px solid #eee;
}

.select2-result-cliente__nombre {
    font-weight: 500;
    font-size: 14px;
    color: #333;
}

.select2-result-cliente__info {
    font-size: 12px;
    color: #666;
}

/* Info del cliente seleccionado */
#infoClienteSeleccionado {
    border: 1px solid #dee2e6;
    background-color: #f8f9fa;
    font-size: 14px;
}

/* Estilos para el card de información del cliente */
#infoClienteSeleccionado {
    animation: fadeInUp 0.4s ease-out;
}

#infoClienteSeleccionado .badge {
    font-size: 0.85rem;
    padding: 0.4em 0.65em;
    font-weight: 500;
}

#infoClienteSeleccionado .font-weight-bold {
    color: #2c3e50;
    font-size: 0.95rem;
}

/* Estilos para el modal scanner */
#modalScanner .modal-body {
    padding: 20px;
}

#areaScanner {
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

#areaScanner:hover {
    background: #e9ecef;
    border-color: #6c757d;
}

#inputCodigoManual {
    font-size: 18px;
    text-align: center;
    letter-spacing: 2px;
}

/* Estilos para el botón de cerrar cliente */
#btnQuitarClienteInfo {
    padding: 2px 6px;
    font-size: 12px;
}

#infoClienteSeleccionado {
    transition: all 0.3s ease;
}

#infoClienteSeleccionado:hover {
    background-color: #f8f9fa;
    border-color: #dc3545;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```


   /* Estilos de debug */
    .select2-container {
        border: 2px solid #007bff !important;
    }
    
    .select2-selection {
        background-color: #f8f9fa !important;
    }
    
    #infoClienteSeleccionado {
        border: 2px dashed #28a745 !important;
        min-height: 50px;
    }
    
    /* Resaltar elementos importantes */
    #selectCliente {
        border: 1px solid #dc3545 !important;
    }


/* Estilos para el formulario de nuevo cliente */
#modalNuevoCliente .form-control.is-invalid {
    border-color: #dc3545;
}

#modalNuevoCliente .text-danger {
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

#modalNuevoCliente .text-success {
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

/* Botón de guardar deshabilitado */
#BtnGuardar_cliente:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}


</style>

@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>


<script>

    (function($) {
    'use strict';

    console.log('🚀 Punto de Venta - Sistema cargado');

    // =============================================
    // VARIABLES GLOBALES
    // =============================================
    let productos = {};
    let carrito = [];
    let numeroFactura = generarNumeroFactura();
    let clienteSeleccionado = null;
    let timeoutBusqueda = null;

    // Configurar toastr
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

    // =============================================
    // 1. FUNCIONES AUXILIARES
    // =============================================

    function generarNumeroFactura() {
        let contador = parseInt(localStorage.getItem('contadorFacturas') || 1);
        localStorage.setItem('contadorFacturas', contador + 1);
        return `F-${contador.toString().padStart(5, '0')}`;
    }

    function formatoPuntosMil(numero) {
        const num = Math.round(parseFloat(numero) || 0);
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatoDinero(numero) {
        let num = numero;
        if (typeof numero === 'string') {
            num = parseFloat(numero.toString().replace(/[^\d.-]/g, '')) || 0;
        }
        const entero = Math.round(parseFloat(num) || 0);
        return '$' + entero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function limpiarClienteSeleccionado() {
        console.log('🧹 Limpiando cliente seleccionado');
        $('#selectCliente').val(null).trigger('change');
        $('#infoClienteSeleccionado').remove();
        $('#cliente_nombre, #cliente_cedula, #cliente_email, #cliente_direccion, #cliente_telefono').val('');
        clienteSeleccionado = null;
        $('#btnQuitarCliente').hide();
        console.log('✅ Cliente limpiado completamente');
    }

    // =============================================
    // 2. CARGA DE PRODUCTOS
    // =============================================

    function cargarProductosDesdeDB() {
        console.log('📦 Cargando productos...');
        $.ajax({
            url: '{{ route("productos-todos") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.productos) {
                    productos = {};
                    response.productos.forEach(function(producto) {
                        const id = producto.id_producto || producto.id;
                        productos[id] = {
                            id: id,
                            codigo: producto.codigo || '',
                            nombre: producto.nombre || 'Sin nombre',
                            precio: parseFloat(producto.precio) || 0,
                            stock: parseInt(producto.stock) || 0,
                            categoria: producto.categoria || 'Sin categoría',
                            unidad: producto.unidad || 'unidad',
                            stock_minimo: producto.stock_minimo || 5
                        };
                    });
                    console.log('✅ ' + Object.keys(productos).length + ' productos cargados');
                    inicializarCategorias();
                    cargarProductosFrecuentes();
                    mostrarTodosLosProductos();
                } else {
                    console.error('❌ Error en respuesta:', response);
                    toastr.error('No se pudieron cargar los productos');
                }
            },
            error: function(xhr) {
                console.error('❌ Error AJAX:', xhr);
                toastr.error('Error al conectar con el servidor');
            }
        });
    }

    // =============================================
    // 3. SELECT2 CLIENTES
    // =============================================

    function configurarSelect2Clientes() {
        console.log('👤 Configurando Select2...');
        const selectElement = $('#selectCliente');

        if (selectElement.hasClass('select2-hidden-accessible')) {
            selectElement.select2('destroy');
        }

        selectElement.select2({
            ajax: {
                url: '{{ route("busqueda-clientes") }}',
                method: 'GET',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    const results = data.map(function(cliente) {
                        return {
                            id: cliente.id,
                            text: cliente.nombre + (cliente.cedula ? ' - ' + cliente.cedula : ''),
                            nombre: cliente.nombre,
                            cedula: cliente.cedula,
                            email: cliente.email,
                            telefono: cliente.telefono,
                            direccion: cliente.direccion
                        };
                    });
                    return { results: results };
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error en búsqueda de clientes:', error);
                    return { results: [] };
                }
            },
            placeholder: 'Escribe para buscar cliente...',
            minimumInputLength: 2,
            allowClear: true,
            width: '100%',
            templateResult: function(cliente) {
                if (cliente.loading) return 'Buscando...';
                return $('<div><div>' + cliente.text + '</div></div>');
            },
            templateSelection: function(cliente) {
                if (!cliente.id) return 'Seleccionar cliente...';
                return cliente.text;
            },
            escapeMarkup: function(markup) { return markup; }
        });

        selectElement.on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').focus().select();
            }, 100);
        });

        selectElement.on('select2:select', function(e) {
            const selectedData = e.params.data;
            if (selectedData && selectedData.id) {
                $('#cliente_nombre').val(selectedData.nombre || '');
                $('#cliente_cedula').val(selectedData.cedula || '');
                $('#cliente_email').val(selectedData.email || '');
                $('#cliente_direccion').val(selectedData.direccion || '');
                $('#cliente_telefono').val(selectedData.telefono || '');
                mostrarInfoClienteBasica(selectedData);
                clienteSeleccionado = {
                    id: selectedData.id,
                    nombre: selectedData.nombre,
                    cedula: selectedData.cedula,
                    email: selectedData.email,
                    direccion: selectedData.direccion,
                    telefono: selectedData.telefono
                };
                $('#btnQuitarCliente').show();
                toastr.success('Cliente ' + selectedData.nombre + ' seleccionado');
            }
        });

        selectElement.on('select2:clear', function(e) {
            e.preventDefault();
            limpiarClienteSeleccionado();
            toastr.info('Cliente removido');
        });
    }

    // =============================================
    // 4. INFO CLIENTE
    // =============================================

    function mostrarInfoClienteBasica(clienteData) {
        $('#infoClienteSeleccionado').remove();
        const infoHtml = `
            <div id="infoClienteSeleccionado" class="mt-2 p-2 bg-light rounded border">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="font-weight-bold text-primary">
                            <i class="fas fa-user mr-1"></i>${clienteData.nombre}
                        </span>
                        ${clienteData.cedula ? `<span class="ml-2 text-muted"><i class="fas fa-id-card mr-1"></i>${clienteData.cedula}</span>` : ''}
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnQuitarClienteInfo">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        $('#selectCliente').closest('.form-group').after(infoHtml);
    }

    // =============================================
    // 5. NUEVO CLIENTE
    // =============================================

    function configurarNuevoCliente() {
        console.log('👤 Configurando nuevo cliente...');

        $('#form_guardar_cliente').off('submit');
        $('#BtnGuardar_cliente').off('click');

        // Capturar click del botón (más confiable que submit en AdminLTE)
        $('#BtnGuardar_cliente').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            if ($btn.prop('disabled')) return false;

            const nombre = $('#form_guardar_cliente [name="nombre"]').val().trim();
            if (!nombre) {
                toastr.error('El nombre es obligatorio');
                return false;
            }

            $btn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

            guardarNuevoCliente();
            return false;
        });

        // Bloquear submit tradicional del form
        $('#form_guardar_cliente').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        // Verificar cédula con debounce
        $('#cedula').off('keyup change blur')
            .on('keyup change', function() {
                const cedula = $(this).val().trim();
                clearTimeout(window._timeoutCedula);

                if (!cedula) {
                    $('#error_cedula').html('');
                    $('#cedula').removeClass('is-invalid is-valid');
                    $('#BtnGuardar_cliente').prop('disabled', false);
                    return;
                }

                window._timeoutCedula = setTimeout(function() {
                    verificarCedulaExistente(cedula);
                }, 400);
            })
            .on('blur', function() {
                const cedula = $(this).val().trim();
                if (cedula.length >= 3) verificarCedulaExistente(cedula);
            });

        // Reset al cerrar el modal
        $('#modalNuevoCliente').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            console.log('🔄 Modal cerrado — reseteando');
            resetearFormularioCliente();
        });
    }

    function resetearFormularioCliente() {
        const form = document.getElementById('form_guardar_cliente');
        if (form) form.reset();
        $('#cedula').removeClass('is-invalid is-valid');
        $('#error_cedula').html('');
        $('#BtnGuardar_cliente')
            .prop('disabled', false)
            .html('<i class="fas fa-save"></i> Guardar Cliente');
        if (window.verificarAjax) {
            window.verificarAjax.abort();
            window.verificarAjax = null;
        }
        clearTimeout(window._timeoutCedula);
        console.log('✅ Formulario reseteado');
    }

    function cerrarModalCliente() {
        console.log('🔒 Cerrando modal cliente...');
        const $modal = $('#modalNuevoCliente');
        $modal.modal('hide');

        // Fallback forzado si Bootstrap no cierra el modal
        setTimeout(function() {
            if ($modal.hasClass('show') || $modal.css('display') !== 'none') {
                console.warn('⚠️ Modal aún abierto, forzando cierre...');
                $modal.removeClass('show').css('display', 'none');
                $modal.attr('aria-hidden', 'true').removeAttr('aria-modal');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
                resetearFormularioCliente();
            }
        }, 500);
    }

    function guardarNuevoCliente() {
        const formData = new FormData(document.getElementById('form_guardar_cliente'));
        const data = {};
        formData.forEach(function(value, key) { data[key] = value; });
        console.log('📤 Datos a enviar:', data);

        $.ajax({
            url: '/guardar_clientes',
            method: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                console.log('✅ Respuesta servidor:', response);
                if (response.success) {
                    toastr.success(response.message || 'Cliente creado', 'Éxito');
                    const clienteData = response.data || response.cliente;
                    if (clienteData) agregarClienteAlSelect2(clienteData);
                    cerrarModalCliente();
                } else {
                    toastr.error(response.message || 'Error al guardar', 'Error');
                    $('#BtnGuardar_cliente')
                        .prop('disabled', false)
                        .html('<i class="fas fa-save"></i> Guardar Cliente');
                }
            },
            error: function(xhr) {
                console.error('❌ Error:', xhr.status, xhr.responseText);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(function(msgs) {
                        toastr.error(msgs[0]);
                    });
                } else if (xhr.status === 409) {
                    toastr.error('La cédula ya existe en el sistema');
                    $('#cedula').addClass('is-invalid');
                    $('#error_cedula').html('<span class="text-danger">Cédula duplicada</span>');
                } else {
                    toastr.error('Error de conexión al guardar el cliente');
                }
                $('#BtnGuardar_cliente')
                    .prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Guardar Cliente');
            }
        });
    }

    function verificarCedulaExistente(cedula) {
        const cedulaLimpia = cedula.replace(/[.,\s-]/g, '');
        if (!cedulaLimpia || cedulaLimpia.length < 3) {
            $('#error_cedula').html('<span class="text-info"><i class="fas fa-info-circle"></i> Ingrese al menos 3 dígitos</span>');
            $('#cedula').removeClass('is-invalid is-valid');
            $('#BtnGuardar_cliente').prop('disabled', false);
            return;
        }

        if (window.verificarAjax) window.verificarAjax.abort();

        $('#error_cedula').html('<span class="text-secondary"><i class="fas fa-spinner fa-spin"></i> Verificando...</span>');

        window.verificarAjax = $.ajax({
            url: '/verificar-cliente',
            method: 'GET',
            data: { cedula: cedulaLimpia },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                if (response.exists === true) {
                    $('#error_cedula').html('<span class="text-danger"><i class="fas fa-exclamation-circle"></i> Esta cédula ya existe en el sistema</span>');
                    $('#cedula').addClass('is-invalid').removeClass('is-valid');
                    $('#BtnGuardar_cliente').prop('disabled', true);
                } else {
                    $('#error_cedula').html('<span class="text-success"><i class="fas fa-check-circle"></i> Cédula disponible</span>');
                    $('#cedula').removeClass('is-invalid').addClass('is-valid');
                    $('#BtnGuardar_cliente').prop('disabled', false);
                }
            },
            error: function(xhr, status) {
                if (status === 'abort') return;
                if (status === 'timeout') {
                    $('#error_cedula').html('<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Verificación lenta. Puede continuar.</span>');
                } else {
                    $('#error_cedula').html('<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Error al verificar, puede continuar</span>');
                }
                $('#cedula').removeClass('is-invalid');
                $('#BtnGuardar_cliente').prop('disabled', false);
            },
            complete: function() {
                window.verificarAjax = null;
            }
        });
    }

    function agregarClienteAlSelect2(cliente) {
        const selectElement = $('#selectCliente');
        const nuevaOpcion = new Option(
            cliente.nombre + (cliente.cedula ? ' - ' + cliente.cedula : ''),
            cliente.id, true, true
        );
        selectElement.append(nuevaOpcion).trigger('change');
        selectElement.trigger({
            type: 'select2:select',
            params: {
                data: {
                    id: cliente.id,
                    text: cliente.nombre + (cliente.cedula ? ' - ' + cliente.cedula : ''),
                    nombre: cliente.nombre,
                    cedula: cliente.cedula,
                    email: cliente.email,
                    telefono: cliente.telefono,
                    direccion: cliente.direccion
                }
            }
        });
        mostrarInfoClienteBasica(cliente);
        $('#cliente_nombre').val(cliente.nombre);
        $('#cliente_cedula').val(cliente.cedula);
        $('#cliente_email').val(cliente.email);
        $('#cliente_direccion').val(cliente.direccion);
        $('#cliente_telefono').val(cliente.telefono);
        clienteSeleccionado = cliente;
        $('#btnQuitarCliente').show();
    }

    // =============================================
    // 6. PRODUCTOS Y CATEGORÍAS
    // =============================================

    function mostrarTodosLosProductos() {
        const todosProductos = Object.values(productos);
        if (todosProductos.length === 0) {
            $('#resultadosProductos').html(`
                <tr><td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-box fa-3x mb-3"></i>
                    <h5>No hay productos disponibles</h5>
                </td></tr>`);
        } else {
            mostrarResultadosBusqueda(todosProductos);
        }
    }

    function inicializarCategorias() {
        const categorias = new Set();
        Object.values(productos).forEach(function(p) {
            if (p.categoria && p.categoria.trim()) categorias.add(p.categoria);
        });

        const botonesContainer = $('#filtrosCategoria .btn-group');
        botonesContainer.empty();
        botonesContainer.append('<button type="button" class="btn btn-outline-primary active" data-categoria="todas">Todas</button>');

        Array.from(categorias).sort().forEach(function(categoria) {
            botonesContainer.append(
                `<button type="button" class="btn btn-outline-secondary" data-categoria="${categoria}">${categoria}</button>`
            );
        });

        botonesContainer.find('button').on('click', function() {
            botonesContainer.find('button').removeClass('active btn-primary').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('active btn-primary');
            filtrarProductosPorCategoria($(this).data('categoria'));
        });
    }

    function configurarBusquedaTiempoReal() {
        $('#busquedaRapida').on('input', function() {
            const termino = $(this).val().trim();
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(function() {
                if (termino.length >= 2) buscarProductos(termino);
                else if (termino.length === 0) mostrarTodosLosProductos();
            }, 300);
        });

        $('#btnBuscarRapido').on('click', function() {
            const termino = $('#busquedaRapida').val().trim();
            if (termino.length >= 2) buscarProductos(termino);
            else mostrarTodosLosProductos();
        });

        $('#busquedaRapida').on('keypress', function(e) {
            if (e.which === 13) {
                const termino = $(this).val().trim();
                if (termino.length >= 2) buscarProductos(termino);
                else mostrarTodosLosProductos();
            }
        });
    }

    function buscarProductos(termino) {
        const terminoLower = termino.toLowerCase();
        const resultados = Object.values(productos).filter(function(p) {
            return (p.codigo && p.codigo.toLowerCase().includes(terminoLower)) ||
                   (p.nombre && p.nombre.toLowerCase().includes(terminoLower)) ||
                   (p.categoria && p.categoria.toLowerCase().includes(terminoLower));
        });
        mostrarResultadosBusqueda(resultados);
    }

    function filtrarProductosPorCategoria(categoria) {
        const productosFiltrados = categoria === 'todas'
            ? Object.values(productos)
            : Object.values(productos).filter(function(p) { return p.categoria === categoria; });
        mostrarResultadosBusqueda(productosFiltrados);
        $('#busquedaRapida').val('');
        toastr.info(productosFiltrados.length + ' productos en ' + (categoria === 'todas' ? 'todas las categorías' : categoria));
    }

    function mostrarResultadosBusqueda(resultados) {
        const tbody = $('#resultadosProductos');
        tbody.empty();

        if (resultados.length === 0) {
            tbody.append(`<tr><td colspan="5" class="text-center text-muted py-5">
                <i class="fas fa-search fa-3x mb-3"></i><h5>No se encontraron productos</h5>
            </td></tr>`);
            return;
        }

        resultados.forEach(function(producto) {
            const precio = parseFloat(producto.precio) || 0;
            const stock = parseInt(producto.stock) || 0;
            const claseStock = stock <= 5 ? 'text-danger font-weight-bold' :
                               stock <= 10 ? 'text-warning font-weight-bold' : 'text-success';
            tbody.append(`
                <tr class="producto-fila" style="cursor:pointer;">
                    <td class="align-middle"><small class="text-muted font-weight-bold">${producto.codigo || 'N/A'}</small></td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;">
                                <i class="fas fa-box text-primary"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold text-dark">${producto.nombre}</div>
                                <small class="text-muted">${producto.categoria || 'Sin categoría'}</small>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle font-weight-bold text-success">${formatoDinero(precio)}</td>
                    <td class="align-middle ${claseStock}">
                        ${stock}${stock <= 5 ? '<br><small class="badge badge-danger">Stock bajo</small>' : ''}
                    </td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-success btn-agregar"
                            data-id="${producto.id}" data-nombre="${producto.nombre}"
                            data-precio="${producto.precio}" data-stock="${producto.stock}"
                            data-codigo="${producto.codigo || ''}">
                            <i class="fas fa-cart-plus"></i> Agregar
                        </button>
                    </td>
                </tr>`);
        });

        $('.btn-agregar').off('click').on('click', function(e) {
            e.stopPropagation();
            const producto = productos[$(this).data('id')];
            if (producto) agregarAlCarrito(producto);
            else toastr.error('Producto no encontrado');
        });

        $('.producto-fila').off('click').on('click', function(e) {
            if (!$(e.target).closest('.btn-agregar').length) {
                const producto = productos[$(this).find('.btn-agregar').data('id')];
                if (producto) agregarAlCarrito(producto);
            }
        });
    }

    // =============================================
    // 7. CARRITO
    // =============================================

    function agregarAlCarrito(producto) {
        if (!producto || producto.stock <= 0) {
            toastr.error('Producto sin stock disponible');
            return;
        }
        const productoEnCarrito = carrito.find(function(item) { return item.id === producto.id; });
        if (productoEnCarrito) {
            if (productoEnCarrito.cantidad >= producto.stock) {
                toastr.error('No hay suficiente stock');
                return;
            }
            productoEnCarrito.cantidad++;
        } else {
            carrito.push({
                id: producto.id, nombre: producto.nombre, precio: producto.precio,
                cantidad: 1, stock: producto.stock, codigo: producto.codigo, categoria: producto.categoria
            });
        }
        actualizarCarrito();
        actualizarMetricas();
    }

    function actualizarCarrito() {
        const tbody = $('#itemsCarrito');
        tbody.empty();

        if (carrito.length === 0) {
            tbody.html(`<tr><td colspan="4" class="text-center text-muted py-3">
                <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>Carrito vacío
            </td></tr>`);
            actualizarTotales();
            return;
        }

        let subtotal = 0;
        carrito.forEach(function(item, index) {
            const itemSubtotal = item.precio * item.cantidad;
            subtotal += itemSubtotal;
            tbody.append(`
                <tr>
                    <td class="align-middle">
                        <div class="font-weight-bold">${item.nombre}</div>
                        <small class="text-muted">${item.codigo}</small>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-outline-secondary btn-sm btn-restar mr-1" data-index="${index}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="input-group" style="width:90px;">
                                <input type="number" class="form-control text-center cantidad-input"
                                    value="${item.cantidad}" min="1" max="${item.stock}"
                                    data-index="${index}" style="height:31px;">
                            </div>
                            <button class="btn btn-outline-secondary btn-sm btn-sumar ml-1" data-index="${index}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="align-middle font-weight-bold">
                        ${formatoDinero(item.precio)}<br>
                        <small class="text-success">Subtotal: ${formatoDinero(itemSubtotal)}</small>
                    </td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-danger btn-eliminar" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`);
        });

        actualizarTotales(subtotal);
        configurarEventosCarrito();
    }

    function configurarEventosCarrito() {
        $('.btn-sumar').off('click').on('click', function() {
            const index = $(this).data('index');
            const item = carrito[index];
            if (item.cantidad < item.stock) {
                item.cantidad++;
                actualizarCarrito();
                actualizarMetricas();
                toastr.info(item.nombre + ': ' + item.cantidad + ' unidades');
            } else {
                toastr.error('Stock insuficiente');
            }
        });

        $('.btn-restar').off('click').on('click', function() {
            const index = $(this).data('index');
            const item = carrito[index];
            if (item.cantidad > 1) {
                item.cantidad--;
            } else {
                carrito.splice(index, 1);
                toastr.info('Producto eliminado');
                actualizarCarrito();
                actualizarMetricas();
                return;
            }
            actualizarCarrito();
            actualizarMetricas();
            toastr.info(item.nombre + ': ' + item.cantidad + ' unidades');
        });

        $('.btn-eliminar').off('click').on('click', function() {
            const index = $(this).data('index');
            const nombre = carrito[index].nombre;
            carrito.splice(index, 1);
            actualizarCarrito();
            actualizarMetricas();
            toastr.info(nombre + ' eliminado');
        });

        $('.cantidad-input').off('change').on('change', function() {
            const index = $(this).data('index');
            const item = carrito[index];
            const nuevaCantidad = parseInt($(this).val());
            if (nuevaCantidad >= 1 && nuevaCantidad <= item.stock) {
                item.cantidad = nuevaCantidad;
                actualizarCarrito();
                actualizarMetricas();
            } else if (nuevaCantidad > item.stock) {
                $(this).val(item.cantidad);
                toastr.error('Stock máximo: ' + item.stock + ' unidades');
            } else {
                $(this).val(item.cantidad);
            }
        });
    }

    function actualizarTotales(subtotal) {
        subtotal = subtotal || 0;
        const ivaPorcentaje = parseFloat($('#selectIva').val()) || 0;
        const iva = subtotal * (ivaPorcentaje / 100);
        const total = subtotal + iva;

        window.ventaSubtotalNumerico = Math.round(subtotal);
        window.ventaIvaNumerico = Math.round(iva);
        window.ventaTotalNumerico = Math.round(total);

        $('#subtotalVenta').text(formatoPuntosMil(Math.round(subtotal)));
        $('#ivaVenta').text(formatoPuntosMil(Math.round(iva)));
        $('#totalVenta').text(formatoDinero(Math.round(total)));
        $('#porcentajeIva').text(ivaPorcentaje + '%');

        if ($('#metodoPago').val() === 'efectivo') calcularCambio();
        if ($('#metodoPago').val() === 'mixto') calcularTotalMixto();
    }

    function actualizarMetricas() {
        let totalProductos = 0, totalVenta = 0;
        carrito.forEach(function(item) {
            totalProductos += item.cantidad;
            totalVenta += item.precio * item.cantidad;
        });
        $('#metricTotalProductos').text(totalProductos);
        $('#metricVentaActual').text(formatoDinero(Math.round(totalVenta)));
    }

    // =============================================
    // 8. PAGOS Y CAMBIO
    // =============================================

    function calcularCambio() {
        const total = parseFloat(window.ventaTotalNumerico) || 0;
        let efectivoInput = $('#efectivoRecibido').val().toString().replace(/\./g, '').replace(',', '.') || '0';
        const efectivo = parseFloat(efectivoInput) || 0;
        const cambio = Math.round(efectivo - total);
        $('#cambioVenta').text(cambio >= 0 ? formatoDinero(cambio) : '-$' + formatoPuntosMil(Math.abs(cambio)));
        window.cambioNumerico = cambio;
        return cambio;
    }

    function configurarInputEfectivo() {
        $('#efectivoRecibido').on('input', function() {
            let valor = $(this).val().replace(/[^\d]/g, '');
            $(this).val(valor && valor !== '0' ? formatoPuntosMil(parseInt(valor)) : '0');
            calcularCambio();
        }).on('focus', function() {
            $(this).select();
        }).on('blur', function() {
            if (!$(this).val() || $(this).val() === '0') {
                $(this).val('0');
                calcularCambio();
            }
        });
    }

    function calcularTotalMixto() {
        const efectivo = parseFloat($('#montoEfectivoMixto').val()) || 0;
        const tarjeta = parseFloat($('#montoTarjetaMixto').val()) || 0;
        $('#totalMixto').text(formatoDinero(Math.round(efectivo + tarjeta)));
    }

    function configurarMetodosPago() {
        $('#metodoPago').on('change', function() {
            $('.metodo-pago-detalle').addClass('d-none');
            const metodo = $(this).val();
            const metodoId = '#pago' + metodo.charAt(0).toUpperCase() + metodo.slice(1);
            $(metodoId).removeClass('d-none');
            if (metodo === 'efectivo') calcularCambio();
            else if (metodo === 'mixto') calcularTotalMixto();
        });
        $('#efectivoRecibido').on('input', calcularCambio);
        $('#montoEfectivoMixto, #montoTarjetaMixto').on('input', calcularTotalMixto);
    }

    // =============================================
    // 9. PRODUCTOS FRECUENTES
    // =============================================

    function cargarProductosFrecuentes() {
        const contenedor = $('#productosFrecuentes');
        contenedor.html('<p class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>');
        $.ajax({
            url: '{{ route("productos/frecuentes") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.productos && response.productos.length > 0) {
                    mostrarProductosFrecuentes(response.productos);
                } else {
                    usarFallbackFrecuentes();
                }
            },
            error: function() {
                usarFallbackFrecuentes();
            }
        });
    }

    function usarFallbackFrecuentes() {
        const lista = Object.values(productos).filter(function(p) { return p.stock > 0; }).slice(0, 6);
        if (lista.length > 0) mostrarProductosFrecuentes(lista);
        else $('#productosFrecuentes').html('<p class="text-muted text-center col-12">No hay productos con stock disponible</p>');
    }

    function mostrarProductosFrecuentes(productosFrecuentes) {
        const contenedor = $('#productosFrecuentes');
        contenedor.empty();

        if (!productosFrecuentes || productosFrecuentes.length === 0) {
            contenedor.html('<p class="text-muted text-center col-12">No hay productos frecuentes</p>');
            return;
        }

        productosFrecuentes.forEach(function(producto) {
            const id     = producto.id || producto.id_producto;
            const nombre = producto.nombre || 'Sin nombre';
            const codigo = producto.codigo || 'S/C';
            const precio = parseFloat(producto.precio || producto.precio_venta || 0);
            const stock  = parseInt(producto.stock || producto.stock_actual || 0);
            const claseStock = stock <= 0 ? 'badge-danger' : stock <= 5 ? 'badge-warning' : stock <= 10 ? 'badge-info' : 'badge-success';
            const badgeStock = stock <= 0
                ? '<span class="badge badge-danger">Sin stock</span>'
                : `<span class="badge ${claseStock}">Stock: ${stock}</span>`;

            contenedor.append(`
                <div class="col-6 col-md-4 mb-3">
                    <div class="producto-card h-100" onclick="window.agregarProductoFrecuente(${id})"
                         style="cursor:pointer;min-height:110px;">
                        <div class="text-center">
                            <i class="fas fa-star text-warning mb-1 d-block"></i>
                            <h6 class="mb-1" style="font-size:0.82rem;line-height:1.2;" title="${nombre}">
                                ${nombre.length > 22 ? nombre.substring(0, 22) + '…' : nombre}
                            </h6>
                            <small class="text-muted d-block mb-1">${codigo}</small>
                            <span class="badge badge-success d-block mb-1">${formatoDinero(precio)}</span>
                            ${badgeStock}
                        </div>
                    </div>
                </div>`);
        });
    }

    // =============================================
    // 10. PROCESAR VENTA
    // =============================================

    function obtenerReferenciaPago() {
        const metodo = $('#metodoPago').val();
        switch(metodo) {
            case 'tarjeta':      return $('#numeroTarjeta').val() || 'Tarjeta';
            case 'transferencia': return $('#referenciaTransaccion').val() || 'Transferencia';
            case 'cheque':       return $('#referenciaTransaccion').val() || 'Cheque';
            case 'mixto':        return 'Mixto: Efectivo ' + ($('#montoEfectivoMixto').val() || 0) + ', Tarjeta ' + ($('#montoTarjetaMixto').val() || 0);
            default:             return null;
        }
    }

    $(document).on('click', '#btnProcesarVenta', function(e) {
        e.preventDefault();

        if (!carrito || carrito.length === 0) {
            toastr.error('El carrito está vacío', 'Error');
            return;
        }

        // Validar stock
        let stockValido = true;
        carrito.forEach(function(item) {
            const producto = productos[item.id];
            if (!producto || producto.stock < item.cantidad) {
                toastr.error('Stock insuficiente para ' + item.nombre, 'Error de stock');
                stockValido = false;
            }
        });
        if (!stockValido) return;

        const carritoParaEnviar = JSON.parse(JSON.stringify(carrito));
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfToken) {
            toastr.error('Token de seguridad no encontrado', 'Error');
            return;
        }

        const ventaData = {
            cliente_id: clienteSeleccionado ? clienteSeleccionado.id : null,
            subtotal: Math.round(window.ventaSubtotalNumerico || 0),
            iva: Math.round(window.ventaIvaNumerico || 0),
            total: Math.round(window.ventaTotalNumerico || 0),
            metodo_pago: $('#metodoPago').val() || 'efectivo',
            tipo_comprobante: $('#tipoComprobante').val() || 'ticket',
            referencia_pago: obtenerReferenciaPago(),
            efectivo_recibido: Math.round(parseFloat($('#efectivoRecibido').val().replace(/\./g, '')) || 0),
            cambio: Math.round(parseFloat(window.cambioNumerico) || 0),
            items: carritoParaEnviar.map(function(item) {
                return {
                    producto_id: item.id,
                    cantidad: item.cantidad,
                    precio: Math.round(item.precio),
                    subtotal: Math.round(item.precio * item.cantidad)
                };
            })
        };

        const $btn = $(this);
        const textoOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: '/procesar-venta',
            method: 'POST',
            data: JSON.stringify(ventaData),
            contentType: 'application/json',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            success: function(response) {
                if (response.success) {
                    toastr.success('Venta realizada con éxito');
                    if (response.productos_actualizados && response.productos_actualizados.length > 0) {
                        actualizarProductosLocales(response.productos_actualizados);
                    } else {
                        carritoParaEnviar.forEach(function(item) {
                            if (productos[item.id]) productos[item.id].stock -= item.cantidad;
                        });
                        mostrarTodosLosProductos();
                        cargarProductosFrecuentes();
                    }
                    if (response.venta_completa) mostrarTicketAutomatico(response.venta_completa);
                    else mostrarVistaPrevia(response.numero_factura);
                    setTimeout(reiniciarFormularioVenta, 1000);
                } else {
                    toastr.error(response.message || 'Error al procesar la venta', 'Error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al procesar la venta';
                try {
                    if (xhr.responseText && xhr.responseText.trim().startsWith('{')) {
                        const err = JSON.parse(xhr.responseText);
                        if (err.message) errorMessage = err.message;
                        if (err.errors) {
                            Object.values(err.errors).forEach(function(msgs) { toastr.error(msgs[0]); });
                            return;
                        }
                    } else if (xhr.responseText && xhr.responseText.includes('CSRF')) {
                        errorMessage = 'Error de token CSRF. Recarga la página.';
                    }
                } catch(ex) { /* ignore */ }
                toastr.error(errorMessage, 'Error');
                if (xhr.status === 403) toastr.warning('Error de autenticación. Recarga la página.', 'Token CSRF');
            },
            complete: function() {
                $btn.prop('disabled', false).html(textoOriginal);
            }
        });
    });

    function actualizarProductosLocales(productosActualizados) {
        productosActualizados.forEach(function(p) {
            const id = p.id_producto || p.id;
            if (productos[id]) productos[id].stock = parseInt(p.stock || p.stock_actual || 0);
        });
        const busquedaActual = $('#busquedaRapida').val().trim();
        if (busquedaActual.length >= 2) buscarProductos(busquedaActual);
        else mostrarTodosLosProductos();
        cargarProductosFrecuentes();
    }

    // =============================================
    // 11. TICKET / COMPROBANTE
    // =============================================

    function mostrarTicketAutomatico(datosVenta) {
        if (!datosVenta) { mostrarVistaPrevia(); return; }

        const ventaData = {
            numeroFactura: datosVenta.numero_factura || numeroFactura,
            cliente: datosVenta.cliente ? datosVenta.cliente.nombre : 'Consumidor Final',
            cedula: datosVenta.cliente ? datosVenta.cliente.cedula : 'N/A',
            telefono: datosVenta.cliente ? datosVenta.cliente.telefono : 'N/A',
            subtotal: datosVenta.subtotal || window.ventaSubtotalNumerico || 0,
            iva: datosVenta.iva || window.ventaIvaNumerico || 0,
            total: datosVenta.total || window.ventaTotalNumerico || 0,
            tipo: datosVenta.tipo_comprobante || $('#tipoComprobante').val() || 'ticket',
            fecha: datosVenta.fecha_venta || new Date().toLocaleString(),
            metodoPago: datosVenta.metodo_pago || $('#metodoPago').val(),
            porcentajeIva: parseFloat($('#selectIva').val()) || 16,
            cambio: datosVenta.cambio || 0,
            efectivoRecibido: datosVenta.efectivo_recibido || 0,
            items: (datosVenta.detalles && datosVenta.detalles.length > 0)
                ? datosVenta.detalles.map(function(d) {
                    return {
                        nombre: d.producto ? d.producto.nombre : 'Producto',
                        cantidad: d.cantidad, precio: d.precio_unitario,
                        codigo: d.producto ? d.producto.codigo : ''
                    };
                  })
                : carrito
        };

        $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
        $('#modalVistaPrevia').modal('show');
    }

    function mostrarVistaPrevia(numeroFacturaServidor) {
        if (carrito.length === 0) return;

        let subtotal = window.ventaSubtotalNumerico || 0;
        let iva = window.ventaIvaNumerico || 0;
        let total = window.ventaTotalNumerico || 0;

        if (subtotal === 0 && carrito.length > 0) {
            subtotal = carrito.reduce(function(sum, item) { return sum + item.precio * item.cantidad; }, 0);
            const ivaPorcentaje = parseFloat($('#selectIva').val()) || 0;
            iva = Math.round(subtotal * ivaPorcentaje / 100);
            total = Math.round(subtotal + iva);
        }

        const ventaData = {
            numeroFactura: numeroFactura,
            cliente: clienteSeleccionado ? clienteSeleccionado.nombre : 'Consumidor Final',
            cedula: clienteSeleccionado ? clienteSeleccionado.cedula : 'N/A',
            telefono: clienteSeleccionado ? clienteSeleccionado.telefono : 'N/A',
            items: carrito,
            subtotal: Math.round(subtotal), iva: Math.round(iva), total: Math.round(total),
            tipo: $('#tipoComprobante').val(),
            fecha: new Date().toLocaleString(),
            metodoPago: $('#metodoPago').val(),
            porcentajeIva: parseFloat($('#selectIva').val()) || 0,
            cambio: Math.round(parseFloat(window.cambioNumerico) || 0),
            efectivoRecibido: Math.round(parseFloat(($('#efectivoRecibido').val() || '0').replace(/\./g, '')) || 0)
        };

        $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
        $('#modalVistaPrevia').modal('show');
    }

    function generarComprobanteHTML(ventaData) {
        const esTicket = ventaData.tipo === 'ticket';
        const cedulaCliente = ventaData.cedula || (clienteSeleccionado ? clienteSeleccionado.cedula : 'N/A');
        const cambio = ventaData.cambio || 0;
        const efectivoRecibido = ventaData.efectivoRecibido || 0;

        if (esTicket) {
            return `<div class="comprobante-ticket" style="width:80mm;font-family:'Courier New',monospace;font-size:12px;">
                <div class="text-center">
                    <h4 style="margin:5px 0;font-weight:bold;">FERRETERÍA</h4>
                    <h5 style="margin:3px 0;font-weight:bold;">"EL MARTILLO"</h5>
                    <p style="margin:2px 0;">NIT: FME850301XYZ</p>
                    <p style="margin:2px 0;">Tel: (555) 123-4567</p>
                    <p style="margin:2px 0;">Av. Principal #123</p>
                </div>
                <hr style="border-top:1px dashed #000;margin:8px 0;">
                <div style="margin:5px 0;">
                    <strong>TICKET:</strong> ${ventaData.numeroFactura}<br>
                    <strong>FECHA:</strong> ${ventaData.fecha}<br>
                    <strong>CLIENTE:</strong> ${ventaData.cliente}<br>
                    <strong>CEDULA:</strong> ${cedulaCliente}
                </div>
                <hr style="border-top:1px dashed #000;margin:8px 0;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead><tr>
                        <th style="text-align:left;border-bottom:1px dashed #000;padding:3px 0;">CANT DESC</th>
                        <th style="text-align:right;border-bottom:1px dashed #000;padding:3px 0;">TOTAL</th>
                    </tr></thead>
                    <tbody>${ventaData.items.map(function(item) {
                        return `<tr>
                            <td style="padding:2px 0;">${item.cantidad} x ${item.nombre.substring(0,20)}</td>
                            <td style="text-align:right;padding:2px 0;">${formatoDinero(item.precio * item.cantidad)}</td>
                        </tr>`;
                    }).join('')}</tbody>
                </table>
                <hr style="border-top:1px dashed #000;margin:8px 0;">
                <table style="width:100%;">
                    <tr><td>SUBTOTAL:</td><td style="text-align:right;">${formatoDinero(ventaData.subtotal)}</td></tr>
                    <tr><td>IVA:</td><td style="text-align:right;">${formatoDinero(ventaData.iva)}</td></tr>
                    <tr style="font-weight:bold;"><td>TOTAL:</td><td style="text-align:right;">${formatoDinero(ventaData.total)}</td></tr>
                    ${ventaData.metodoPago === 'efectivo' ? `
                    <tr><td>EFECTIVO:</td><td style="text-align:right;">${formatoDinero(efectivoRecibido)}</td></tr>
                    <tr><td>CAMBIO:</td><td style="text-align:right;">${formatoDinero(cambio)}</td></tr>` : ''}
                </table>
                <hr style="border-top:1px dashed #000;margin:8px 0;">
                <div style="text-align:center;margin:10px 0;">
                    <p style="margin:3px 0;"><strong>PAGO:</strong> ${ventaData.metodoPago.toUpperCase()}</p>
                    <p style="margin:3px 0;">¡GRACIAS POR SU COMPRA!</p>
                    <p style="margin:3px 0;font-size:10px;">*** TICKET NO FISCAL ***</p>
                </div>
            </div>`;
        } else {
            return `<div class="comprobante-factura">
                <div class="text-center mb-3">
                    <h2>FACTURA</h2>
                    <h4>FERRETERÍA "EL MARTILLO"</h4>
                    <p>RFC: FME850301XYZ • Tel: (555) 123-4567</p>
                    <p>Av. Principal #123, Col. Centro</p>
                </div>
                <table class="table table-bordered table-sm">
                    <tr>
                        <td><strong>No. Documento:</strong></td><td>${ventaData.numeroFactura}</td>
                        <td><strong>Fecha:</strong></td><td>${ventaData.fecha}</td>
                    </tr>
                    <tr><td><strong>Cliente:</strong></td><td colspan="3">${ventaData.cliente}</td></tr>
                    <tr>
                        <td><strong>Cédula:</strong></td><td>${cedulaCliente}</td>
                        <td><strong>Teléfono:</strong></td><td>${ventaData.telefono}</td>
                    </tr>
                </table>
                <table class="table table-bordered table-sm">
                    <thead class="thead-dark">
                        <tr><th>Cant.</th><th>Descripción</th><th>P.Unit</th><th>Total</th></tr>
                    </thead>
                    <tbody>${ventaData.items.map(function(item) {
                        return `<tr>
                            <td>${item.cantidad}</td><td>${item.nombre}</td>
                            <td>${formatoDinero(item.precio)}</td>
                            <td>${formatoDinero(item.precio * item.cantidad)}</td>
                        </tr>`;
                    }).join('')}</tbody>
                </table>
                <table class="table table-bordered table-sm float-right" style="width:300px;">
                    <tr><td><strong>Subtotal:</strong></td><td style="text-align:right;">${formatoDinero(ventaData.subtotal)}</td></tr>
                    <tr><td><strong>IVA:</strong></td><td style="text-align:right;">${formatoDinero(ventaData.iva)}</td></tr>
                    <tr class="table-success"><td><strong>TOTAL:</strong></td><td style="text-align:right;">${formatoDinero(ventaData.total)}</td></tr>
                    ${ventaData.metodoPago === 'efectivo' ? `
                    <tr><td><strong>Efectivo Recibido:</strong></td><td style="text-align:right;">${formatoDinero(efectivoRecibido)}</td></tr>
                    <tr><td><strong>Cambio:</strong></td><td style="text-align:right;">${formatoDinero(cambio)}</td></tr>` : ''}
                </table>
                <div class="clearfix"></div>
                <div class="mt-4 text-center">
                    <p><strong>Método de Pago:</strong> ${ventaData.metodoPago.toUpperCase()}</p>
                    <p class="text-muted">¡Gracias por su compra!</p>
                    <small class="text-muted">*** Comprobante de venta ***</small>
                </div>
            </div>`;
        }
    }

    // =============================================
    // 12. REINICIAR VENTA
    // =============================================

    function reiniciarFormularioVenta() {
        carrito = [];
        actualizarCarrito();
        actualizarMetricas();
        $('#selectCliente').val(null).trigger('change');
        $('#infoClienteSeleccionado').remove();
        clienteSeleccionado = null;
        $('#btnQuitarCliente').hide();
        $('#subtotalVenta').text('0');
        $('#ivaVenta').text('0');
        $('#totalVenta').text('$0');
        $('#porcentajeIva').text('16%');
        $('#metodoPago').val('efectivo');
        $('#efectivoRecibido').val('0');
        $('#cambioVenta').text('$0');
        $('#tipoComprobante').val('ticket');
        $('#selectIva').val('16');
        $('#numeroTarjeta, #fechaVencimiento, #cvvTarjeta, #nombreTitular, #referenciaTransaccion').val('');
        $('#montoEfectivoMixto, #montoTarjetaMixto').val('0');
        $('.metodo-pago-detalle').addClass('d-none');
        $('#pagoEfectivo').removeClass('d-none');
        numeroFactura = generarNumeroFactura();
        $('#numeroFactura').text(numeroFactura);
    }

    function reiniciarVenta() {
        reiniciarFormularioVenta();
        cargarProductosDesdeDB();
    }

    // =============================================
    // 13. EVENTOS DE BOTONES
    // =============================================

    $(document).on('click', '#btnImprimir', function() {
        const esTicket = $('#tipoComprobante').val() === 'ticket';
        const ventana = window.open('', '_blank');
        const estilo = esTicket
            ? '<style>@media print{body{margin:0;padding:0;}.comprobante-ticket{width:80mm;font-family:"Courier New",monospace;font-size:12px;}}</style>'
            : '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        ventana.document.write('<!DOCTYPE html><html><head><title>Comprobante - ' + numeroFactura + '</title>' + estilo + '</head><body>' +
            $('#vistaPreviaComprobante').html() +
            '<script>window.onload=function(){window.print();setTimeout(function(){window.close();},1000);}<\/script></body></html>');
        ventana.document.close();
        toastr.success('Comprobante enviado a impresión', 'Impresión');
    });

    $(document).on('click', '#btnNuevaVenta', function() {
        $('#modalVistaPrevia').modal('hide');
        reiniciarVenta();
        toastr.success('Nueva venta iniciada', 'Sistema');
    });

    $(document).on('click', '#btnQuitarClienteInfo', function(e) {
        e.preventDefault();
        e.stopPropagation();
        limpiarClienteSeleccionado();
        toastr.info('Cliente removido');
        return false;
    });

    $(document).on('click', '#btnQuitarCliente', function(e) {
        e.preventDefault();
        limpiarClienteSeleccionado();
        toastr.info('Cliente removido');
    });

    $(document).on('click', '#btnCancelar', function() {
        if (carrito.length > 0 && confirm('¿Está seguro de cancelar la venta?')) {
            reiniciarVenta();
            toastr.info('Venta cancelada', 'Sistema');
        }
    });

    $(document).on('click', '#btnLimpiarCarrito', function() {
        if (carrito.length > 0 && confirm('¿Limpiar carrito?')) {
            carrito = [];
            actualizarCarrito();
            actualizarMetricas();
            toastr.success('Carrito limpiado');
        }
    });

    $(document).on('click', '#btnImprimirDirecto', function() {
        if (carrito.length === 0) {
            toastr.info('No hay productos en el carrito para imprimir', 'Información');
            return;
        }
        mostrarVistaPrevia();
    });

    // =============================================
    // 14. ESCÁNER
    // =============================================

    $(document).on('click', '#btnOpenScanner', function() {
        $('#modalScanner').modal('show');
        setTimeout(function() { $('#inputCodigoManual').focus(); }, 500);
    });

    $('#modalScanner').on('shown.bs.modal', function() {
        $('#inputCodigoManual').focus();
    }).on('hidden.bs.modal', function() {
        $('#inputCodigoManual').val('');
    });

    $(document).on('keydown', '#inputCodigoManual', function(e) {
        if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            setTimeout(procesarCodigoEscaneado, 10);
        }
    });

    $(document).on('click', '#btnProcesarCodigo', function() {
        procesarCodigoEscaneado();
    });

    function procesarCodigoEscaneado() {
        const codigo = $('#inputCodigoManual').val().trim();
        if (!codigo) {
            toastr.warning('Ingrese un código para escanear', 'Escáner');
            $('#inputCodigoManual').focus();
            return;
        }
        $('#inputCodigoManual').prop('disabled', true);
        buscarProductoPorCodigo(codigo);
        setTimeout(function() { $('#inputCodigoManual').prop('disabled', false).focus(); }, 100);
    }

    function buscarProductoPorCodigo(codigo) {
        if (Object.keys(productos).length === 0) {
            toastr.error('No hay productos cargados en el sistema', 'Error');
            return;
        }
        const productoEncontrado = Object.values(productos).find(function(p) {
            return (p.codigo || '').toString().trim() === codigo.toString().trim();
        });

        if (!productoEncontrado) {
            toastr.error('No se encontró producto con código: "' + codigo + '"', 'Producto no encontrado');
            setTimeout(function() { $('#inputCodigoManual').focus().select(); }, 100);
            return;
        }
        if (productoEncontrado.stock <= 0) {
            toastr.error('Producto sin stock disponible', 'Stock');
            $('#inputCodigoManual').val('').focus();
            return;
        }

        const productoEnCarrito = carrito.find(function(item) { return item.id === productoEncontrado.id; });
        if (productoEnCarrito && productoEnCarrito.cantidad >= productoEncontrado.stock) {
            toastr.error('Stock máximo alcanzado. Disponible: ' + productoEncontrado.stock, 'Stock');
            $('#inputCodigoManual').val('').focus();
            return;
        }

        if (productoEnCarrito) {
            productoEnCarrito.cantidad += 1;
            toastr.success('"' + productoEncontrado.nombre + '" - Cantidad: ' + productoEnCarrito.cantidad, 'Carrito');
        } else {
            carrito.push({
                id: productoEncontrado.id, nombre: productoEncontrado.nombre,
                precio: productoEncontrado.precio, cantidad: 1,
                stock: productoEncontrado.stock, codigo: productoEncontrado.codigo,
                categoria: productoEncontrado.categoria
            });
            toastr.success('"' + productoEncontrado.nombre + '" agregado al carrito', 'Carrito');
        }
        actualizarCarrito();
        actualizarMetricas();
        setTimeout(function() { $('#inputCodigoManual').val('').focus(); }, 100);
    }

    // =============================================
    // 15. ATAJOS DE TECLADO
    // =============================================

    $(document).on('keydown', function(e) {
        // No activar atajos si el foco está en un input/textarea/select
        const enInput = $(e.target).is('input, textarea, select');

        if (e.key === 'F1') {
            e.preventDefault();
            $('#modalAtajos').modal('show');
        }
        if (e.key === 'F2') {
            e.preventDefault();
            $('#modalScanner').modal('show');
        }
        if (e.key === 'F3') {
            e.preventDefault();
            $('#btnProcesarVenta').trigger('click');
        }
        if (e.key === 'F9') {
            e.preventDefault();
            $('#btnLimpiarCarrito').trigger('click');
        }
        if (e.ctrlKey && e.key === 'e' && !enInput) {
            e.preventDefault();
            $('#modalScanner').modal('show');
        }
    });

    // =============================================
    // 16. INICIALIZACIÓN
    // =============================================

    function inicializarSistema() {
        console.log('🚀 Inicializando sistema...');
        configurarSelect2Clientes();
        configurarNuevoCliente();
        cargarProductosDesdeDB();
        configurarMetodosPago();
        configurarBusquedaTiempoReal();
        configurarInputEfectivo();

        $('#selectIva').on('change', function() {
            actualizarTotales(parseFloat(window.ventaSubtotalNumerico) || 0);
        });

        $('#btnAtajos').on('click', function() {
            $('#modalAtajos').modal('show');
        });

        $('#numeroFactura').text(numeroFactura);
        console.log('✅ Sistema inicializado');
        toastr.success('Sistema de punto de venta listo');
    }

    // =============================================
    // 17. FUNCIONES GLOBALES (window)
    // =============================================

    window.agregarProductoFrecuente = function(id) {
        const producto = productos[id];
        if (producto) agregarAlCarrito(producto);
        else toastr.error('Producto no encontrado');
    };

    window.recargarFrecuentes = function() {
        cargarProductosFrecuentes();
        toastr.info('Productos frecuentes actualizados');
    };

    // Arrancar
    $(document).ready(function() {
        setTimeout(inicializarSistema, 500);
    });

})(jQuery);
</script>

@stop