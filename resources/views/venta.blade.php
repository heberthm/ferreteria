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
            <div class="modal-header bg-default">
                <h5 class="modal-title">
                    <i class="fas fa-receipt mr-2"></i> Comprobante de Venta
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">
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

  /* Estilos para vista previa e impresión */
    @media print {
        body * {
            visibility: hidden;
        }
        .print-content, .print-content * {
            visibility: visible;
        }
        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background-color: white;
        }
        .no-print {
            display: none !important;
        }
        .modal {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: 100% !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .modal-content {
            border: none !important;
            box-shadow: none !important;
            min-height: 100vh !important;
        }
        .modal-header, .modal-footer {
            display: none !important;
        }
        .modal-body {
            padding: 0 !important;
        }
        .vista-previa-buttons,
        .view-navigation,
        .preview-title,
        .preview-container {
            display: none !important;
        }
        
        /* Específico para impresión de ticket */
        .ticket-print {
            font-family: 'Courier New', monospace !important;
            width: 300px !important;
            margin: 0 auto !important;
            padding: 10px !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
            border: none !important;
            background-color: white !important;
            box-shadow: none !important;
            transform: none !important;
            max-height: none !important;
            overflow: visible !important;
        }
        
        /* Específico para impresión de factura */
        .factura-print {
            font-family: Arial, sans-serif !important;
            width: 210mm !important;
            margin: 0 auto !important;
            padding: 20px !important;
            font-size: 14px !important;
            border: none !important;
            background-color: white !important;
            box-shadow: none !important;
            transform: none !important;
        }
    }
    

    /* Estilos específicos para ticket - IDÉNTICOS EN VISTA PREVIA E IMPRESIÓN */
   #vistaPreviaTicket .preview-container {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 70vh !important;
    background-color: #f5f5f5 !important;
    padding: 20px !important;
    margin: 0 !important;
}

#ticketPreview {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    transition: transform 0.2s ease;
}

#ticketPreview > div {
    width: 302px !important;
    max-width: 302px !important;
    min-width: 302px !important;
    margin: 0 auto !important;
    box-sizing: border-box !important;
}
    
    .ticket-header {
        text-align: center;
        border-bottom: 1px dashed #000;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    
    .ticket-item {
        border-bottom: 1px dotted #ccc;
        padding: 4px 0;
        page-break-inside: avoid; /* Evitar que los items se corten al imprimir */
    }
    
    .ticket-item-name {
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .ticket-item-details {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        margin-bottom: 2px;
    }
    
    .ticket-item-total {
        text-align: right;
        font-weight: bold;
        font-size: 11px;
    }
    
    .ticket-footer {
        border-top: 1px dashed #000;
        padding-top: 10px;
        margin-top: 10px;
        text-align: center;
        page-break-inside: avoid; /* Evitar que el footer se corte */
    }
    
    /* Contenedor de items del ticket SIN SCROLL */
    .ticket-items-container {
        margin: 10px 0;
        max-height: none !important; /* Eliminar límite de altura */
        overflow: visible !important; /* Eliminar scroll */
        page-break-inside: auto; /* Permitir salto de página si es necesario */
    }
    
    /* Estilos específicos para factura - IDÉNTICOS EN VISTA PREVIA E IMPRESIÓN */
    .factura-preview, .factura-print {
        font-family: Arial, sans-serif;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 20px;
        font-size: 14px;
        border: 1px solid #ddd;
        background-color: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        page-break-inside: avoid;
    }
    
    .factura-header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    
    .factura-cliente {
        border: 1px solid #000;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f9f9f9;
    }
    
    .factura-items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    .factura-items th {
        border-bottom: 2px solid #000;
        padding: 8px;
        text-align: left;
    }
    
    .factura-items td {
        border-bottom: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    
    .factura-totales {
        margin-top: 20px;
        border-top: 2px solid #000;
        padding-top: 10px;
    }
    
    .text-right {
        text-align: right !important;
    }
    
    .text-center {
        text-align: center !important;
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

    // Variables para vista previa de ventas
    let modalVentaId = null;
    let datosVenta = null;
    let datosCliente = null;
    let datosVendedor = null;
    let detallesVenta = null;
    let escalaTicket = 1;
    let escalaFactura = 0.8;
    let contenidoTicketGenerado = '';
    let contenidoFacturaGenerado = '';

    // Configurar toastr
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        timeOut: "3000",
        extendedTimeOut: "1000"
    };

    // =============================================
    // FUNCIONES AUXILIARES
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

    // ============================================
    // VER DETALLE DE VENTA
    // ============================================
    function verDetalleVenta(id) {
        modalVentaId = id;
        
        $.ajax({
            url: "{{ url('ventas/detalle') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    datosVenta = response.data.venta;
                    datosCliente = response.data.cliente;
                    datosVendedor = response.data.vendedor;
                    detallesVenta = response.data.detalles;
                    
                    $('#modalFactura').text(datosVenta.numero_factura || 'N/A');
                    $('#modalFecha').text(datosVenta.fecha || 'N/A');
                    $('#modalHora').text(datosVenta.hora || 'N/A');
                    
                    let estado = datosVenta.estado || 'pendiente';
                    let estadoTexto = '';
                    let badgeClass = '';
                    
                    switch(estado) {
                        case 'completada': estadoTexto = 'Completada'; badgeClass = 'badge-success'; break;
                        case 'pendiente': estadoTexto = 'Pendiente'; badgeClass = 'badge-warning'; break;
                        case 'cancelada': estadoTexto = 'Cancelada'; badgeClass = 'badge-danger'; break;
                        default: estadoTexto = estado.charAt(0).toUpperCase() + estado.slice(1); badgeClass = 'badge-secondary';
                    }
                    
                    $('#modalEstado').removeClass().addClass('badge ' + badgeClass).text(estadoTexto);
                    $('#modalCliente').text(datosCliente ? datosCliente.nombre : 'Cliente General');
                    $('#modalDocumento').text(datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A');
                    $('#modalMetodoPago').text(datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A');
                    $('#modalVendedor').text(datosVendedor ? datosVendedor.nombre : 'N/A');
                    
                    let htmlProductos = '';
                    let subtotalProductos = 0;
                    
                    if (detallesVenta && detallesVenta.length > 0) {
                        detallesVenta.forEach(function(p) {
                            let cantidad = parseFloat(p.cantidad) || 0;
                            let precioUnitario = parseFloat(p.precio_unitario) || 0;
                            let subtotal = parseFloat(p.subtotal) || 0;
                            subtotalProductos += subtotal;
                            
                            htmlProductos += '<tr>' +
                                '<td>' + (p.nombre || 'Producto sin nombre') + '</td>' +
                                '<td>' + (p.codigo || 'N/A') + '</td>' +
                                '<td class="text-center">' + cantidad.toFixed(0) + '</td>' +
                                '<td class="text-right">$' + precioUnitario.toLocaleString('es-CO') + '</td>' +
                                '<td class="text-right">$' + subtotal.toLocaleString('es-CO') + '</td>' +
                                '</tr>';
                        });
                    } else {
                        htmlProductos = '<tr><td colspan="5" class="text-center text-muted">No hay productos registrados</td></tr>';
                    }
                    
                    $('#modalDetalleProductos').html(htmlProductos);
                    
                    let totalVenta = parseFloat(datosVenta.total) || 0;
                    $('#modalSubtotalProductos').text('$' + subtotalProductos.toLocaleString('es-CO'));
                    $('#modalTotalVenta').text('$' + totalVenta.toLocaleString('es-CO'));
                    
                    let diferencia = totalVenta - subtotalProductos;
                    $('#filaIVA, #filaDescuento').hide();
                    
                    if (Math.abs(diferencia) > 0.01) {
                        let ivaCalculado = subtotalProductos * 0.19;
                        if (Math.abs(diferencia - ivaCalculado) < 1) {
                            $('#modalIVA').text('$' + diferencia.toLocaleString('es-CO'));
                            $('#filaIVA').show();
                        } else if (diferencia < 0) {
                            $('#modalDescuento').text('-$' + Math.abs(diferencia).toLocaleString('es-CO'));
                            $('#filaDescuento').show();
                        }
                    }
                    
                    let observaciones = datosVenta.observaciones || '';
                    if (observaciones && observaciones.trim() !== '') {
                        $('#modalObservaciones').text(observaciones);
                        $('#modalObservacionesContainer').show();
                    } else {
                        $('#modalObservacionesContainer').hide();
                    }
                    
                    let btnCancelar = $('#btnCancelarVentaModal');
                    if (estado !== 'cancelada' && estado !== 'completada') {
                        btnCancelar.removeClass('d-none');
                    } else {
                        btnCancelar.addClass('d-none');
                    }
                    
                    mostrarContenidoDetalle();
                    $('#modalDetalleVenta').modal('show');
                } else {
                    toastr.error(response.message || 'No se pudo cargar el detalle');
                }
            },
            error: function() {
                toastr.error('Error al cargar el detalle de la venta');
            }
        });
    }

    // ============================================
    // FUNCIONES DE VISTA PREVIA
    // ============================================
  function mostrarVistaPrevia(tipo) {
    prepararDatosVistaPrevia(tipo);
    
    if (tipo === 'ticket') {
        mostrarVistaPreviaTicket();
    } else {
        mostrarVistaPreviaFactura();
    }
}

function mostrarVistaPreviaTicket() {
    prepararDatosVistaPrevia('ticket');
    ocultarTodosContenidos();
    $('#vistaPreviaTicket').show();
    
    // Configurar contenedor para ticket centrado de 80mm
    $('.preview-container').css({
        'display': 'flex',
        'justify-content': 'center',
        'align-items': 'center',
        'min-height': '70vh',
        'max-height': 'none',
        'overflow-y': 'visible',
        'background': '#f5f5f5',
        'padding': '20px',
        'margin': '0'
    });
    
    aplicarEscalaTicket();
}

function mostrarVistaPreviaFactura() {
    prepararDatosVistaPrevia('factura');
    ocultarTodosContenidos();
    $('#vistaPreviaFactura').show();
    aplicarEscalaFactura();
    
    // Configurar contenedor para factura (scroll normal)
    $('.preview-container').css({
        'max-height': '70vh',
        'overflow-y': 'auto',
        'display': 'block',
        'justify-content': 'normal',
        'align-items': 'normal',
        'min-height': 'auto',
        'background': 'transparent',
        'padding': '0',
        'margin': '0'
    });
}

// ============================================
// FUNCIÓN PARA FORMATEAR NÚMEROS SIN DECIMALES
// ============================================
function formatSinDecimales(numero) {
    var entero = Math.round(parseFloat(numero) || 0);
    return entero.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

// ============================================
// PREPARAR DATOS PARA VISTA PREVIA
// ============================================

function prepararDatosVistaPrevia(tipo) {
    if (!datosVenta) return;

    var totalVenta = parseFloat(datosVenta.total) || 0;
    var subtotalProductos = 0;
    var totalProductosVendidos = 0;

    if (detallesVenta && detallesVenta.length > 0) {
        detallesVenta.forEach(function(p) {
            var cantidad = parseFloat(p.cantidad) || 0;
            var subtotal = parseFloat(p.subtotal) || 0;
            subtotalProductos += subtotal;
            totalProductosVendidos += cantidad;
        });
    }

    var diferencia = totalVenta - subtotalProductos;
    var tieneIVA = false;
    var tieneDescuento = false;
    var valorIVA = 0;
    var valorDescuento = 0;

    if (Math.abs(diferencia) > 0.01) {
        if (diferencia > 0) { tieneIVA = true; valorIVA = diferencia; }
        else { tieneDescuento = true; valorDescuento = Math.abs(diferencia); }
    }

   if (tipo === 'ticket') {
        // ============================================
        // TICKET 80mm - 302px EXACTOS
        // ============================================
        contenidoTicketGenerado = `
            <div style="width: 302px; max-width: 302px; min-width: 302px; margin: 0 auto; background: white; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box;">
                <div style="width: 100%; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.2; padding: 10px; box-sizing: border-box;">
                    
                    <!-- HEADER -->
                    <div style="text-align: center; padding-bottom: 8px; border-bottom: 1px dashed #000; margin-bottom: 8px;">
                        <h4 style="margin: 0; font-size: 14px; font-weight: bold;">SUPERMERCADO XYZ</h4>
                        <p style="margin: 2px 0; font-size: 10px;">NIT: 123456789-0</p>
                        <p style="margin: 2px 0; font-size: 9px;">Dirección: Calle 123 #45-67</p>
                        <p style="margin: 2px 0; font-size: 9px;">Tel: (601) 123-4567</p>
                        <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                        <p style="margin: 2px 0;"><strong>FACTURA:</strong> ${datosVenta.numero_factura || 'N/A'}</p>
                        
                        <!-- FECHA Y HORA EN LA MISMA LÍNEA -->
                        <p style="margin: 2px 0; display: flex; justify-content: center; gap: 10px;">
                            <span><strong>FECHA:</strong> ${datosVenta.fecha || 'N/A'}</span>
                            <span><strong>HORA:</strong> ${datosVenta.hora || 'N/A'}</span>
                        </p>
                    </div>
                    
                    <!-- CLIENTE -->
                    <div style="margin: 6px 0;">
                        <p style="margin: 2px 0;"><strong>CLIENTE:</strong> ${datosCliente ? datosCliente.nombre : 'Cliente General'}</p>
                        <p style="margin: 2px 0;"><strong>CED:</strong> ${datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A'}</p>
                        <p style="margin: 2px 0;"><strong>VENDEDOR:</strong> ${datosVendedor ? datosVendedor.nombre : 'N/A'}</p>
                    </div>
                    
                    <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                    
                    <!-- ENCABEZADOS DE COLUMNAS -->
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #000; font-weight: bold; font-size: 11px; margin-bottom: 4px;">
                        <div style="width: 35%;">DESCRIPCIÓN</div>
                        <div style="width: 15%; text-align: center;">CANT.</div>
                        <div style="width: 20%; text-align: right;">V.UNIT</div>
                        <div style="width: 30%; text-align: right;">VR.TOTAL</div>
                    </div>
                    
                    <div style="margin: 8px 0;">
        `;
        
        // PRODUCTOS
        if (detallesVenta && detallesVenta.length > 0) {
            detallesVenta.forEach(function(p) {
                var cantidad = parseFloat(p.cantidad) || 0;
                var precioUnitario = parseFloat(p.precio_unitario) || 0;
                var subtotal = parseFloat(p.subtotal) || 0;
                var nombreProducto = p.nombre || 'Producto';
                
                if (nombreProducto.length > 20) {
                    nombreProducto = nombreProducto.substring(0, 17) + '...';
                }
                
                contenidoTicketGenerado += `
                    <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dotted #ccc;">
                        <div style="width: 35%; font-size: 11px;">${nombreProducto}</div>
                        <div style="width: 15%; text-align: center; font-size: 11px;">${cantidad}</div>
                        <div style="width: 20%; text-align: right; font-size: 11px;">$${formatSinDecimales(precioUnitario)}</div>
                        <div style="width: 30%; text-align: right; font-size: 11px; font-weight: bold;">$${formatSinDecimales(subtotal)}</div>
                    </div>
                `;
            });
        } else {
            contenidoTicketGenerado += `<div style="text-align: center; padding: 10px; font-size: 11px;">No hay productos registrados</div>`;
        }
        
        contenidoTicketGenerado += `
                    </div>
                    
                    <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                    
                    <!-- TOTAL PRODUCTOS -->
                    <div style="margin-bottom: 5px;">
                        <p style="margin: 2px 0; font-size: 11px; font-weight: bold;">TOTAL PRODUCTOS: ${totalProductosVendidos} unidades</p>
                    </div>
                    
                    <div style="text-align: right; margin-top: 8px;">
                        <p style="margin: 3px 0; font-size: 11px;">Subtotal: $${formatSinDecimales(subtotalProductos)}</p>
        `;
        
        if (tieneIVA) {
            contenidoTicketGenerado += `<p style="margin: 3px 0; font-size: 11px;">IVA (19%): $${formatSinDecimales(valorIVA)}</p>`;
        }
        
        if (tieneDescuento) {
            contenidoTicketGenerado += `<p style="margin: 3px 0; font-size: 11px;">Descuento: -$${formatSinDecimales(valorDescuento)}</p>`;
        }
        
        contenidoTicketGenerado += `
                        <p style="margin: 5px 0; font-weight: bold; font-size: 13px; border-top: 1px dashed #000; padding-top: 3px;">TOTAL: $${formatSinDecimales(totalVenta)}</p>
                        <p style="margin: 3px 0; font-size: 11px;"><strong>PAGO:</strong> ${datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A'}</p>
                    </div>
                    
                    <!-- FOOTER -->
                    <div style="border-top: 1px dashed #000; padding-top: 8px; margin-top: 8px; text-align: center;">
                        <p style="margin: 2px 0; font-size: 10px; font-weight: bold;">¡GRACIAS POR SU COMPRA!</p>
                        <p style="margin: 2px 0; font-size: 9px;">Conserve este ticket para cambios</p>
                        <p style="margin: 2px 0; font-size: 9px;">${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO', {hour: '2-digit', minute:'2-digit'})}</p>
                    </div>
                    
                </div>
            </div>
        `;
        
        $('#ticketPreview').html(contenidoTicketGenerado);
        
    } else {
        // ============================================
        // FACTURA - Formato A4
        // ============================================
        contenidoFacturaGenerado = `
            <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: white;">
                
                <!-- HEADER FACTURA -->
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;">
                    <h1 style="margin: 0;">FACTURA DE VENTA</h1>
                    <h3 style="margin: 5px 0;">SUPERMERCADO XYZ</h3>
                    <p style="margin: 2px 0;">NIT: 123456789-0</p>
                    <p style="margin: 2px 0;">Dirección: Calle 123 #45-67, Bogotá D.C.</p>
                    <p style="margin: 2px 0;">Tel: (601) 123-4567 | Email: info@superxyz.com</p>
                </div>
                
                <!-- INFORMACIÓN FACTURA Y CLIENTE -->
                <div style="display: flex; margin-bottom: 20px;">
                    <div style="flex: 1; padding-right: 15px;">
                        <h4>INFORMACIÓN DE FACTURA</h4>
                        <table style="width: 100%;">
                            <tr><td><strong>No. Factura:</strong></td><td>${datosVenta.numero_factura || 'N/A'}</td></tr>
                            <tr><td><strong>Fecha:</strong></td><td>${datosVenta.fecha || 'N/A'}</td></tr>
                            <tr><td><strong>Hora:</strong></td><td>${datosVenta.hora || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td>${datosVenta.estado ? datosVenta.estado.charAt(0).toUpperCase() + datosVenta.estado.slice(1) : 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div style="flex: 1; padding-left: 15px;">
                        <h4>INFORMACIÓN DEL CLIENTE</h4>
                        <table style="width: 100%;">
                            <tr><td><strong>Nombre:</strong></td><td>${datosCliente ? datosCliente.nombre : 'Cliente General'}</td></tr>
                            <tr><td><strong>Documento:</strong></td><td>${datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A'}</td></tr>
                            <tr><td><strong>Método de Pago:</strong></td><td>${datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A'}</td></tr>
                            <tr><td><strong>Vendedor:</strong></td><td>${datosVendedor ? datosVendedor.nombre : 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                
               
               <!-- TOTAL DE PRODUCTOS VENDIDOS -->
                <div style="margin-bottom: 15px; padding: 10px; border-left: 4px solid #27292a;">
                    <h5 style="margin: 0; color: #1c1c1d;">
                        <i class="fas fa-boxes" style="margin-right: 8px;"></i>
                        TOTAL DE PRODUCTOS VENDIDOS: <strong>${totalProductosVendidos} unidades</strong>
                    </h5>
                </div>
                
                <!-- TABLA DE PRODUCTOS -->
                <h4>DETALLE DE PRODUCTOS</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: left;">DESCRIPCIÓN</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: left;">CÓDIGO</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: center;">CANTIDAD</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: right;">PRECIO UNITARIO</th>
                            <th style="border-bottom: 2px solid #000; padding: 8px; text-align: right;">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (detallesVenta && detallesVenta.length > 0) {
            detallesVenta.forEach(function(p) {
                var cantidad = parseFloat(p.cantidad) || 0;
                var precioUnitario = parseFloat(p.precio_unitario) || 0;
                var subtotal = parseFloat(p.subtotal) || 0;
                
                contenidoFacturaGenerado += `
                    <tr>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px;">${p.nombre || 'Producto sin nombre'}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px;">${p.codigo || 'N/A'}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: center;">${cantidad}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: right;">$${precioUnitario.toLocaleString('es-CO')}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: right;">$${subtotal.toLocaleString('es-CO')}</td>
                    </tr>
                `;
            });
        } else {
            contenidoFacturaGenerado += `<tr><td colspan="5" style="padding: 20px; text-align: center;">No hay productos registrados</td></tr>`;
        }
        
        contenidoFacturaGenerado += `
                    </tbody>
                </table>
                
                <!-- TOTALES -->
                <div style="margin-top: 20px; border-top: 2px solid #000; padding-top: 10px;">
                    <div style="display: flex;">
                        <div style="flex: 2;"></div>
                        <div style="flex: 1;">
                            <table style="width: 100%;">
                                <tr><td><strong>Subtotal:</strong></td><td style="text-align: right;">$${subtotalProductos.toLocaleString('es-CO')}</td></tr>
        `;
        
        if (tieneIVA) {
            contenidoFacturaGenerado += `<tr><td>IVA (19%):</td><td style="text-align: right;">$${valorIVA.toLocaleString('es-CO')}</td></tr>`;
        }
        
        if (tieneDescuento) {
            contenidoFacturaGenerado += `<tr><td>Descuento:</td><td style="text-align: right;">-$${valorDescuento.toLocaleString('es-CO')}</td></tr>`;
        }
        
        contenidoFacturaGenerado += `
                                <tr style="border-top: 1px solid #000;">
                                    <td><strong>TOTAL:</strong></td>
                                    <td style="text-align: right;"><strong>$${totalVenta.toLocaleString('es-CO')}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
        `;
        
        if (datosVenta.observaciones) {
            contenidoFacturaGenerado += `
                <div style="margin-top: 30px;">
                    <h5>Observaciones:</h5>
                    <div style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                        ${datosVenta.observaciones}
                    </div>
                </div>
            `;
        }

            
        contenidoFacturaGenerado += `
                <!-- FIRMAS -->
                <div style="margin-top: 50px; display: flex;">
                    <div style="flex: 1; text-align: center;">
                        <hr style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">
                        <p>Firma del Cliente</p>
                    </div>
                    <div style="flex: 1; text-align: center;">
                        <hr style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">
                        <p>Firma del Vendedor</p>
                    </div>
                </div>
                
                <!-- PIE DE PÁGINA -->
                <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                    <p>Documento generado el: ${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO')}</p>
                    <p>Este documento es válido como factura de venta según Resolución DIAN 12345</p>
                </div>
                
            </div>
        `;
        
        $('#facturaPreview').html(contenidoFacturaGenerado);
    }
}


// ============================================
// ESCALAS Y ZOOM
// ============================================

function aplicarEscalaTicket() {
    $('#ticketPreview').css({
        'transform': 'scale(' + escalaTicket + ')',
        'transform-origin': 'center center'
    });
}

function aplicarEscalaFactura() {
    $('#facturaPreview').css('transform', 'scale(' + escalaFactura + ')');
}

function ajustarTicket(accion) {
    if (accion === 'aumentar' && escalaTicket < 1.5) {
        escalaTicket += 0.1;
    } else if (accion === 'disminuir' && escalaTicket > 0.5) {
        escalaTicket -= 0.1;
    } else if (accion === 'reset') {
        escalaTicket = 1;
    }
    aplicarEscalaTicket();
}

function ajustarFactura(accion) {
    if (accion === 'aumentar' && escalaFactura < 1.2) {
        escalaFactura += 0.1;
    } else if (accion === 'disminuir' && escalaFactura > 0.5) {
        escalaFactura -= 0.1;
    } else if (accion === 'reset') {
        escalaFactura = 0.8;
    }
    aplicarEscalaFactura();
}

// ============================================
// IMPRESIÓN
// ============================================

function imprimirTicket() {
    var currentScale = escalaTicket;
    $('#ticketPreview').css('transform', 'scale(1)');
    
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ticket de Venta</title>
            <style>
                @page { size: 80mm auto; margin: 0; }
                body { margin: 0; padding: 0; font-family: 'Courier New', monospace; background: white; width: 80mm; }
                .ticket-print { width: 80mm; margin: 0 auto; padding: 10px; box-sizing: border-box; }
            </style>
        </head>
        <body>
            <div class="ticket-print">${contenidoTicketGenerado}</div>
        </body>
        </html>
    `;
    
    var iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.opacity = '0';
    document.body.appendChild(iframe);
    
    var iframeDoc = iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(printContent);
    iframeDoc.close();
    
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            $('#ticketPreview').css('transform', 'scale(' + currentScale + ')');
            document.body.removeChild(iframe);
        }, 100);
    }, 100);
}

function imprimirFactura() {
    var currentScale = escalaFactura;
    $('#facturaPreview').css('transform', 'scale(0.8)');
    
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Factura de Venta</title>
            <style>
                @page { size: A4; margin: 0; }
                body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: white; }
                .factura-print { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20px; box-sizing: border-box; }
            </style>
        </head>
        <body>
            <div class="factura-print">${contenidoFacturaGenerado}</div>
        </body>
        </html>
    `;
    
    var iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.opacity = '0';
    document.body.appendChild(iframe);
    
    var iframeDoc = iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(printContent);
    iframeDoc.close();
    
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            $('#facturaPreview').css('transform', 'scale(' + currentScale + ')');
            document.body.removeChild(iframe);
        }, 100);
    }, 100);
}

function formatSinDecimales(numero) {
    var entero = Math.round(numero);
    return entero.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

// ============================================
// CONTROL DE VISTAS
// ============================================

function mostrarContenidoDetalle() {
    ocultarTodosContenidos();
    $('#contenidoDetalle').show();
    escalaTicket = 1;
    escalaFactura = 0.8;
    
    $('.preview-container').css({
        'max-height': '70vh',
        'overflow-y': 'auto',
        'display': 'block',
        'justify-content': 'normal',
        'align-items': 'normal',
        'min-height': 'auto',
        'background': 'transparent',
        'padding': '0',
        'margin': '0'
    });
}

function volverAlDetalle() {
    mostrarContenidoDetalle();
}

function ocultarTodosContenidos() {
    $('#vistaPreviaTicket, #vistaPreviaFactura, #contenidoDetalle').hide();
}

// ´==========================================
// ´Cancelar ´venta
// ´===========================================

    function cancelarVenta(id) {
        id = id || modalVentaId;
        if (!id) { toastr.error('ID no válido'); return; }
        if (confirm('¿Está seguro de cancelar esta venta?')) {
            $.ajax({
                url: "{{ url('ventas/cancelar') }}/" + id,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Venta cancelada exitosamente');
                        $('#modalDetalleVenta').modal('hide');
                        if (window.tablaVentas) window.tablaVentas.ajax.reload();
                    } else {
                        toastr.error(response.message || 'No se pudo cancelar');
                    }
                },
                error: function() { toastr.error('Error en la solicitud'); }
            });
        }
    }

    // Exponer funciones globales
    window.verDetalleVenta = verDetalleVenta;
    window.mostrarVistaPrevia = mostrarVistaPrevia;
    window.ajustarTicket = ajustarTicket;
    window.ajustarFactura = ajustarFactura;
    window.imprimirTicket = imprimirTicket;
    window.imprimirFactura = imprimirFactura;
    window.volverAlDetalle = volverAlDetalle;
    window.cancelarVenta = cancelarVenta;

    // =============================================
    // LIMPIAR CLIENTE
    // =============================================
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
    // CARGA DE PRODUCTOS
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
                    toastr.error('No se pudieron cargar los productos');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    }

    // =============================================
    // SELECT2 CLIENTES
    // =============================================
    function configurarSelect2Clientes() {
    const selectElement = $('#selectCliente');
    if (selectElement.hasClass('select2-hidden-accessible')) selectElement.select2('destroy');
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
                return { 
                    results: data.map(function(cliente) {
                        return { 
                            id: cliente.id, 
                            text: cliente.nombre + (cliente.cedula ? ' - ' + cliente.cedula : ''), 
                            nombre: cliente.nombre, 
                            cedula: cliente.cedula, 
                            email: cliente.email, 
                            telefono: cliente.telefono, 
                            direccion: cliente.direccion 
                        };
                    }) 
                };
            }
        },
        placeholder: 'Escribe para buscar cliente...',
        minimumInputLength: 2,
        allowClear: true,
        width: '100%',
        language: {
            errorLoading: function() {
                return 'No se pudieron cargar los resultados';
            },
            inputTooLong: function(args) {
                var overChars = args.input.length - args.maximum;
                return 'Por favor, elimina ' + overChars + ' carácter' + (overChars > 1 ? 'es' : '');
            },
            inputTooShort: function(args) {
                var remainingChars = args.minimum - args.input.length;
                return 'Por favor, escribe ' + remainingChars + ' o más caracteres';
            },
            loadingMore: function() {
                return 'Cargando más resultados...';
            },
            maximumSelected: function(args) {
                return 'Solo puedes seleccionar ' + args.maximum + ' elemento' + (args.maximum > 1 ? 's' : '');
            },
            noResults: function() {
                return 'No se encontraron resultados';
            },
            searching: function() {
                return 'Buscando...';
            },
            removeAllItems: function() {
                return 'Eliminar todos los elementos';
            },
            removeItem: function() {
                return 'Eliminar elemento';
            }
        }
    });
    
    // Forzar el foco al hacer clic en el select2
    selectElement.on('select2:open', function() {
        setTimeout(function() {
            const searchField = document.querySelector('.select2-search__field');
            if (searchField) {
                searchField.focus();
            }
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
    
    selectElement.on('select2:clear', function() { 
        limpiarClienteSeleccionado(); 
        toastr.info('Cliente removido'); 
    });
}

function mostrarInfoClienteBasica(clienteData) {
    $('#infoClienteSeleccionado').remove();
    const infoHtml = `<div id="infoClienteSeleccionado" class="mt-2 p-2 bg-light rounded border">
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
    // PRODUCTOS Y CATEGORÍAS
    // =============================================
    function mostrarTodosLosProductos() {
        const todosProductos = Object.values(productos);
        if (todosProductos.length === 0) {
            $('#resultadosProductos').html(`<tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-box fa-3x mb-3"></i><h5>No hay productos disponibles</h5></td></tr>`);
        } else {
            mostrarResultadosBusqueda(todosProductos);
        }
    }

    function inicializarCategorias() {
        const categorias = new Set();
        Object.values(productos).forEach(p => { if (p.categoria && p.categoria.trim()) categorias.add(p.categoria); });
        const botonesContainer = $('#filtrosCategoria .btn-group');
        botonesContainer.empty();
        botonesContainer.append('<button type="button" class="btn btn-outline-primary active" data-categoria="todas">Todas</button>');
        Array.from(categorias).sort().forEach(categoria => { botonesContainer.append(`<button type="button" class="btn btn-outline-secondary" data-categoria="${categoria}">${categoria}</button>`); });
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
        $('#busquedaRapida').on('keypress', function(e) { if (e.which === 13) { const termino = $(this).val().trim(); if (termino.length >= 2) buscarProductos(termino); else mostrarTodosLosProductos(); } });
    }

    function buscarProductos(termino) {
        const terminoLower = termino.toLowerCase();
        const resultados = Object.values(productos).filter(p => (p.codigo && p.codigo.toLowerCase().includes(terminoLower)) || (p.nombre && p.nombre.toLowerCase().includes(terminoLower)) || (p.categoria && p.categoria.toLowerCase().includes(terminoLower)));
        mostrarResultadosBusqueda(resultados);
    }

    function filtrarProductosPorCategoria(categoria) {
        const productosFiltrados = categoria === 'todas' ? Object.values(productos) : Object.values(productos).filter(p => p.categoria === categoria);
        mostrarResultadosBusqueda(productosFiltrados);
        $('#busquedaRapida').val('');
        toastr.info(productosFiltrados.length + ' productos en ' + (categoria === 'todas' ? 'todas las categorías' : categoria));
    }

    function mostrarResultadosBusqueda(resultados) {
        const tbody = $('#resultadosProductos');
        tbody.empty();
        if (resultados.length === 0) { tbody.append(`<tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3"></i><h5>No se encontraron productos</h5></td></tr>`); return; }
        resultados.forEach(producto => {
            const precio = parseFloat(producto.precio) || 0;
            const stock = parseInt(producto.stock) || 0;
            const claseStock = stock <= 5 ? 'text-danger font-weight-bold' : stock <= 10 ? 'text-warning font-weight-bold' : 'text-success';
            tbody.append(`<tr class="producto-fila" style="cursor:pointer;"><td class="align-middle"><small class="text-muted font-weight-bold">${producto.codigo || 'N/A'}</small></td><td class="align-middle"><div class="d-flex align-items-center"><div class="bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;"><i class="fas fa-box text-primary"></i></div><div><div class="font-weight-bold text-dark">${producto.nombre}</div><small class="text-muted">${producto.categoria || 'Sin categoría'}</small></div></div></td><td class="align-middle font-weight-bold text-success">${formatoDinero(precio)}</td><td class="align-middle ${claseStock}">${stock}${stock <= 5 ? '<br><small class="badge badge-danger">Stock bajo</small>' : ''}</td><td class="align-middle"><button class="btn btn-sm btn-success btn-agregar" data-id="${producto.id}" data-nombre="${producto.nombre}" data-precio="${producto.precio}" data-stock="${producto.stock}" data-codigo="${producto.codigo || ''}"><i class="fas fa-cart-plus"></i> Agregar</button></td></tr>`);
        });
        $('.btn-agregar').off('click').on('click', function(e) { e.stopPropagation(); const producto = productos[$(this).data('id')]; if (producto) agregarAlCarrito(producto); else toastr.error('Producto no encontrado'); });
        $('.producto-fila').off('click').on('click', function(e) { if (!$(e.target).closest('.btn-agregar').length) { const producto = productos[$(this).find('.btn-agregar').data('id')]; if (producto) agregarAlCarrito(producto); } });
    }

    // =============================================
    // CARRITO
    // =============================================
    function agregarAlCarrito(producto) {
        if (!producto || producto.stock <= 0) { toastr.error('Producto sin stock disponible'); return; }
        const productoEnCarrito = carrito.find(item => item.id === producto.id);
        if (productoEnCarrito) {
            if (productoEnCarrito.cantidad >= producto.stock) { toastr.error('No hay suficiente stock'); return; }
            productoEnCarrito.cantidad++;
        } else {
            carrito.push({ id: producto.id, nombre: producto.nombre, precio: producto.precio, cantidad: 1, stock: producto.stock, codigo: producto.codigo, categoria: producto.categoria });
        }
        actualizarCarrito();
        actualizarMetricas();
    }

    function actualizarCarrito() {
        const tbody = $('#itemsCarrito');
        tbody.empty();
        if (carrito.length === 0) { tbody.html(`<td><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>Carrito vacío</td></tr>`); actualizarTotales(); return; }
        let subtotal = 0;
        carrito.forEach((item, index) => {
            const itemSubtotal = item.precio * item.cantidad;
            subtotal += itemSubtotal;
            tbody.append(`<tr><td class="align-middle"><div class="font-weight-bold">${item.nombre}</div><small class="text-muted">${item.codigo}</small></td><td class="align-middle"><div class="d-flex align-items-center justify-content-center"><button class="btn btn-outline-secondary btn-sm btn-restar mr-1" data-index="${index}"><i class="fas fa-minus"></i></button><div class="input-group" style="width:90px;"><input type="number" class="form-control text-center cantidad-input" value="${item.cantidad}" min="1" max="${item.stock}" data-index="${index}" style="height:31px;"></div><button class="btn btn-outline-secondary btn-sm btn-sumar ml-1" data-index="${index}"><i class="fas fa-plus"></i></button></div></td><td class="align-middle font-weight-bold">${formatoDinero(item.precio)}<br><small class="text-success">Subtotal: ${formatoDinero(itemSubtotal)}</small></td><td class="align-middle"><button class="btn btn-sm btn-danger btn-eliminar" data-index="${index}"><i class="fas fa-trash"></i></button></td></tr>`);
        });
        actualizarTotales(subtotal);
        configurarEventosCarrito();
    }

    function configurarEventosCarrito() {
        $('.btn-sumar').off('click').on('click', function() { const index = $(this).data('index'); const item = carrito[index]; if (item.cantidad < item.stock) { item.cantidad++; actualizarCarrito(); actualizarMetricas(); toastr.info(item.nombre + ': ' + item.cantidad + ' unidades'); } else { toastr.error('Stock insuficiente'); } });
        $('.btn-restar').off('click').on('click', function() { const index = $(this).data('index'); const item = carrito[index]; if (item.cantidad > 1) { item.cantidad--; } else { carrito.splice(index, 1); toastr.info('Producto eliminado'); actualizarCarrito(); actualizarMetricas(); return; } actualizarCarrito(); actualizarMetricas(); toastr.info(item.nombre + ': ' + item.cantidad + ' unidades'); });
        $('.btn-eliminar').off('click').on('click', function() { const index = $(this).data('index'); const nombre = carrito[index].nombre; carrito.splice(index, 1); actualizarCarrito(); actualizarMetricas(); toastr.info(nombre + ' eliminado'); });
        $('.cantidad-input').off('change').on('change', function() { const index = $(this).data('index'); const item = carrito[index]; const nuevaCantidad = parseInt($(this).val()); if (nuevaCantidad >= 1 && nuevaCantidad <= item.stock) { item.cantidad = nuevaCantidad; actualizarCarrito(); actualizarMetricas(); } else if (nuevaCantidad > item.stock) { $(this).val(item.cantidad); toastr.error('Stock máximo: ' + item.stock + ' unidades'); } else { $(this).val(item.cantidad); } });
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
        carrito.forEach(item => { totalProductos += item.cantidad; totalVenta += item.precio * item.cantidad; });
        $('#metricTotalProductos').text(totalProductos);
        $('#metricVentaActual').text(formatoDinero(Math.round(totalVenta)));
    }

    // =============================================
    // PAGOS Y CAMBIO
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
        $('#efectivoRecibido').on('input', function() { let valor = $(this).val().replace(/[^\d]/g, ''); $(this).val(valor && valor !== '0' ? formatoPuntosMil(parseInt(valor)) : '0'); calcularCambio(); }).on('focus', function() { $(this).select(); }).on('blur', function() { if (!$(this).val() || $(this).val() === '0') { $(this).val('0'); calcularCambio(); } });
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
            $(`#pago${metodo.charAt(0).toUpperCase() + metodo.slice(1)}`).removeClass('d-none');
            if (metodo === 'efectivo') calcularCambio();
            else if (metodo === 'mixto') calcularTotalMixto();
        });
        $('#efectivoRecibido').on('input', calcularCambio);
        $('#montoEfectivoMixto, #montoTarjetaMixto').on('input', calcularTotalMixto);
    }

    // =============================================
    // PRODUCTOS FRECUENTES
    // =============================================
    function cargarProductosFrecuentes() {
        const contenedor = $('#productosFrecuentes');
        contenedor.html('<p class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>');
        $.ajax({
            url: '{{ route("productos/frecuentes") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.productos && response.productos.length > 0) mostrarProductosFrecuentes(response.productos);
                else usarFallbackFrecuentes();
            },
            error: () => usarFallbackFrecuentes()
        });
    }

    function usarFallbackFrecuentes() {
        const lista = Object.values(productos).filter(p => p.stock > 0).slice(0, 6);
        if (lista.length > 0) mostrarProductosFrecuentes(lista);
        else $('#productosFrecuentes').html('<p class="text-muted text-center col-12">No hay productos con stock disponible</p>');
    }

    function mostrarProductosFrecuentes(productosFrecuentes) {
        const contenedor = $('#productosFrecuentes');
        contenedor.empty();
        if (!productosFrecuentes || productosFrecuentes.length === 0) { contenedor.html('<p class="text-muted text-center col-12">No hay productos frecuentes</p>'); return; }
        productosFrecuentes.forEach(producto => {
            const id = producto.id || producto.id_producto;
            const nombre = producto.nombre || 'Sin nombre';
            const codigo = producto.codigo || 'S/C';
            const precio = parseFloat(producto.precio || producto.precio_venta || 0);
            const stock = parseInt(producto.stock || producto.stock_actual || 0);
            const claseStock = stock <= 0 ? 'badge-danger' : stock <= 5 ? 'badge-warning' : stock <= 10 ? 'badge-info' : 'badge-success';
            const badgeStock = stock <= 0 ? '<span class="badge badge-danger">Sin stock</span>' : `<span class="badge ${claseStock}">Stock: ${stock}</span>`;
            contenedor.append(`<div class="col-6 col-md-4 mb-3"><div class="producto-card h-100" onclick="window.agregarProductoFrecuente(${id})" style="cursor:pointer;min-height:110px;"><div class="text-center"><i class="fas fa-star text-warning mb-1 d-block"></i><h6 class="mb-1" style="font-size:0.82rem;line-height:1.2;" title="${nombre}">${nombre.length > 22 ? nombre.substring(0, 22) + '…' : nombre}</h6><small class="text-muted d-block mb-1">${codigo}</small><span class="badge badge-success d-block mb-1">${formatoDinero(precio)}</span>${badgeStock}</div></div></div>`);
        });
    }

    // =============================================
    // PROCESAR VENTA
    // =============================================
    function obtenerReferenciaPago() {
        const metodo = $('#metodoPago').val();
        switch(metodo) {
            case 'tarjeta': return $('#numeroTarjeta').val() || 'Tarjeta';
            case 'transferencia': return $('#referenciaTransaccion').val() || 'Transferencia';
            case 'cheque': return $('#referenciaTransaccion').val() || 'Cheque';
            case 'mixto': return 'Mixto: Efectivo ' + ($('#montoEfectivoMixto').val() || 0) + ', Tarjeta ' + ($('#montoTarjetaMixto').val() || 0);
            default: return null;
        }
    }

    $(document).on('click', '#btnProcesarVenta', function(e) {
        e.preventDefault();
        if (!carrito || carrito.length === 0) { toastr.error('El carrito está vacío', 'Error'); return; }
        let stockValido = true;
        carrito.forEach(item => { const producto = productos[item.id]; if (!producto || producto.stock < item.cantidad) { toastr.error('Stock insuficiente para ' + item.nombre, 'Error de stock'); stockValido = false; } });
        if (!stockValido) return;
        const carritoParaEnviar = JSON.parse(JSON.stringify(carrito));
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfToken) { toastr.error('Token de seguridad no encontrado', 'Error'); return; }
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
            items: carritoParaEnviar.map(item => ({ producto_id: item.id, cantidad: item.cantidad, precio: Math.round(item.precio), subtotal: Math.round(item.precio * item.cantidad) }))
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
                    if (response.productos_actualizados && response.productos_actualizados.length > 0) actualizarProductosLocales(response.productos_actualizados);
                    else { carritoParaEnviar.forEach(item => { if (productos[item.id]) productos[item.id].stock -= item.cantidad; }); mostrarTodosLosProductos(); cargarProductosFrecuentes(); }
                    if (response.venta_completa) mostrarTicketAutomatico(response.venta_completa);
                    else mostrarVistaPrevia(response.numero_factura);
                    setTimeout(reiniciarFormularioVenta, 1000);
                } else { toastr.error(response.message || 'Error al procesar la venta', 'Error'); }
            },
            error: function(xhr) {
                let errorMessage = 'Error al procesar la venta';
                try {
                    if (xhr.responseText && xhr.responseText.trim().startsWith('{')) {
                        const err = JSON.parse(xhr.responseText);
                        if (err.message) errorMessage = err.message;
                        if (err.errors) { Object.values(err.errors).forEach(msgs => toastr.error(msgs[0])); return; }
                    } else if (xhr.responseText && xhr.responseText.includes('CSRF')) errorMessage = 'Error de token CSRF. Recarga la página.';
                } catch(ex) {}
                toastr.error(errorMessage, 'Error');
                if (xhr.status === 403) toastr.warning('Error de autenticación. Recarga la página.', 'Token CSRF');
            },
            complete: function() { $btn.prop('disabled', false).html(textoOriginal); }
        });
    });

    function actualizarProductosLocales(productosActualizados) {
        productosActualizados.forEach(p => { const id = p.id_producto || p.id; if (productos[id]) productos[id].stock = parseInt(p.stock || p.stock_actual || 0); });
        const busquedaActual = $('#busquedaRapida').val().trim();
        if (busquedaActual.length >= 2) buscarProductos(busquedaActual);
        else mostrarTodosLosProductos();
        cargarProductosFrecuentes();
    }

   function mostrarTicketAutomatico(datosVenta) {
    if (!datosVenta) { mostrarVistaPrevia(); return; }
    
    // 👇 TOMAR EL NOMBRE_USUARIO DESDE LA VENTA REGISTRADA
    const nombreVendedor = datosVenta.nombre_usuario || datosVenta.vendedor || window.vendedorNombre || 'Administrador';
    
    // Determinar cliente
    let nombreCliente = 'Consumidor Final';
    let cedulaCliente = 'N/A';
    let telefonoCliente = 'N/A';
    
    if (datosVenta.cliente) {
        nombreCliente = datosVenta.cliente.nombre || 'Consumidor Final';
        cedulaCliente = datosVenta.cliente.cedula || datosVenta.cliente.documento || 'N/A';
        telefonoCliente = datosVenta.cliente.telefono || 'N/A';
    }
    
    // Procesar items (pueden venir de detalles o directamente)
    let itemsVenta = [];
    if (datosVenta.detalles && datosVenta.detalles.length > 0) {
        itemsVenta = datosVenta.detalles.map(d => ({ 
            nombre: d.producto ? d.producto.nombre : (d.nombre || 'Producto'), 
            cantidad: d.cantidad, 
            precio: d.precio_unitario, 
            codigo: d.producto ? d.producto.codigo : (d.codigo || '') 
        }));
    } else if (datosVenta.items) {
        itemsVenta = datosVenta.items;
    } else if (window.carrito && window.carrito.length > 0) {
        itemsVenta = window.carrito;
    }
    
    const ventaData = {
        numeroFactura: datosVenta.numero_factura || numeroFactura,
        cliente: nombreCliente,
        cedula: cedulaCliente,
        telefono: telefonoCliente,
        vendedor: nombreVendedor, // 👈 AHORA VIENE DE nombre_usuario
        subtotal: datosVenta.subtotal || window.ventaSubtotalNumerico || 0,
        iva: datosVenta.iva || window.ventaIvaNumerico || 0,
        total: datosVenta.total || window.ventaTotalNumerico || 0,
        tipo: datosVenta.tipo_comprobante || $('#tipoComprobante').val() || 'ticket',
        fecha: datosVenta.fecha_venta || new Date().toLocaleString(),
        metodoPago: datosVenta.metodo_pago || $('#metodoPago').val(),
        porcentajeIva: parseFloat($('#selectIva').val()) || 19,
        cambio: datosVenta.cambio || 0,
        efectivoRecibido: datosVenta.efectivo_recibido || 0,
        items: itemsVenta
    };
    
    console.log('📄 Generando comprobante con vendedor:', ventaData.vendedor);
    
    $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
    $('#modalVistaPrevia').modal('show');
}



function mostrarVistaPrevia(numeroFacturaServidor) {
    if (carrito.length === 0) return;
    
    let subtotal = window.ventaSubtotalNumerico || 0;
    let iva = window.ventaIvaNumerico || 0;
    let total = window.ventaTotalNumerico || 0;
    
    if (subtotal === 0 && carrito.length > 0) {
        subtotal = carrito.reduce((sum, item) => sum + item.precio * item.cantidad, 0);
        const ivaPorcentaje = parseFloat($('#selectIva').val()) || 0;
        iva = Math.round(subtotal * ivaPorcentaje / 100);
        total = Math.round(subtotal + iva);
    }
    
    const ventaData = {
        numeroFactura: numeroFacturaServidor || numeroFactura,
        cliente: clienteSeleccionado ? clienteSeleccionado.nombre : 'Consumidor Final',
        cedula: clienteSeleccionado ? (clienteSeleccionado.cedula || 'N/A') : 'N/A',
        telefono: clienteSeleccionado ? (clienteSeleccionado.telefono || 'N/A') : 'N/A',
        vendedor: window.vendedorNombre || 'Administrador', // 👈 TOMAR DE LA VARIABLE GLOBAL
        items: carrito,
        subtotal: Math.round(subtotal),
        iva: Math.round(iva),
        total: Math.round(total),
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
    const totalProductos = ventaData.items.reduce((sum, item) => sum + item.cantidad, 0);
    const metodoPagoTexto = (ventaData.metodoPago || 'efectivo').charAt(0).toUpperCase() + (ventaData.metodoPago || 'efectivo').slice(1);
    const fechaFormateada = ventaData.fecha ? ventaData.fecha.split(',')[0] : new Date().toLocaleDateString('es-CO');
    const horaFormateada = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const documentoCliente = (ventaData.cedula && ventaData.cedula !== 'N/A' && ventaData.cedula !== 'null') ? ventaData.cedula : 'Consumidor Final';
    const cambio = ventaData.cambio || 0;
    const efectivoRecibido = ventaData.efectivoRecibido || 0;
    const ivaPorcentaje = ventaData.porcentajeIva || 19;
    const vendedorNombre = ventaData.vendedor || window.vendedorNombre || 'Administrador'; // 👈 TOMA EL NOMBRE

    // ============================================
    // TICKET 80mm
    // ============================================
    if (esTicket) {
        return `
        <div style="width: 80mm; font-family: 'Courier New', monospace; font-size: 11px; margin: 0 auto; background: white; padding: 8px; box-sizing: border-box;">
            
            <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 14px;">SUPERMERCADO XYZ</div>
                <div style="font-size: 9px;">NIT: 123456789-0</div>
                <div style="font-size: 9px;">Dirección: Calle 123 #45-67</div>
                <div style="font-size: 9px;">Tel: (601) 123-4567</div>
                <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
                <div><strong>FACTURA: ${ventaData.numeroFactura || 'N/A'}</strong></div>
                <div>FECHA: ${fechaFormateada} HORA: ${horaFormateada}</div>
            </div>

            <div style="margin: 6px 0;">
                <div><strong>CLIENTE:</strong> ${ventaData.cliente || 'Cliente General'}</div>
                <div><strong>DOC:</strong> ${documentoCliente}</div>
                <div><strong>VENDEDOR:</strong> ${vendedorNombre}</div> <!-- 👈 AHORA DINÁMICO -->
            </div>

            <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="text-align: left; padding: 4px 0;">DESCRIPCIÓN</th>
                        <th style="text-align: center; padding: 4px 0;">CANT.</th>
                        <th style="text-align: right; padding: 4px 0;">V.UNIT</th>
                        <th style="text-align: right; padding: 4px 0;">VR.TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    ${ventaData.items.map(item => {
                        const nombreCorto = item.nombre.length > 18 ? item.nombre.substring(0, 15) + '...' : item.nombre;
                        return `
                        <tr style="border-bottom: 1px dotted #ccc;">
                            <td style="padding: 3px 0; text-align: left;">${nombreCorto}</td>
                            <td style="padding: 3px 0; text-align: center;">${item.cantidad}</td>
                            <td style="padding: 3px 0; text-align: right;">$${Math.round(item.precio).toLocaleString('es-CO')}</td>
                            <td style="padding: 3px 0; text-align: right;">$${Math.round(item.precio * item.cantidad).toLocaleString('es-CO')}</td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>

            <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>

            <div style="margin-top: 6px; text-align: right;">
                <div><strong>TOTAL PRODUCTOS:</strong> ${totalProductos} unidades</div>
                <div style="margin-top: 4px;">Subtotal: $${Math.round(ventaData.subtotal || 0).toLocaleString('es-CO')}</div>
                <div>IVA (${ivaPorcentaje}%): $${Math.round(ventaData.iva || 0).toLocaleString('es-CO')}</div>
                <div style="font-weight: bold; font-size: 13px; margin-top: 4px;">TOTAL: $${Math.round(ventaData.total || 0).toLocaleString('es-CO')}</div>
                <div style="margin-top: 4px;"><strong>PAGO:</strong> ${metodoPagoTexto}</div>
            </div>

            <div style="border-top: 1px dashed #000; margin: 10px 0 6px 0; text-align: center;">
                <div style="font-weight: bold; margin: 6px 0;">¡GRACIAS POR SU COMPRA!</div>
                <div style="font-size: 9px;">Conserve este ticket para cambios</div>
                <div style="font-size: 9px; margin-top: 4px;">${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' })}</div>
            </div>
        </div>
        `;
    }

    // ============================================
    // FACTURA CARTA
    // ============================================
    return `
    <div style="font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: white; border: 1px solid #ccc;">
        
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 24px;">FACTURA DE VENTA<br>SUPERMERCADO XYZ</h1>
            <div style="margin-top: 8px; font-size: 12px;">
                NIT: 123456789-0 | Dirección: Calle 123 #45-67, Bogotá D.C. | Tel: (601) 123-4567 | Email: info@superxyz.com
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 20px; border: 1px solid #000; padding: 12px; background: #fef9e6;">
            <div style="flex: 1;">
                <h3 style="background: #000; color: white; padding: 4px 8px; font-size: 12px; display: inline-block; margin-bottom: 10px;">INFORMACIÓN DE FACTURA</h3>
                <div><strong>No. Factura:</strong> ${ventaData.numeroFactura || 'N/A'}</div>
                <div><strong>Fecha:</strong> ${fechaFormateada}</div>
                <div><strong>Hora:</strong> ${horaFormateada}</div>
                <div><strong>Estado:</strong> Completada</div>
            </div>
            <div style="flex: 1;">
                <h3 style="background: #000; color: white; padding: 4px 8px; font-size: 12px; display: inline-block; margin-bottom: 10px;">INFORMACIÓN DEL CLIENTE</h3>
                <div><strong>Nombre:</strong> ${ventaData.cliente || 'Cliente General'}</div>
                <div><strong>Documento:</strong> ${documentoCliente}</div>
                <div><strong>Método de Pago:</strong> ${metodoPagoTexto}</div>
                <div><strong>Vendedor:</strong> ${vendedorNombre}</div> <!-- 👈 AHORA DINÁMICO -->
            </div>
        </div>

        <div style="margin-bottom: 15px; padding: 8px 12px; background: #f8f9fa; border-left: 4px solid #000;">
            <strong>TOTAL DE PRODUCTOS VENDIDOS:</strong> ${totalProductos} unidades
        </div>

        <h4>DETALLE DE PRODUCTOS</h4>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border-bottom: 2px solid #000; padding: 8px; text-align: left;">DESCRIPCIÓN</th>
                    <th style="border-bottom: 2px solid #000; padding: 8px; text-align: left;">CÓDIGO</th>
                    <th style="border-bottom: 2px solid #000; padding: 8px; text-align: center;">CANTIDAD</th>
                    <th style="border-bottom: 2px solid #000; padding: 8px; text-align: right;">PRECIO UNITARIO</th>
                    <th style="border-bottom: 2px solid #000; padding: 8px; text-align: right;">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                ${ventaData.items.map(item => `
                <tr>
                    <td style="border-bottom: 1px solid #ddd; padding: 8px;">${item.nombre || 'Producto'}</td>
                    <td style="border-bottom: 1px solid #ddd; padding: 8px;">${item.codigo || 'N/A'}</td>
                    <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: center;">${item.cantidad}</td>
                    <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: right;">$${Math.round(item.precio).toLocaleString('es-CO')}</td>
                    <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: right;">$${Math.round(item.precio * item.cantidad).toLocaleString('es-CO')}</td>
                </tr>
                `).join('')}
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 20px; border-top: 1px solid #aaa; padding-top: 10px;">
            <div>Subtotal: $${Math.round(ventaData.subtotal || 0).toLocaleString('es-CO')}</div>
            <div>IVA (${ivaPorcentaje}%): $${Math.round(ventaData.iva || 0).toLocaleString('es-CO')}</div>
            <div style="font-size: 18px; font-weight: bold;">TOTAL: $${Math.round(ventaData.total || 0).toLocaleString('es-CO')}</div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 50px;">
            <div style="text-align: center; width: 45%;">
                <hr style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">
                <p style="margin-top: 8px;">Firma del Cliente</p>
            </div>
            <div style="text-align: center; width: 45%;">
                <hr style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">
                <p style="margin-top: 8px;">Firma del Vendedor</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px; font-size: 11px; color: #666;">
            <p>Documento generado el: ${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO')}</p>
            <p>Este documento es válido como factura de venta según Resolución DIAN 12345</p>
        </div>
    </div>
    `;
}

    // =============================================
    // REINICIAR VENTA
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

    function mostrarTicketAutomatico(datosVenta) {
    if (!datosVenta) { mostrarVistaPrevia(); return; }
    
    const ventaData = {
        numeroFactura: datosVenta.numero_factura || numeroFactura,
        cliente: datosVenta.cliente ? datosVenta.cliente.nombre : 'Consumidor Final',
        cedula: datosVenta.cliente ? datosVenta.cliente.cedula : 'N/A',
        telefono: datosVenta.cliente ? datosVenta.cliente.telefono : 'N/A',
        vendedor: datosVenta.vendedor || window.vendedorNombre || 'Administrador', // 👈 NUEVO
        subtotal: datosVenta.subtotal || window.ventaSubtotalNumerico || 0,
        iva: datosVenta.iva || window.ventaIvaNumerico || 0,
        total: datosVenta.total || window.ventaTotalNumerico || 0,
        tipo: datosVenta.tipo_comprobante || $('#tipoComprobante').val() || 'ticket',
        fecha: datosVenta.fecha_venta || new Date().toLocaleString(),
        metodoPago: datosVenta.metodo_pago || $('#metodoPago').val(),
        porcentajeIva: parseFloat($('#selectIva').val()) || 19,
        cambio: datosVenta.cambio || 0,
        efectivoRecibido: datosVenta.efectivo_recibido || 0,
        items: (datosVenta.detalles && datosVenta.detalles.length > 0) ? datosVenta.detalles.map(d => ({ 
            nombre: d.producto ? d.producto.nombre : 'Producto', 
            cantidad: d.cantidad, 
            precio: d.precio_unitario, 
            codigo: d.producto ? d.producto.codigo : '' 
        })) : carrito
    };
    
    $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
    $('#modalVistaPrevia').modal('show');
}

    // =============================================
    // EVENTOS DE BOTONES
    // =============================================
    $(document).on('click', '#btnImprimir', function() {
        const esTicket = $('#tipoComprobante').val() === 'ticket';
        const ventana = window.open('', '_blank');
        const estilo = esTicket ? '<style>@media print{body{margin:0;padding:0;}.comprobante-ticket{width:80mm;font-family:"Courier New",monospace;font-size:12px;}}</style>' : '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        ventana.document.write('<!DOCTYPE html><html><head><title>Comprobante - ' + numeroFactura + '</title>' + estilo + '</head><body>' + $('#vistaPreviaComprobante').html() + '<script>window.onload=function(){window.print();setTimeout(function(){window.close();},1000);}<\/script></body></html>');
        ventana.document.close();
        toastr.success('Comprobante enviado a impresión', 'Impresión');
    });

    $(document).on('click', '#btnNuevaVenta', function() { $('#modalVistaPrevia').modal('hide'); reiniciarVenta(); toastr.success('Nueva venta iniciada', 'Sistema'); });
    $(document).on('click', '#btnQuitarClienteInfo', function(e) { e.preventDefault(); e.stopPropagation(); limpiarClienteSeleccionado(); toastr.info('Cliente removido'); return false; });
    $(document).on('click', '#btnQuitarCliente', function(e) { e.preventDefault(); limpiarClienteSeleccionado(); toastr.info('Cliente removido'); });
    $(document).on('click', '#btnCancelar', function() { if (carrito.length > 0 && confirm('¿Está seguro de cancelar la venta?')) { reiniciarVenta(); toastr.info('Venta cancelada', 'Sistema'); } });
    $(document).on('click', '#btnLimpiarCarrito', function() { if (carrito.length > 0 && confirm('¿Limpiar carrito?')) { carrito = []; actualizarCarrito(); actualizarMetricas(); toastr.success('Carrito limpiado'); } });
    $(document).on('click', '#btnImprimirDirecto', function() { if (carrito.length === 0) { toastr.info('No hay productos en el carrito para imprimir', 'Información'); return; } mostrarVistaPrevia(); });

    // =============================================
    // ESCÁNER
    // =============================================
    $(document).on('click', '#btnOpenScanner', function() { $('#modalScanner').modal('show'); setTimeout(() => $('#inputCodigoManual').focus(), 500); });
    $('#modalScanner').on('shown.bs.modal', () => $('#inputCodigoManual').focus()).on('hidden.bs.modal', () => $('#inputCodigoManual').val(''));
    $(document).on('keydown', '#inputCodigoManual', function(e) { if (e.key === 'Enter' || e.key === 'Tab') { e.preventDefault(); setTimeout(procesarCodigoEscaneado, 10); } });
    $(document).on('click', '#btnProcesarCodigo', () => procesarCodigoEscaneado());

    function procesarCodigoEscaneado() {
        const codigo = $('#inputCodigoManual').val().trim();
        if (!codigo) { toastr.warning('Ingrese un código para escanear', 'Escáner'); $('#inputCodigoManual').focus(); return; }
        $('#inputCodigoManual').prop('disabled', true);
        buscarProductoPorCodigo(codigo);
        setTimeout(() => $('#inputCodigoManual').prop('disabled', false).focus(), 100);
    }

    function buscarProductoPorCodigo(codigo) {
        if (Object.keys(productos).length === 0) { toastr.error('No hay productos cargados en el sistema', 'Error'); return; }
        const productoEncontrado = Object.values(productos).find(p => (p.codigo || '').toString().trim() === codigo.toString().trim());
        if (!productoEncontrado) { toastr.error('No se encontró producto con código: "' + codigo + '"', 'Producto no encontrado'); setTimeout(() => $('#inputCodigoManual').focus().select(), 100); return; }
        if (productoEncontrado.stock <= 0) { toastr.error('Producto sin stock disponible', 'Stock'); $('#inputCodigoManual').val('').focus(); return; }
        const productoEnCarrito = carrito.find(item => item.id === productoEncontrado.id);
        if (productoEnCarrito && productoEnCarrito.cantidad >= productoEncontrado.stock) { toastr.error('Stock máximo alcanzado. Disponible: ' + productoEncontrado.stock, 'Stock'); $('#inputCodigoManual').val('').focus(); return; }
        if (productoEnCarrito) { productoEnCarrito.cantidad += 1; toastr.success('"' + productoEncontrado.nombre + '" - Cantidad: ' + productoEnCarrito.cantidad, 'Carrito'); }
        else { carrito.push({ id: productoEncontrado.id, nombre: productoEncontrado.nombre, precio: productoEncontrado.precio, cantidad: 1, stock: productoEncontrado.stock, codigo: productoEncontrado.codigo, categoria: productoEncontrado.categoria }); toastr.success('"' + productoEncontrado.nombre + '" agregado al carrito', 'Carrito'); }
        actualizarCarrito();
        actualizarMetricas();
        setTimeout(() => $('#inputCodigoManual').val('').focus(), 100);
    }

    // =============================================
    // ATAJOS DE TECLADO
    // =============================================
    $(document).on('keydown', function(e) {
        const enInput = $(e.target).is('input, textarea, select');
        if (e.key === 'F1') { e.preventDefault(); $('#modalAtajos').modal('show'); }
        if (e.key === 'F2') { e.preventDefault(); $('#modalScanner').modal('show'); }
        if (e.key === 'F3') { e.preventDefault(); $('#btnProcesarVenta').trigger('click'); }
        if (e.key === 'F9') { e.preventDefault(); $('#btnLimpiarCarrito').trigger('click'); }
        if (e.ctrlKey && e.key === 'e' && !enInput) { e.preventDefault(); $('#modalScanner').modal('show'); }
    });

    // =============================================
    // INICIALIZACIÓN
    // =============================================
    function inicializarSistema() {
        console.log('🚀 Inicializando sistema...');
        configurarSelect2Clientes();
        cargarProductosDesdeDB();
        configurarMetodosPago();
        configurarBusquedaTiempoReal();
        configurarInputEfectivo();
        $('#selectIva').on('change', () => actualizarTotales(parseFloat(window.ventaSubtotalNumerico) || 0));
        $('#btnAtajos').on('click', () => $('#modalAtajos').modal('show'));
        $('#numeroFactura').text(numeroFactura);
        console.log('✅ Sistema inicializado');
        toastr.success('Sistema de punto de venta listo');
    }

    window.agregarProductoFrecuente = function(id) { const producto = productos[id]; if (producto) agregarAlCarrito(producto); else toastr.error('Producto no encontrado'); };
    window.recargarFrecuentes = function() { cargarProductosFrecuentes(); toastr.info('Productos frecuentes actualizados'); };

    $(document).ready(() => setTimeout(inicializarSistema, 500));
 
 
  window.vendedorNombre = '{{ Auth::user()->name ?? Auth::user()->nombre_usuario ?? "Administrador" }}';
console.log('👤 Vendedor session:', window.vendedorNombre);

})(jQuery);
</script>

@stop