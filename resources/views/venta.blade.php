@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cash-register"></i> Punto de Venta</h1>
        <div class="d-flex align-items-center gap-3">
            <button id="btnAtajos" class="btn bg-ligth">
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
       

       <!-- Carrito de Compras -->
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
                        <option value="factura_fiscal">Factura Fiscal (CFDI)</option>
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

<!-- Modal Atajos de Teclado -->
<div class="modal fade" id="modalAtajos" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="fas fa-keyboard"></i> Atajos de Teclado</h5>
                <button type="button" class="close" data-bs-dismiss="modal"">&times;</button>
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
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                
                <form method="POST" id="form_guardar_cliente" action="{{ route('venta') }}">
                    @csrf
                    
                    <!-- Campo userId oculto -->
                    <input type="hidden" name="userId" value="{{ Auth::check() ? Auth::user()->id : 1 }}">

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
                <button type="button" class="close text-white" data-bs-dismiss="modal">
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


<script>(function($) {
    'use strict';
    
    console.log('🚀 Punto de Venta - Sistema cargado');

    // Variables globales
    let productos = {};
    let carrito = [];
    let numeroFactura = generarNumeroFactura();
    let clienteSeleccionado = null;
    let timeoutBusqueda = null;
    let scannerModalActive = false; 

    // Configurar toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // =============================================
    // 1. FUNCIONES AUXILIARES CORREGIDAS
    // =============================================
    
    function generarNumeroFactura() {
        let contador = localStorage.getItem('contadorFacturas') || 1;
        contador = parseInt(contador);
        localStorage.setItem('contadorFacturas', contador + 1);
        return `F-${contador.toString().padStart(5, '0')}`;
    }
    
    // MODIFICADA: Sin decimales, solo puntos de miles
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
        
        const selectElement = $('#selectCliente');
        selectElement.val(null).trigger('change');
        
        if ($('#infoClienteSeleccionado').length) {
            $('#infoClienteSeleccionado').remove();
        }
        
        $('#cliente_nombre').val('');
        $('#cliente_cedula').val('');
        $('#cliente_email').val('');
        $('#cliente_direccion').val('');
        $('#cliente_telefono').val('');
        
        clienteSeleccionado = null;
        $('#btnQuitarCliente').hide();
        
        console.log('✅ Cliente limpiado completamente');
    }

    // =============================================
    // 2. FUNCIONES DE INICIALIZACIÓN
    // =============================================
    
    function cargarProductosDesdeDB() {
        console.log('📦 Cargando productos...');
        
        $.ajax({
            url: '{{ route("productos-todos") }}',
            method: 'GET',
            success: function(response) {
                console.log('Respuesta de productos:', response);
                
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
    // 3. CONFIGURAR SELECT2 CLIENTES
    // =============================================
    function configurarSelect2Clientes() {
        console.log('👤 Configurando Select2...');
        
        const selectElement = $('#selectCliente');
        
        if (selectElement.hasClass('select2-hidden-accessible')) {
            selectElement.select2('destroy');
        }
        
        selectElement.select2({
            ajax: {
                url: '{{ route("buscar_cliente") }}',
                method: 'GET',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    console.log('📋 Clientes encontrados:', data);
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
                    toastr.error('Error al buscar clientes');
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
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        selectElement.on('select2:open', function() {
            console.log('🔍 Select2 abierto, enfocando campo de búsqueda...');
            setTimeout(function() {
                $('.select2-search__field').focus().select();
            }, 100);
        });
            
        selectElement.on('select2:select', function(e) {
            const selectedData = e.params.data;
            console.log('✅ Cliente seleccionado:', selectedData);
            
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
            console.log('🧹 Select2 limpiado con X');
            e.preventDefault();
            limpiarClienteSeleccionado();
            toastr.info('Cliente removido');
        });
        
        selectElement.on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').focus();
            }, 50);
        });
    }

    // =============================================
    // 4. MOSTRAR INFO BÁSICA DEL CLIENTE
    // =============================================
    function mostrarInfoClienteBasica(clienteData) {
        if ($('#infoClienteSeleccionado').length) {
            $('#infoClienteSeleccionado').remove();
        }
        
        const infoHtml = `
            <div id="infoClienteSeleccionado" class="mt-2 p-2 bg-light rounded border">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="font-weight-bold text-primary">
                            <i class="fas fa-user mr-1"></i>
                            ${clienteData.nombre}
                        </span>
                        ${clienteData.cedula ? `
                            <span class="ml-2 text-muted">
                                <i class="fas fa-id-card mr-1"></i>
                                ${clienteData.cedula}
                            </span>
                        ` : ''}
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnQuitarClienteInfo">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#selectCliente').closest('.form-group').after(infoHtml);
    }

    // =============================================
    // 5. MANEJO DE NUEVO CLIENTE
    // =============================================
    function configurarNuevoCliente() {
        console.log('👤 Configurando nuevo cliente...');
        
        $('#form_guardar_cliente').off('submit');
        $('#form_guardar_cliente').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('#BtnGuardar_cliente');
            if ($btn.prop('disabled')) {
                console.log('⏸️ Botón ya deshabilitado, evitando doble envío');
                return false;
            }
            
            $btn.prop('disabled', true);
            
            setTimeout(function() {
                guardarNuevoCliente();
            }, 300);
        });
        
        $('#cedula').on('blur', function() {
            const cedula = $(this).val().trim();
            if (cedula) {
                verificarCedulaExistente(cedula);
            }
        });
    }

    function verificarCedulaExistente(cedula) {
        $.ajax({
            url: '{{ route("verificar_cliente") }}',
            method: 'GET',
            data: { cedula: cedula },
            success: function(response) {
                if (response === 'unique') {
                    $('#error_cedula').html('<span class="text-danger">Esta cédula ya existe</span>');
                    $('#cedula').addClass('is-invalid');
                    $('#BtnGuardar_cliente').prop('disabled', true);
                } else {
                    $('#error_cedula').html('<span class="text-success">Cédula disponible</span>');
                    $('#cedula').removeClass('is-invalid');
                    $('#BtnGuardar_cliente').prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error al verificar cédula');
            }
        });
    }

    function guardarNuevoCliente() {
        const formData = $('#form_guardar_cliente').serialize();
        
        $('#BtnGuardar_cliente').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: '{{ route("guardar_clientes") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message, 'Cliente');
                    
                    $('#modalNuevoCliente').hide();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    
                    $('#form_guardar_cliente')[0].reset();
                    $('#error_cedula').html('');
                    
                    agregarClienteAlSelect2(response.cliente);
                    
                    $('body').css('padding-right', '');
                    
                } else {
                    toastr.error(response.message, 'Error');
                    if (response.errors) {
                        Object.keys(response.errors).forEach(function(key) {
                            toastr.error(response.errors[key][0]);
                        });
                    }
                }
            },
            error: function(xhr) {
                console.error('Error al guardar cliente:', xhr);
                
                if (xhr.status === 409) {
                    toastr.error('La cédula/NIT ya existe en el sistema');
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('Error al guardar el cliente');
                }
            },
            complete: function() {
                $('#BtnGuardar_cliente').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Cliente');
            }
        });
    }

    function agregarClienteAlSelect2(cliente) {
        const selectElement = $('#selectCliente');
        
        const nuevaOpcion = new Option(
            cliente.nombre + (cliente.cedula ? ' - ' + cliente.cedula : ''),
            cliente.id,
            true,
            true
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
    // 6. MOSTRAR TODOS LOS PRODUCTOS Y CATEGORÍAS
    // =============================================
    function mostrarTodosLosProductos() {
        const todosProductos = Object.values(productos);
        console.log('Mostrando TODOS los productos:', todosProductos.length, 'productos');
        
        if (todosProductos.length === 0) {
            console.log('No hay productos para mostrar');
            $('#resultadosProductos').html(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <h5>No hay productos disponibles</h5>
                    </td>
                </tr>
            `);
        } else {
            mostrarResultadosBusqueda(todosProductos);
        }
    }

    function inicializarCategorias() {
        const categorias = new Set();
        Object.values(productos).forEach(function(p) {
            if (p.categoria && p.categoria.trim() !== '') {
                categorias.add(p.categoria);
            }
        });
        
        console.log('Categorías encontradas:', Array.from(categorias));
        
        const botonesContainer = $('#filtrosCategoria .btn-group');
        botonesContainer.empty();
        
        botonesContainer.append(`
            <button type="button" class="btn btn-outline-primary active" data-categoria="todas">
                Todas
            </button>
        `);
        
        Array.from(categorias).sort().forEach(function(categoria) {
            botonesContainer.append(`
                <button type="button" class="btn btn-outline-secondary" data-categoria="${categoria}">
                    ${categoria}
                </button>
            `);
        });
        
        $('#filtrosCategoria').show();
        
        botonesContainer.find('button').on('click', function() {
            const categoria = $(this).data('categoria');
            
            botonesContainer.find('button').removeClass('active btn-primary')
                .addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary')
                .addClass('active btn-primary');
            
            filtrarProductosPorCategoria(categoria);
        });
    }

    function configurarBusquedaTiempoReal() {
        $('#busquedaRapida').on('input', function() {
            const termino = $(this).val().trim();
            
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
            }
            
            timeoutBusqueda = setTimeout(function() {
                if (termino.length >= 2) {
                    buscarProductos(termino);
                } else if (termino.length === 0) {
                    mostrarTodosLosProductos();
                }
            }, 300);
        });

        $('#btnBuscarRapido').click(function() {
            const termino = $('#busquedaRapida').val().trim();
            if (termino.length >= 2) {
                buscarProductos(termino);
            } else {
                mostrarTodosLosProductos();
            }
        });

        $('#busquedaRapida').on('keypress', function(e) {
            if (e.which === 13) {
                const termino = $(this).val().trim();
                if (termino.length >= 2) {
                    buscarProductos(termino);
                } else {
                    mostrarTodosLosProductos();
                }
            }
        });
    }

    function buscarProductos(termino) {
        const terminoLower = termino.toLowerCase();
        const resultados = Object.values(productos).filter(function(producto) {
            return (
                (producto.codigo && producto.codigo.toLowerCase().includes(terminoLower)) ||
                (producto.nombre && producto.nombre.toLowerCase().includes(terminoLower)) ||
                (producto.categoria && producto.categoria.toLowerCase().includes(terminoLower))
            );
        });

        mostrarResultadosBusqueda(resultados);
    }

    function filtrarProductosPorCategoria(categoria) {
        let productosFiltrados = [];
        
        if (categoria === 'todas') {
            productosFiltrados = Object.values(productos);
        } else {
            productosFiltrados = Object.values(productos).filter(function(p) {
                return p.categoria === categoria;
            });
        }
        
        mostrarResultadosBusqueda(productosFiltrados);
        $('#busquedaRapida').val('');
        
        toastr.info(productosFiltrados.length + ' productos en ' + (categoria === 'todas' ? 'todas las categorías' : categoria));
    }

    function mostrarResultadosBusqueda(resultados) {
        console.log('Mostrando resultados:', resultados.length, 'productos');
        
        const tbody = $('#resultadosProductos');
        tbody.empty();
        
        if (resultados.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h5>No se encontraron productos</h5>
                    </td>
                </tr>
            `);
        } else {
            resultados.forEach(function(producto) {
                console.log('Producto:', producto);
                
                const precio = parseFloat(producto.precio) || 0;
                const stock = parseInt(producto.stock) || 0;
                const claseStock = stock <= 5 ? 'text-danger font-weight-bold' : 
                                  stock <= 10 ? 'text-warning font-weight-bold' : 'text-success';
                
                const fila = `
                    <tr class="producto-fila" style="cursor: pointer;">
                        <td class="align-middle">
                            <small class="text-muted font-weight-bold">${producto.codigo || 'N/A'}</small>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mr-3" 
                                     style="width: 40px; height: 40px;">
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
                            ${stock}
                            ${stock <= 5 ? '<br><small class="badge badge-danger">Stock bajo</small>' : ''}
                        </td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-success btn-agregar" 
                                    data-id="${producto.id}"
                                    data-nombre="${producto.nombre}"
                                    data-precio="${producto.precio}"
                                    data-stock="${producto.stock}"
                                    data-codigo="${producto.codigo || ''}">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(fila);
            });

            $('.btn-agregar').off('click').on('click', function(e) {
                e.stopPropagation();
                const productoId = $(this).data('id');
                console.log('Agregando producto ID:', productoId);
                const producto = productos[productoId];
                if (producto) {
                    agregarAlCarrito(producto);
                } else {
                    console.error('Producto no encontrado en productos[]:', productoId);
                    toastr.error('Producto no encontrado');
                }
            });

            $('.producto-fila').off('click').on('click', function(e) {
                if (!$(e.target).closest('.btn-agregar').length) {
                    const productoId = $(this).find('.btn-agregar').data('id');
                    const producto = productos[productoId];
                    if (producto) {
                        agregarAlCarrito(producto);
                    }
                }
            });
        }
    }

    // =============================================
    // 7. FUNCIONES DEL CARRITO
    // =============================================
    function agregarAlCarrito(producto) {
        if (!producto || producto.stock <= 0) {
            toastr.error('Producto sin stock disponible');
            return;
        }

        const productoEnCarrito = carrito.find(function(item) {
            return item.id === producto.id;
        });
        
        if (productoEnCarrito) {
            if (productoEnCarrito.cantidad >= producto.stock) {
                toastr.error('No hay suficiente stock');
                return;
            }
            productoEnCarrito.cantidad++;
        } else {
            carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: producto.precio,
                cantidad: 1,
                stock: producto.stock,
                codigo: producto.codigo,
                categoria: producto.categoria
            });
        }
        
        actualizarCarrito();
        actualizarMetricas();
    }

    function actualizarCarrito() {
        const tbody = $('#itemsCarrito');
        tbody.empty();
        
        if (carrito.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>
                        Carrito vacío
                    </td>
                </tr>
            `);
            actualizarTotales();
            return;
        }
        
        let subtotal = 0;
        
        carrito.forEach(function(item, index) {
            const itemSubtotal = item.precio * item.cantidad;
            subtotal += itemSubtotal;
            
            const fila = `
                <tr>
                    <td class="align-middle">
                        <div class="font-weight-bold">${item.nombre}</div>
                        <small class="text-muted">${item.codigo}</small>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-outline-secondary btn-sm btn-restar mr-1" 
                                    data-index="${index}">
                                <i class="fas fa-minus"></i>
                            </button>
                            
                            <div class="input-group" style="width: 90px;">
                                <input type="number" 
                                       class="form-control text-center cantidad-input" 
                                       value="${item.cantidad}" 
                                       min="1" 
                                       max="${item.stock}"
                                       data-index="${index}"
                                       style="height: 31px;">
                            </div>
                            
                            <button class="btn btn-outline-secondary btn-sm btn-sumar ml-1" 
                                    data-index="${index}">
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
                </tr>
            `;
            tbody.append(fila);
        });
        
        actualizarTotales(subtotal);
        configurarEventosCarrito();
    }

    function configurarEventosCarrito() {
        $('.btn-sumar').off('click').on('click', function() {
            const index = $(this).data('index');
            const producto = carrito[index];
            if (producto.cantidad < producto.stock) {
                producto.cantidad++;
                actualizarCarrito();
                actualizarMetricas();
                toastr.info(producto.nombre + ': ' + producto.cantidad + ' unidades');
            } else {
                toastr.error('Stock insuficiente');
            }
        });
        
        $('.btn-restar').off('click').on('click', function() {
            const index = $(this).data('index');
            const producto = carrito[index];
            if (producto.cantidad > 1) {
                producto.cantidad--;
                actualizarCarrito();
                actualizarMetricas();
                toastr.info(producto.nombre + ': ' + producto.cantidad + ' unidades');
            } else {
                carrito.splice(index, 1);
                actualizarCarrito();
                actualizarMetricas();
                toastr.info('Producto eliminado');
            }
        });
        
        $('.btn-eliminar').off('click').on('click', function() {
            const index = $(this).data('index');
            const producto = carrito[index];
            
            carrito.splice(index, 1);
            actualizarCarrito();
            actualizarMetricas();
            toastr.info(producto.nombre + ' eliminado');
        });
        
        $('.cantidad-input').off('change').on('change', function() {
            const index = $(this).data('index');
            const nuevaCantidad = parseInt($(this).val());
            const producto = carrito[index];
            
            if (nuevaCantidad >= 1 && nuevaCantidad <= producto.stock) {
                producto.cantidad = nuevaCantidad;
                actualizarCarrito();
                actualizarMetricas();
                toastr.info(producto.nombre + ': ' + producto.cantidad + ' unidades');
            } else if (nuevaCantidad > producto.stock) {
                $(this).val(producto.cantidad);
                toastr.error('Stock máximo: ' + producto.stock + ' unidades');
            } else {
                $(this).val(producto.cantidad);
            }
        });
    }
    
    function actualizarTotales(subtotal = 0) {
        const ivaPorcentaje = parseFloat($('#selectIva').val()) || 0;
        const iva = subtotal * (ivaPorcentaje / 100);
        const total = subtotal + iva;
        
        // Redondear todos los valores
        window.ventaSubtotalNumerico = Math.round(subtotal);
        window.ventaIvaNumerico = Math.round(iva);
        window.ventaTotalNumerico = Math.round(total);
        
        // Usar formato sin decimales
        $('#subtotalVenta').text(formatoPuntosMil(Math.round(subtotal)));
        $('#ivaVenta').text(formatoPuntosMil(Math.round(iva)));
        $('#totalVenta').text(formatoDinero(Math.round(total)));
        $('#porcentajeIva').text(ivaPorcentaje + '%');
        
        if ($('#metodoPago').val() === 'efectivo') {
            calcularCambio();
        }
        
        if ($('#metodoPago').val() === 'mixto') {
            calcularTotalMixto();
        }
    }
    
    function actualizarMetricas() {
        let totalProductos = 0;
        let totalVenta = 0;
        
        carrito.forEach(function(item) {
            totalProductos += item.cantidad;
            totalVenta += item.precio * item.cantidad;
        });
        
        $('#metricTotalProductos').text(totalProductos);
        $('#metricVentaActual').text(formatoDinero(Math.round(totalVenta)));
    }

    // =============================================
    // FUNCIONES NUEVAS PARA CORREGIR PROBLEMAS
    // =============================================

    // FUNCIÓN CALCULAR CAMBIO CORREGIDA
    function calcularCambio() {
        const total = parseFloat(window.ventaTotalNumerico) || 0;
        
        // Obtener valor del input de efectivo
        let efectivoInput = $('#efectivoRecibido').val();
        
        // Si hay valor, limpiar puntos de mil para cálculo
        if (efectivoInput && efectivoInput !== '0') {
            // Convertir a número para cálculo
            efectivoInput = efectivoInput.toString().replace(/\./g, '').replace(',', '.');
        } else {
            efectivoInput = '0';
        }
        
        const efectivo = parseFloat(efectivoInput) || 0;
        
        // Calcular cambio
        const cambio = Math.round(efectivo - total);
        
        // Mostrar cambio CON FORMATO (con puntos de mil)
        if (cambio >= 0) {
            $('#cambioVenta').text(formatoDinero(cambio));
        } else {
            $('#cambioVenta').text('-$' + formatoPuntosMil(Math.abs(cambio)));
        }
        
        // Guardar valor numérico para enviar al servidor
        window.cambioNumerico = cambio;
        
        return cambio;
    }

    // CONFIGURAR INPUT EFECTIVO MEJORADO
    function configurarInputEfectivo() {
        $('#efectivoRecibido').on('input', function() {
            // Obtener valor actual
            let valor = $(this).val();
            
            // Remover todo excepto números
            valor = valor.replace(/[^\d]/g, '');
            
            // Formatear con puntos de mil
            if (valor && valor !== '0') {
                const valorNumerico = parseInt(valor);
                $(this).val(formatoPuntosMil(valorNumerico));
            } else {
                $(this).val('0');
            }
            
            // Calcular cambio
            calcularCambio();
        });
        
        // Al enfocar, seleccionar todo el texto
        $('#efectivoRecibido').on('focus', function() {
            $(this).select();
        });
        
        // Al perder el foco, asegurar formato
        $('#efectivoRecibido').on('blur', function() {
            let valor = $(this).val();
            if (!valor || valor === '0') {
                $(this).val('0');
                calcularCambio();
            }
        });
    }

    function calcularTotalMixto() {
        const efectivo = parseFloat($('#montoEfectivoMixto').val()) || 0;
        const tarjeta = parseFloat($('#montoTarjetaMixto').val()) || 0;
        const totalMixto = Math.round(efectivo + tarjeta);
        
        $('#totalMixto').text(formatoDinero(totalMixto));
    }

    function configurarMetodosPago() {
        $('#metodoPago').on('change', function() {
            $('.metodo-pago-detalle').addClass('d-none');
            
            const metodo = $(this).val();
            $('#pago' + metodo.charAt(0).toUpperCase() + metodo.slice(1)).removeClass('d-none');
            
            if (metodo === 'efectivo') {
                calcularCambio();
            } else if (metodo === 'mixto') {
                calcularTotalMixto();
            }
        });
        
        $('#efectivoRecibido').on('input', calcularCambio);
        $('#montoEfectivoMixto, #montoTarjetaMixto').on('input', calcularTotalMixto);
    }

 


// =============================================
// FUNCIÓN MEJORADA CON DIAGNÓSTICO
// =============================================

function cargarProductosFrecuentes() {
    console.log('⭐ Cargando productos frecuentes...');
    
    const url = window.location.origin + '/productos/frecuentes';
    const debugUrl = window.location.origin + '/verificar-bd';
    
    console.log(`🔗 URL productos: ${url}`);
    console.log(`🔗 URL debug: ${debugUrl}`);
    
    // Primero verificar el estado de la BD
    fetch(debugUrl)
        .then(response => response.json())
        .then(debugData => {
            console.log('📊 Estado de la BD:', debugData);
            
            if (debugData.success && debugData.estadisticas) {
                const stats = debugData.estadisticas;
                console.log(`📈 Estadísticas: ${stats.total_productos} productos totales, ${stats.productos_con_stock} con stock, ${stats.ventas_completadas} ventas`);
                
                // Mostrar estadísticas en UI
                mostrarEstadisticasBD(stats);
                
                // Si no hay productos en absoluto
                if (stats.total_productos === 0) {
                    mostrarErrorBaseDatosVacia();
                    return;
                }
            }
            
            // Ahora cargar productos frecuentes
            cargarProductosDesdeAPI(url);
        })
        .catch(error => {
            console.error('❌ Error al verificar BD:', error);
            // Continuar intentando cargar productos de todas formas
            cargarProductosDesdeAPI(url);
        });
}

function cargarProductosDesdeAPI(url) {
    console.log(`📥 Cargando desde API: ${url}`);
    
    const contenedor = $('#productosFrecuentes');
    
    // Mostrar loading específico
    contenedor.html(`
        <div class="col-12 text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Consultando base de datos...</p>
            <small class="text-info" id="contadorTiempo">Esperando respuesta...</small>
        </div>
    `);
    
    // Contador de tiempo
    let tiempoInicio = Date.now();
    let intervalo = setInterval(() => {
        let segundos = Math.floor((Date.now() - tiempoInicio) / 1000);
        $('#contadorTiempo').text(`Esperando... ${segundos}s`);
    }, 1000);
    
    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        timeout: 20000,
        success: function(response) {
            clearInterval(intervalo);
            console.log('✅ Respuesta completa:', response);
            
            if (response && response.success) {
                if (response.productos && response.productos.length > 0) {
                    console.log(`📦 ${response.productos.length} productos cargados`);
                    mostrarProductosFrecuentesUI(response.productos, response.message);
                } else {
                    console.warn('⚠️ Respuesta exitosa pero sin productos');
                    mostrarSinProductosConOpciones(response.message);
                }
            } else {
                console.error('❌ Respuesta sin éxito:', response);
                mostrarErrorAPI(response);
            }
        },
        error: function(xhr, status, error) {
            clearInterval(intervalo);
            console.error('❌ Error en AJAX:', {status, error, xhr});
            mostrarErrorConexionCompleto(xhr, url);
        }
    });
}

// =============================================
// FUNCIONES DE UI MEJORADAS
// =============================================

function mostrarEstadisticasBD(stats) {
    // Opcional: mostrar estadísticas en algún lugar de la UI
    $('#estadisticasBD').remove();
    
    const html = `
        <div id="estadisticasBD" class="alert alert-light border mb-3">
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-2">
                    <div class="text-primary">
                        <i class="fas fa-box fa-2x"></i>
                        <div class="mt-1">
                            <strong>${stats.total_productos}</strong>
                            <div class="small">Productos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                        <div class="mt-1">
                            <strong>${stats.productos_activos}</strong>
                            <div class="small">Activos</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="text-warning">
                        <i class="fas fa-layer-group fa-2x"></i>
                        <div class="mt-1">
                            <strong>${stats.productos_con_stock}</strong>
                            <div class="small">Con stock</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="text-info">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                        <div class="mt-1">
                            <strong>${stats.ventas_completadas}</strong>
                            <div class="small">Ventas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#productosFrecuentes').before(html);
}

function mostrarProductosFrecuentesUI(productosArray, mensaje = '') {
    const contenedor = $('#productosFrecuentes');
    contenedor.empty();
    
    if (!productosArray || productosArray.length === 0) {
        mostrarSinProductosConOpciones(mensaje);
        return;
    }
    
    // Mostrar mensaje de origen
    if (mensaje) {
        contenedor.append(`
            <div class="col-12 mb-3">
                <div class="alert alert-info py-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    ${mensaje}
                </div>
            </div>
        `);
    }
    
    // Mostrar encabezado
    contenedor.append(`
        <div class="col-12 mb-3">
            <h5 class="border-bottom pb-2">
                <i class="fas fa-star text-warning mr-2"></i>
                Productos Disponibles
                <span class="badge badge-warning ml-2">${productosArray.length}</span>
            </h5>
        </div>
    `);
    
    // Mostrar productos
    productosArray.forEach((producto) => {
        const cardHtml = crearCardProductoConTipo(producto);
        contenedor.append(cardHtml);
    });
    
    // Mostrar pie con opciones
    contenedor.append(`
        <div class="col-12 mt-4">
            <div class="card bg-light">
                <div class="card-body text-center py-3">
                    
                </div>
            </div>
        </div>
    `);
}

function crearCardProductoConTipo(producto) {
    const id = producto.id || producto.id_producto;
    const nombre = producto.nombre || 'Sin nombre';
    const codigo = producto.codigo || 'N/A';
    const precio = parseFloat(producto.precio) || 0;
    const stock = parseInt(producto.stock) || 0;
    const categoria = producto.categoria || 'General';
    const tipo = producto.tipo || 'general';
    const totalVendido = producto.total_vendido || 0;
    
    // Determinar badge según tipo
    let tipoBadge = 'badge-secondary';
    let tipoTexto = 'General';
    let tipoIcono = 'fa-box';
    
    switch(tipo) {
        case 'mas_vendido':
            tipoBadge = 'badge-warning';
            tipoTexto = 'Más Vendido';
            tipoIcono = 'fa-fire';
            break;
        case 'con_stock':
            tipoBadge = 'badge-success';
            tipoTexto = 'Con Stock';
            tipoIcono = 'fa-boxes';
            break;
        case 'activo':
            tipoBadge = 'badge-info';
            tipoTexto = 'Activo';
            tipoIcono = 'fa-check-circle';
            break;
    }
    
    // Determinar estado del stock
    let stockClass = 'text-success';
    let stockIcon = 'fa-check';
    let stockTexto = `${stock} unidades`;
    
    if (stock <= 0) {
        stockClass = 'text-danger';
        stockIcon = 'fa-times';
        stockTexto = 'Agotado';
    } else if (stock <= 5) {
        stockClass = 'text-warning';
        stockIcon = 'fa-exclamation-triangle';
        stockTexto = `${stock} (Bajo)`;
    }
    
    return `
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100 shadow-sm">
                <!-- Header con tipo -->
                <div class="card-header py-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge ${tipoBadge}">
                            <i class="fas ${tipoIcono} mr-1"></i> ${tipoTexto}
                        </span>
                        ${totalVendido > 0 ? 
                            `<span class="badge badge-dark">
                                <i class="fas fa-chart-line mr-1"></i> ${totalVendido}
                            </span>` : ''
                        }
                    </div>
                </div>
                
                <!-- Cuerpo -->
                <div class="card-body p-3">
                    <!-- Nombre -->
                    <h6 class="card-title font-weight-bold mb-2" style="font-size: 0.95rem; min-height: 50px;">
                        ${nombre}
                    </h6>
                    
                    <!-- Código y categoría -->
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-barcode mr-1"></i> ${codigo}
                            <br>
                            <i class="fas fa-tag mr-1"></i> ${categoria}
                        </small>
                    </div>
                    
                    <!-- Precio -->
                    <div class="mb-2">
                        <h5 class="text-success font-weight-bold mb-0">
                            ${formatoDinero(precio)}
                        </h5>
                    </div>
                    
                    <!-- Stock -->
                    <div class="mb-3">
                        <small class="${stockClass}">
                            <i class="fas ${stockIcon} mr-1"></i> ${stockTexto}
                        </small>
                    </div>
                    
                    <!-- Botón -->
                    <button class="btn btn-sm btn-success btn-block" 
                            onclick="agregarProductoDesdeFrecuentes(${id})"
                            ${stock <= 0 ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus mr-1"></i>
                        ${stock <= 0 ? 'Sin stock' : 'Agregar'}
                    </button>
                </div>
            </div>
        </div>
    `;
}

function mostrarSinProductosConOpciones(mensaje = '') {
    const contenedor = $('#productosFrecuentes');
    
    contenedor.html(`
        <div class="col-12">
            <div class="alert alert-warning text-center py-5">
                <i class="fas fa-database fa-3x mb-4 text-warning"></i>
                <h4 class="mb-3">No hay productos disponibles</h4>
                
                ${mensaje ? `<p class="mb-3">${mensaje}</p>` : ''}
                
                <p class="text-muted mb-4">Puede ser que:</p>
                
                <div class="row text-left mb-4">
                    <div class="col-md-6">
                        <ul>
                            <li>No hay productos en el sistema</li>
                            <li>Todos los productos están inactivos</li>
                            <li>No hay stock disponible</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li>No se han registrado ventas</li>
                            <li>Hay un problema con la base de datos</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button class="btn btn-primary mr-2" onclick="verEstadoBD()">
                        <i class="fas fa-chart-bar mr-1"></i> Ver estado de BD
                    </button>
                    <a href="/admin/productos" class="btn btn-success">
                        <i class="fas fa-plus-circle mr-1"></i> Agregar productos
                    </a>
                </div>
            </div>
        </div>
    `);
}

// =============================================
// FUNCIONES AUXILIARES
// =============================================

window.verEstadoBD = function() {
    const url = window.location.origin + '/verificar-bd';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Estado BD:', data);
            
            if (data.success && data.estadisticas) {
                const stats = data.estadisticas;
                let mensaje = `
                    <strong>Estadísticas de la Base de Datos:</strong><br>
                    • Productos totales: ${stats.total_productos}<br>
                    • Productos activos: ${stats.productos_activos}<br>
                    • Productos con stock: ${stats.productos_con_stock}<br>
                    • Ventas completadas: ${stats.ventas_completadas}
                `;
                
                toastr.info(mensaje, 'Estado de BD', { timeOut: 10000 });
            }
        })
        .catch(error => {
            toastr.error('Error al verificar BD: ' + error.message, 'Error');
        });
};

window.mostrarTodosProductos = function() {
    // Esta función podría cargar todos los productos sin filtro
    const url = window.location.origin + '/productos/todos';
    
    // Implementar según tus necesidades
    toastr.info('Función por implementar', 'Información');
};


// =============================================
// CREAR CARD DE PRODUCTO
// =============================================

function crearCardProducto(producto, index) {
    const id = producto.id || index;
    const nombre = producto.nombre || 'Sin nombre';
    const codigo = producto.codigo || 'N/A';
    const precio = parseFloat(producto.precio) || 0;
    const stock = parseInt(producto.stock) || 0;
    const categoria = producto.categoria || 'General';
    
    // Determinar estilo según stock
    let stockClass = 'badge-success';
    let stockText = 'Disponible';
    if (stock <= 0) {
        stockClass = 'badge-danger';
        stockText = 'Agotado';
    } else if (stock <= 5) {
        stockClass = 'badge-warning';
        stockText = 'Bajo stock';
    }
    
    return `
        <div class="col-6 col-md-4 col-lg-3 mb-3">
            <div class="card h-100 shadow-sm border-hover" 
                 onclick="agregarProductoDesdeFrecuentes(${id})"
                 style="cursor: pointer; transition: all 0.3s;">
                <div class="card-body p-3 text-center">
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-fire"></i> Popular
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-box fa-lg text-primary"></i>
                        </div>
                    </div>
                    
                    <h6 class="card-title font-weight-bold mb-1" style="font-size: 0.9rem; min-height: 40px;">
                        ${nombre}
                    </h6>
                    
                    <p class="text-muted mb-2" style="font-size: 0.8rem;">
                        <small>${codigo} • ${categoria}</small>
                    </p>
                    
                    <div class="mb-2">
                        <h5 class="text-success font-weight-bold mb-0">
                            ${formatoDinero(precio)}
                        </h5>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge ${stockClass}">
                            <i class="fas fa-boxes"></i> ${stock} ${stockText}
                        </span>
                    </div>
                    
                    <button class="btn btn-sm btn-success btn-block" 
                            onclick="event.stopPropagation(); agregarProductoDesdeFrecuentes(${id});">
                        <i class="fas fa-cart-plus mr-1"></i> Agregar
                    </button>
                </div>
            </div>
        </div>
    `;
}

// =============================================
// FUNCIÓN PARA AGREGAR PRODUCTO
// =============================================

window.agregarProductoDesdeFrecuentes = function(id) {
    console.log('➕ Agregando producto ID:', id);
    
    // Buscar en productos cargados
    let producto = productos[id];
    
    if (producto) {
        agregarAlCarrito(producto);
        toastr.success(`"${producto.nombre}" agregado al carrito`);
        return;
    }
    
    // Si no está en productos cargados, buscar en el array actual
    const contenedor = $('#productosFrecuentes');
    const productoCard = contenedor.find(`[onclick*="${id}"]`);
    
    if (productoCard.length) {
        const nombre = productoCard.find('.card-title').text().trim();
        
        // Crear producto temporal
        const productoTemp = {
            id: id,
            nombre: nombre,
            precio: 0,
            stock: 10,
            codigo: 'TEMP'
        };
        
        agregarAlCarrito(productoTemp);
        toastr.success(`"${nombre}" agregado al carrito`);
    } else {
        toastr.warning('Producto no encontrado', 'Aviso');
    }
};



function mostrarErrorSinProductos() {
    $('#productosFrecuentes').html(`
        <div class="col-12">
            <div class="alert alert-warning text-center py-4">
                <i class="fas fa-database fa-2x mb-3"></i>
                <h5>No hay productos en el sistema</h5>
                <p class="mb-0">Agrega productos primero en el módulo de inventario</p>
            </div>
        </div>
    `);
}

// =============================================
// ACTUALIZAR FUNCIÓN mostrarProductosFrecuentes()
// =============================================

window.mostrarProductosFrecuentes = function() {
    console.log('📊 Mostrando sección de productos frecuentes');
    
    // Crear sección si no existe
    if (!$('#seccionProductosFrecuentes').length) {
        const html = `
            <div class="card mb-4 border-warning" id="seccionProductosFrecuentes">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-star mr-2"></i>
                            <strong>Productos Más Vendidos</strong>
                            <small class="ml-2">(Actualizados en tiempo real)</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-light mr-1" onclick="cargarProductosFrecuentes()" 
                                    title="Actualizar lista">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-light" onclick="$('#seccionProductosFrecuentes').hide();"
                                    title="Ocultar sección">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="productosFrecuentes">
                        <div class="col-12 text-center py-3">
                            <p class="text-muted">Iniciando carga de productos...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#dashboardRapido').after(html);
    }
    
    // Mostrar sección
    $('#seccionProductosFrecuentes').show();
    
    // Cargar productos
    cargarProductosFrecuentes();
};
  

// =============================================
// FUNCIONES AUXILIARES PARA AGREGAR PRODUCTOS FRECUENTES
// =============================================

window.agregarProductoFrecuenteDesdeTarjeta = function(id) {
    console.log('🛒 Agregando producto frecuente desde tarjeta:', id);
    agregarProductoFrecuente(id);
};

window.agregarProductoFrecuenteDesdeTabla = function(id) {
    console.log('🛒 Agregando producto frecuente desde tabla:', id);
    agregarProductoFrecuente(id);
};

function agregarProductoFrecuente(id) {
    const producto = productos[id];
    if (producto) {
        agregarAlCarrito(producto);
        toastr.success(`${producto.nombre} agregado al carrito`, 'Producto Frecuente');
    } else {
        console.error('❌ Producto no encontrado en productos[]:', id);
        toastr.error('Producto no disponible');
    }
}

// =============================================
// FUNCIÓN MODIFICADA PARA mostrarProductosFrecuentes()
// =============================================

window.mostrarProductosFrecuentes = function() {
    console.log('⭐ Función: mostrarProductosFrecuentes()');
    
    // Obtener la sección de productos frecuentes
    const $seccionFrecuentes = $('#seccionProductosFrecuentes');
    
    // Si la sección no existe en el DOM, crearla
    if (!$seccionFrecuentes.length) {
        console.log('📁 Creando sección de productos frecuentes...');
        
        const seccionHTML = `
            <div class="card mb-4" id="seccionProductosFrecuentes">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-star mr-2"></i>Productos Frecuentes
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-light mr-2" onclick="cargarProductosFrecuentes()" title="Recargar">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="ocultarProductosFrecuentes()" title="Ocultar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Vista de tarjetas -->
                    <div class="row" id="productosFrecuentes">
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-warning" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando productos frecuentes...</p>
                        </div>
                    </div>
                    
                    <!-- Vista de tabla (opcional) -->
                    <div class="mt-4" id="tablaFrecuentesContenedor" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm" id="tablaProductosFrecuentes">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Botones de vista -->
                    <div class="text-center mt-3">
                        <button class="btn btn-sm btn-outline-warning mr-2" onclick="cambiarVistaFrecuentes('tarjetas')">
                            <i class="fas fa-th-large"></i> Tarjetas
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="cambiarVistaFrecuentes('tabla')">
                            <i class="fas fa-table"></i> Tabla
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Insertar después del dashboard rápido
        $('#dashboardRapido').after(seccionHTML);
    }
    
    // Mostrar la sección
    $('#seccionProductosFrecuentes').show();
    
    // Cargar productos frecuentes si no están cargados
    if ($('#productosFrecuentes').is(':empty') || 
        $('#productosFrecuentes .spinner-border').length > 0) {
        cargarProductosFrecuentes();
    }
    
    toastr.success('Productos frecuentes mostrados', 'Frecuentes');
};

// =============================================
// FUNCIONES AUXILIARES PARA PRODUCTOS FRECUENTES
// =============================================

window.ocultarProductosFrecuentes = function() {
    $('#seccionProductosFrecuentes').hide();
    toastr.info('Productos frecuentes ocultados');
};

window.cambiarVistaFrecuentes = function(vista) {
    if (vista === 'tarjetas') {
        $('#productosFrecuentes').show();
        $('#tablaFrecuentesContenedor').hide();
        toastr.info('Vista de tarjetas activada');
    } else if (vista === 'tabla') {
        $('#productosFrecuentes').hide();
        $('#tablaFrecuentesContenedor').show();
        toastr.info('Vista de tabla activada');
    }
};


    // =============================================
    // 8. PROCESAR VENTA - FUNCIÓN PRINCIPAL CORREGIDA
    // =============================================


function verificarCarrito() {
    console.log('=== VERIFICACIÓN DEL CARRITO ===');
    console.log('1. Variable carrito:', carrito);
    console.log('2. Tipo:', typeof carrito);
    console.log('3. Es array?:', Array.isArray(carrito));
    console.log('4. Longitud:', carrito ? carrito.length : 'N/A');
    console.log('5. Contenido:', JSON.stringify(carrito));
    
    // También verifica el DOM
    const itemsEnTabla = $('#itemsCarrito tr').length;
    console.log('6. Filas en tabla carrito:', itemsEnTabla);
    
    return carrito && Array.isArray(carrito) && carrito.length > 0;
}

// Luego modifica el event listener:
$(document).on('click', '#btnProcesarVenta', function(e) {
    e.preventDefault();
    console.log('🖱️ Clic en botón COBRAR');
    
    // Usar la función de verificación
    const tieneProductos = verificarCarrito();
    
    if (!tieneProductos) {
        console.log('❌ VERIFICACIÓN: Carrito vacío');
        toastr.error('El carrito está vacío. Agregue productos antes de cobrar.', 'Error');
        return;
    }
    
    console.log('✅ VERIFICACIÓN: Carrito con productos - Procesando...');
    console.log('📋 Productos:', carrito.length);
        
    // Validar stock antes de procesar
    let stockValido = true;
    let erroresStock = [];
    
    carrito.forEach(function(item) {
        const producto = productos[item.id];
        if (!producto) {
            erroresStock.push('Producto ' + item.nombre + ' no encontrado');
            stockValido = false;
        } else if (producto.stock < item.cantidad) {
            erroresStock.push('Stock insuficiente para ' + item.nombre + '. Disponible: ' + producto.stock);
            stockValido = false;
        }
    });
    
    if (!stockValido) {
        erroresStock.forEach(function(error) {
            toastr.error(error, 'Error de stock');
        });
        return;
    }
    
    // Preparar datos de venta
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
        items: carrito.map(function(item) {
            return {
                producto_id: item.id,
                cantidad: item.cantidad,
                precio: Math.round(item.precio),
                subtotal: Math.round(item.precio * item.cantidad)
            };
        })
    };
    
    console.log('📤 Datos de venta preparados:', ventaData);
    
    // Mostrar loading
    const $btn = $(this);
    const textoOriginal = $btn.html();
    $btn.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
    
    // Obtener token CSRF
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    if (!csrfToken) {
        toastr.error('Token de seguridad no encontrado', 'Error');
        $btn.prop('disabled', false).html(textoOriginal);
        return;
    }
    
    console.log('🔐 Token CSRF:', csrfToken ? 'Presente' : 'Ausente');
    
    // Enviar al servidor
    $.ajax({
        url: '/procesar-venta',
        method: 'POST',
        data: JSON.stringify(ventaData),
        contentType: 'application/json',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(response) {
            console.log('✅ Respuesta del servidor:', response);
            
            if (response.success) {
                // MOSTRAR MENSAJE DE ÉXITO
                toastr.success('Venta guardada con éxito', '¡Éxito!');
                
                // Actualizar stock localmente
                carrito.forEach(function(item) {
                    if (productos[item.id]) {
                        productos[item.id].stock -= item.cantidad;
                    }
                });
                
                // MOSTRAR TICKET AUTOMÁTICAMENTE
                if (response.venta_completa) {
                    console.log('🎫 Mostrando ticket con datos del servidor');
                    mostrarTicketAutomatico(response.venta_completa);
                } else {
                    console.log('🎫 Mostrando ticket con datos locales');
                    mostrarVistaPrevia(response.numero_factura);
                }
                
                // REINICIAR FORMULARIO DESPUÉS DE 1 SEGUNDO
                setTimeout(function() {
                    console.log('🔄 Reiniciando formulario...');
                    reiniciarFormularioVenta();
                }, 1000);
                
            } else {
                console.error('❌ Error en respuesta:', response);
                toastr.error(response.message || 'Error al procesar la venta', 'Error');
                
                if (response.errors) {
                    Object.keys(response.errors).forEach(function(field) {
                        response.errors[field].forEach(function(error) {
                            toastr.error(error, 'Error de validación');
                        });
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en AJAX:', {
                status: xhr.status,
                statusText: xhr.statusText,
                error: error,
                responseText: xhr.responseText
            });
            
            let errorMessage = 'Error al procesar la venta';
            
            try {
                if (xhr.responseText) {
                    if (xhr.responseText.trim().startsWith('{')) {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                        if (errorResponse.errors) {
                            Object.keys(errorResponse.errors).forEach(function(field) {
                                errorResponse.errors[field].forEach(function(err) {
                                    toastr.error(err, 'Error');
                                });
                            });
                            return;
                        }
                    } else if (xhr.responseText.includes('CSRF')) {
                        errorMessage = 'Error de token CSRF. Recarga la página.';
                    }
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
            
            toastr.error(errorMessage, 'Error');
            
            // Si es error 403, probablemente problema con CSRF
            if (xhr.status === 403) {
                toastr.warning('Error de autenticación. Intenta recargar la página.', 'Token CSRF');
            }
        },
        complete: function() {
            $btn.prop('disabled', false).html(textoOriginal);
        }
    });
});


   
   function mostrarTicketAutomatico(datosVenta) {
    console.log('🎫 Mostrando ticket automático...', datosVenta);
    
    // Verificar que el modal existe
    if (!$('#modalVistaPrevia').length) {
        console.error('❌ Modal de vista previa no encontrado');
        toastr.error('No se pudo mostrar el ticket', 'Error');
        return;
    }
    
    // Si no hay datos de venta, usar los locales
    if (!datosVenta) {
        console.log('⚠️ No hay datos de venta, usando datos locales');
        mostrarVistaPrevia();
        return;
    }
    
    // Extraer datos de la venta
    const ventaData = {
        numeroFactura: datosVenta.numero_factura || numeroFactura,
        cliente: datosVenta.cliente ? datosVenta.cliente.nombre : 'Consumidor Final',
        cedula: datosVenta.cliente ? datosVenta.cliente.cedula : 'N/A',
        telefono: datosVenta.cliente ? datosVenta.cliente.telefono : 'N/A',
        items: [],
        subtotal: datosVenta.subtotal || window.ventaSubtotalNumerico || 0,
        iva: datosVenta.iva || window.ventaIvaNumerico || 0,
        total: datosVenta.total || window.ventaTotalNumerico || 0,
        tipo: datosVenta.tipo_comprobante || $('#tipoComprobante').val() || 'ticket',
        fecha: datosVenta.fecha_venta || new Date().toLocaleString(),
        metodoPago: datosVenta.metodo_pago || $('#metodoPago').val(),
        porcentajeIva: parseFloat($('#selectIva').val()) || 16,
        cambio: datosVenta.cambio || 0,
        efectivoRecibido: datosVenta.efectivo_recibido || 0
    };
    
    // Obtener items
    if (datosVenta.detalles && datosVenta.detalles.length > 0) {
        ventaData.items = datosVenta.detalles.map(function(detalle) {
            return {
                id: detalle.id_producto,
                nombre: detalle.producto ? detalle.producto.nombre : 'Producto',
                cantidad: detalle.cantidad,
                precio: detalle.precio_unitario,
                subtotal: detalle.subtotal,
                codigo: detalle.producto ? detalle.producto.codigo : '',
                categoria: detalle.producto ? detalle.producto.categoria : ''
            };
        });
    } else {
        ventaData.items = carrito;
    }
    
    // Generar HTML del comprobante
    const comprobanteHTML = generarComprobanteHTML(ventaData);
    
    // Verificar que el contenedor existe
    if (!$('#vistaPreviaComprobante').length) {
        console.error('❌ Contenedor de vista previa no encontrado');
        return;
    }
    
    // Insertar HTML
    $('#vistaPreviaComprobante').html(comprobanteHTML);
    
    // Mostrar modal
    try {
        $('#modalVistaPrevia').modal('show');
        console.log('✅ Modal de vista previa mostrado');
    } catch (e) {
        console.error('❌ Error al mostrar modal:', e);
        toastr.error('Error al mostrar el ticket', 'Error');
    }
}

    // Función auxiliar para obtener referencia de pago
    function obtenerReferenciaPago() {
        const metodo = $('#metodoPago').val();
        
        switch(metodo) {
            case 'efectivo':
                return null;
            case 'tarjeta':
                return $('#numeroTarjeta').val() || 'Tarjeta';
            case 'transferencia':
                return $('#referenciaTransaccion').val() || 'Transferencia';
            case 'cheque':
                return $('#referenciaTransaccion').val() || 'Cheque';
            case 'mixto':
                return 'Mixto: Efectivo ' + ($('#montoEfectivoMixto').val() || 0) + ', Tarjeta ' + ($('#montoTarjetaMixto').val() || 0);
            default:
                return null;
        }
    }
    
    // =============================================
    // 9. FUNCIÓN PARA MOSTRAR TICKET AUTOMÁTICAMENTE
    // =============================================
    function mostrarTicketAutomatico(datosVenta) {
        console.log('🎫 Mostrando ticket automático...');
        
        // Si no hay datos de venta, usar los locales
        if (!datosVenta) {
            mostrarVistaPrevia();
            return;
        }
        
        // Extraer datos de la venta
        const ventaData = {
            numeroFactura: datosVenta.numero_factura || numeroFactura,
            cliente: datosVenta.cliente ? datosVenta.cliente.nombre : 'Consumidor Final',
            cedula: datosVenta.cliente ? datosVenta.cliente.cedula : 'N/A',
            telefono: datosVenta.cliente ? datosVenta.cliente.telefono : 'N/A',
            items: [],
            subtotal: datosVenta.subtotal || window.ventaSubtotalNumerico || 0,
            iva: datosVenta.iva || window.ventaIvaNumerico || 0,
            total: datosVenta.total || window.ventaTotalNumerico || 0,
            tipo: datosVenta.tipo_comprobante || $('#tipoComprobante').val() || 'ticket',
            fecha: datosVenta.fecha_venta || new Date().toLocaleString(),
            metodoPago: datosVenta.metodo_pago || $('#metodoPago').val(),
            porcentajeIva: parseFloat($('#selectIva').val()) || 16,
            cambio: datosVenta.cambio || 0,
            efectivoRecibido: datosVenta.efectivo_recibido || 0
        };
        
        // Si hay detalles de venta del servidor, usarlos
        if (datosVenta.detalles && datosVenta.detalles.length > 0) {
            ventaData.items = datosVenta.detalles.map(function(detalle) {
                return {
                    id: detalle.id_producto,
                    nombre: detalle.producto ? detalle.producto.nombre : 'Producto',
                    cantidad: detalle.cantidad,
                    precio: detalle.precio_unitario,
                    subtotal: detalle.subtotal,
                    codigo: detalle.producto ? detalle.producto.codigo : '',
                    categoria: detalle.producto ? detalle.producto.categoria : ''
                };
            });
        } else {
            // Si no hay detalles del servidor, usar el carrito local
            ventaData.items = carrito;
        }
        
        // Generar y mostrar el comprobante
        $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
        $('#modalVistaPrevia').modal('show');
    }

    // =============================================
    // 10. FUNCIÓN REINICIAR FORMULARIO VENTA
    // =============================================
    function reiniciarFormularioVenta() {
        console.log('🔄 Reiniciando formulario de venta...');
        
        // 1. Limpiar carrito
        carrito = [];
        actualizarCarrito();
        actualizarMetricas();
        
        // 2. Resetear cliente
        $('#selectCliente').val(null).trigger('change');
        clienteSeleccionado = null;
        $('#btnQuitarCliente').hide();
        
        // 3. Resetear montos
        $('#subtotalVenta').text('0');
        $('#ivaVenta').text('0');
        $('#totalVenta').text('$0');
        $('#porcentajeIva').text('16%');
        
        // 4. Resetear métodos de pago
        $('#metodoPago').val('efectivo');
        $('#efectivoRecibido').val('0');
        $('#cambioVenta').text('$0');
        
        // 5. Resetear otros inputs
        $('#tipoComprobante').val('ticket');
        $('#selectIva').val('16');
        
        // 6. Resetear campos de otros métodos de pago
        $('#numeroTarjeta').val('');
        $('#fechaVencimiento').val('');
        $('#cvvTarjeta').val('');
        $('#nombreTitular').val('');
        $('#montoEfectivoMixto').val('0');
        $('#montoTarjetaMixto').val('0');
        $('#referenciaTransaccion').val('');
        
        // 7. Mostrar solo pago efectivo
        $('.metodo-pago-detalle').addClass('d-none');
        $('#pagoEfectivo').removeClass('d-none');
        
        // 8. Generar nuevo número de factura
        numeroFactura = generarNumeroFactura();
        $('#numeroFactura').text(numeroFactura);
        
        console.log('✅ Formulario de venta reiniciado');
    }

    // =============================================
    // 11. VISTA PREVIA Y COMPROBANTES
    // =============================================
    function mostrarVistaPrevia(numeroFacturaServidor = null) {
        if (carrito.length === 0) {
            console.log('Carrito vacío, no se puede mostrar vista previa');
            return;
        }
        
        const tipoComprobante = $('#tipoComprobante').val();
        
        let subtotal = window.ventaSubtotalNumerico || 0;
        let iva = window.ventaIvaNumerico || 0;
        let total = window.ventaTotalNumerico || 0;
        
        if (subtotal === 0 && carrito.length > 0) {
            subtotal = carrito.reduce(function(sum, item) {
                return sum + (item.precio * item.cantidad);
            }, 0);
            const ivaPorcentaje = parseFloat($('#selectIva').val()) || 0;
            iva = Math.round(subtotal * (ivaPorcentaje / 100));
            total = Math.round(subtotal + iva);
        }
        
        const ventaData = {
            numeroFactura: numeroFactura,
            cliente: clienteSeleccionado ? clienteSeleccionado.nombre : 'Consumidor Final',
            cedula: clienteSeleccionado ? clienteSeleccionado.cedula : 'N/A',
            telefono: clienteSeleccionado ? clienteSeleccionado.telefono : 'N/A',
            items: carrito,
            subtotal: Math.round(subtotal),
            iva: Math.round(iva),
            total: Math.round(total),
            tipo: tipoComprobante,
            fecha: new Date().toLocaleString(),
            metodoPago: $('#metodoPago').val(),
            porcentajeIva: parseFloat($('#selectIva').val()) || 0,
            cambio: Math.round(parseFloat(window.cambioNumerico) || 0),
            efectivoRecibido: Math.round(parseFloat($('#efectivoRecibido').val().replace(/\./g, '')) || 0)
        };
        
        $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
        $('#modalVistaPrevia').modal('show');
    }
    
    function generarComprobanteHTML(ventaData) {
        const esFactura = ventaData.tipo !== 'ticket';
        const esTicket = ventaData.tipo === 'ticket';
        const cedulaCliente = ventaData.cedula || (clienteSeleccionado ? clienteSeleccionado.cedula : 'N/A');
        
        // Formatear valores de pago
        const cambio = ventaData.cambio || 0;
        const efectivoRecibido = ventaData.efectivoRecibido || 0;
        
        if (esTicket) {
            return `
            <div class="comprobante-ticket" style="width: 80mm; font-family: 'Courier New', monospace; font-size: 12px;">
                <div class="text-center">
                    <h4 style="margin: 5px 0; font-weight: bold;">FERRETERÍA</h4>
                    <h5 style="margin: 3px 0; font-weight: bold;">"EL MARTILLO"</h5>
                    <p style="margin: 2px 0;">NIT: FME850301XYZ</p>
                    <p style="margin: 2px 0;">Tel: (555) 123-4567</p>
                    <p style="margin: 2px 0;">Av. Principal #123</p>
                </div>
                
                <hr style="border-top: 1px dashed #000; margin: 8px 0;">
                
                <div style="margin: 5px 0;">
                    <strong>TICKET:</strong> ${ventaData.numeroFactura}<br>
                    <strong>FECHA:</strong> ${ventaData.fecha}<br>
                    <strong>CLIENTE:</strong> ${ventaData.cliente}<br>
                    <strong>CEDULA:</strong> ${cedulaCliente}
                </div>
                
                <hr style="border-top: 1px dashed #000; margin: 8px 0;">
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; border-bottom: 1px dashed #000; padding: 3px 0;">CANT DESC</th>
                            <th style="text-align: right; border-bottom: 1px dashed #000; padding: 3px 0;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${ventaData.items.map(function(item) {
                            return `
                                <tr>
                                    <td style="padding: 2px 0;">
                                        ${item.cantidad} x ${item.nombre.substring(0, 20)}
                                    </td>
                                    <td style="text-align: right; padding: 2px 0;">
                                        ${formatoDinero(item.precio * item.cantidad)}
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
                
                <hr style="border-top: 1px dashed #000; margin: 8px 0;">
                
                <table style="width: 100%;">
                    <tr>
                        <td>SUBTOTAL:</td>
                        <td style="text-align: right;">${formatoDinero(ventaData.subtotal)}</td>
                    </tr>
                    <tr>
                        <td>IVA:</td>
                        <td style="text-align: right;">${formatoDinero(ventaData.iva)}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td>TOTAL:</td>
                        <td style="text-align: right;">${formatoDinero(ventaData.total)}</td>
                    </tr>
                    ${ventaData.metodoPago === 'efectivo' ? `
                    <tr>
                        <td>EFECTIVO:</td>
                        <td style="text-align: right;">${formatoDinero(efectivoRecibido)}</td>
                    </tr>
                    <tr>
                        <td>CAMBIO:</td>
                        <td style="text-align: right;">${formatoDinero(cambio)}</td>
                    </tr>
                    ` : ''}
                </table>
                
                <hr style="border-top: 1px dashed #000; margin: 8px 0;">
                
                <div style="text-align: center; margin: 10px 0;">
                    <p style="margin: 3px 0;"><strong>PAGO:</strong> ${ventaData.metodoPago.toUpperCase()}</p>
                    <p style="margin: 3px 0;">¡GRACIAS POR SU COMPRA!</p>
                    <p style="margin: 3px 0; font-size: 10px;">*** TICKET NO FISCAL ***</p>
                </div>
            </div>
            `;
        } else {
            return `
            <div class="comprobante-factura">
                <div class="text-center mb-3">
                    <h2>${esFactura ? 'FACTURA' : 'COMPROBANTE'}</h2>
                    <h4>FERRETERÍA "EL MARTILLO"</h4>
                    <p>RFC: FME850301XYZ • Tel: (555) 123-4567</p>
                    <p>Av. Principal #123, Col. Centro</p>
                </div>
                
                <table class="table table-bordered table-sm">
                    <tr>
                        <td><strong>No. Documento:</strong></td>
                        <td>${ventaData.numeroFactura}</td>
                        <td><strong>Fecha:</strong></td>
                        <td>${ventaData.fecha}</td>
                    </tr>
                    <tr>
                        <td><strong>Cliente:</strong></td>
                        <td>${ventaData.cliente}</td>
                    </tr>
                    <tr>
                        <td><strong>Cédula:</strong></td>
                        <td>${cedulaCliente}</td>
                        <td><strong>Teléfono:</strong></td>
                        <td>${ventaData.telefono}</td>
                    </tr>
                </table>
                
                <table class="table table-bordered table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cant.</th>
                            <th>Descripción</th>
                            <th>P.Unit</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${ventaData.items.map(function(item) {
                            return `
                                <tr>
                                    <td>${item.cantidad}</td>
                                    <td>${item.nombre}</td>
                                    <td>${formatoDinero(item.precio)}</td>
                                    <td>${formatoDinero(item.precio * item.cantidad)}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
                
                <table class="table table-bordered table-sm float-right" style="width: 300px;">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td style="text-align: right;">${formatoDinero(ventaData.subtotal)}</td>
                    </tr>
                    <tr>
                        <td><strong>IVA:</strong></td>
                       <td style="text-align: right;">${formatoDinero(ventaData.iva)}</td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>TOTAL:</strong></td>
                       <td style="text-align: right;">${formatoDinero(ventaData.total)}</td>
                    </tr>
                    ${ventaData.metodoPago === 'efectivo' ? `
                    <tr>
                        <td><strong>Efectivo Recibido:</strong></td>
                        <td style="text-align: right;">${formatoDinero(efectivoRecibido)}</td>
                    </tr>
                    <tr>
                        <td><strong>Cambio:</strong></td>
                        <td style="text-align: right;">${formatoDinero(cambio)}</td>
                    </tr>
                    ` : ''}
                </table>
                
                <div class="clearfix"></div>
                
                <div class="mt-4 text-center">
                    <p><strong>Método de Pago:</strong> ${ventaData.metodoPago.toUpperCase()}</p>
                    <p class="text-muted">¡Gracias por su compra!</p>
                    <small class="text-muted">
                        ${esFactura ? 
                          '*** Este documento es una factura fiscal ***' : 
                          '*** Comprobante de venta ***'}
                    </small>
                </div>
            </div>
            `;
        }
    }
    
    $(document).on('click', '#btnImprimir', function() {
        const tipoComprobante = $('#tipoComprobante').val();
        const esTicket = tipoComprobante === 'ticket';
        
        const ventana = window.open('', '_blank');
        const estilo = esTicket ? 
            '<style>@media print { body { margin: 0; padding: 0; } .comprobante-ticket { width: 80mm; font-family: "Courier New", monospace; font-size: 12px; } }</style>' : 
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        
        ventana.document.write('<!DOCTYPE html><html><head><title>Comprobante - ' + numeroFactura + '</title>' + estilo + '</head><body>' + $('#vistaPreviaComprobante').html() + '<script>window.onload = function() { window.print(); setTimeout(function() { window.close(); }, 1000); }<\/script></body></html>');
        ventana.document.close();
        
        toastr.success('Comprobante enviado a impresión', 'Impresión');
    });
    
    $(document).on('click', '#btnNuevaVenta', function() {
        $('#modalVistaPrevia').modal('hide');
        reiniciarVenta();
        toastr.success('Nueva venta iniciada', 'Sistema');
    });
    
    function reiniciarVenta() {
        carrito = [];
        clienteSeleccionado = null;
        numeroFactura = generarNumeroFactura();
        
        $('#numeroFactura').text(numeroFactura);
        $('#selectCliente').val('').trigger('change');
        $('#infoCliente').addClass('d-none');
        $('#efectivoRecibido').val('0');
        $('#selectIva').val('16');
        $('#metodoPago').val('efectivo');
        $('#tipoComprobante').val('ticket');
        
        $('#numeroTarjeta').val('');
        $('#fechaVencimiento').val('');
        $('#cvvTarjeta').val('');
        $('#nombreTitular').val('');
        $('#montoEfectivoMixto').val('0');
        $('#montoTarjetaMixto').val('0');
        $('#referenciaTransaccion').val('');
        
        $('.metodo-pago-detalle').addClass('d-none');
        $('#pagoEfectivo').removeClass('d-none');
        
        actualizarCarrito();
        actualizarMetricas();
        
        cargarProductosDesdeDB();
    }
    
    // =============================================
    // 12. EVENTOS DE BOTONES
    // =============================================
    $(document).on('click', '#btnQuitarClienteInfo', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🔴 Clic en btnQuitarClienteInfo');
        limpiarClienteSeleccionado();
        toastr.info('Cliente removido');
        return false;
    });
    
    $(document).on('click', '#btnQuitarCliente', function(e) {
        e.preventDefault();
        console.log('🔴 Clic en btnQuitarCliente (header)');
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
            // SOLO muestra mensaje informativo, NO error
            toastr.info('No hay productos en el carrito para imprimir', 'Información');
            return;
        }
        mostrarVistaPrevia();
        toastr.info('Generando vista previa para impresión', 'Impresión');
    });

    // =============================================
    // CONFIGURACIÓN DEL ESCÁNER MODAL
    // =============================================
    
    $(document).on('click', '#btnOpenScanner', function() {
        console.log('🟢 Botón escáner clickeado');
        $('#modalScanner').modal('show');
        
        setTimeout(function() {
            $('#inputCodigoManual').focus();
            console.log('🔍 Input de código enfocado');
        }, 500);
    });
    
    $(document).on('keydown', '#inputCodigoManual', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            console.log('⏎ Enter presionado en input de código');
            
            setTimeout(function() {
                procesarCodigoEscaneado();
            }, 10);
        }
        
        if (e.key === 'Tab') {
            e.preventDefault();
            console.log('↹ Tab presionado en input de código');
            
            setTimeout(function() {
                procesarCodigoEscaneado();
            }, 10);
        }
    });
    
    $(document).on('click', '#btnProcesarCodigo', function() {
        console.log('🟡 Botón procesar código clickeado');
        procesarCodigoEscaneado();
    });
    
    $('#modalScanner').on('hidden.bs.modal', function() {
        console.log('🔴 Modal escáner cerrado');
        $('#inputCodigoManual').val('');
    });
    
    $('#modalScanner').on('shown.bs.modal', function() {
        console.log('🟢 Modal escáner abierto');
        $('#inputCodigoManual').focus();
    });
    
    function procesarCodigoEscaneado() {
        const codigo = $('#inputCodigoManual').val().trim();
        
        console.log('📋 Código a procesar:', codigo);
        
        if (!codigo) {
            toastr.warning('Ingrese un código para escanear', 'Escáner');
            $('#inputCodigoManual').focus();
            return;
        }
        
        console.log('🔍 Procesando código escaneado:', codigo);
        
        $('#inputCodigoManual').prop('disabled', true);
        
        buscarProductoPorCodigo(codigo);
        
        setTimeout(function() {
            $('#inputCodigoManual').prop('disabled', false).focus();
        }, 100);
    }
    
    function buscarProductoPorCodigo(codigo) {
        console.log('🔎 Buscando producto con código:', codigo);
        
        if (Object.keys(productos).length === 0) {
            toastr.error('No hay productos cargados en el sistema', 'Error');
            $('#inputCodigoManual').focus();
            return;
        }
        
        const productoEncontrado = Object.values(productos).find(function(producto) {
            const codigoProducto = producto.codigo ? producto.codigo.toString().trim() : '';
            const codigoBuscado = codigo.toString().trim();
            
            return codigoProducto === codigoBuscado;
        });
        
        if (productoEncontrado) {
            console.log('✅ Producto encontrado:', productoEncontrado);
            
            if (productoEncontrado.stock <= 0) {
                toastr.error('Producto sin stock disponible', 'Stock');
                $('#inputCodigoManual').val('').focus();
                return;
            }
            
            const productoEnCarrito = carrito.find(item => item.id === productoEncontrado.id);
            const cantidadActual = productoEnCarrito ? productoEnCarrito.cantidad : 0;
            
            if (cantidadActual >= productoEncontrado.stock) {
                toastr.error(`Stock máximo alcanzado. Disponible: ${productoEncontrado.stock}`, 'Stock');
                $('#inputCodigoManual').val('').focus();
                return;
            }
            
            if (productoEnCarrito) {
                productoEnCarrito.cantidad += 1;
                toastr.success(`"${productoEncontrado.nombre}" - Cantidad aumentada a ${productoEnCarrito.cantidad}`, 'Carrito');
            } else {
                carrito.push({
                    id: productoEncontrado.id,
                    nombre: productoEncontrado.nombre,
                    precio: productoEncontrado.precio,
                    cantidad: 1,
                    stock: productoEncontrado.stock,
                    codigo: productoEncontrado.codigo,
                    categoria: productoEncontrado.categoria
                });
                toastr.success(`"${productoEncontrado.nombre}" agregado al carrito`, 'Carrito');
            }
            
            actualizarCarrito();
            actualizarMetricas();
            
            setTimeout(function() {
                $('#inputCodigoManual').val('').focus();
            }, 100);
            
        } else {
            console.log('❌ Producto no encontrado con código:', codigo);
            
            toastr.error(`No se encontró producto con código: "${codigo}"`, 'Producto no encontrado');
            
            setTimeout(function() {
                $('#inputCodigoManual').focus().select();
            }, 100);
        }
    }
    
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'e' && !$(e.target).is('input, textarea, select')) {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+E presionado');
            $('#modalScanner').modal('show');
        }
        
        if (e.key === 'F2') {
            e.preventDefault();
            console.log('⌨️ Tecla F2 presionada');
            $('#modalScanner').modal('show');
        }
        
        if (e.key === 'Escape' && $('#modalScanner').hasClass('show')) {
            $('#modalScanner').modal('hide');
        }
    });
    
    $(document).on('click', '#modalScanner .close, #modalScanner [data-bs-dismiss="modal"]', function(e) {
        e.preventDefault();
        console.log('🔴 Intentando cerrar modal escáner');
        
        $('#modalScanner').modal('hide');
        
        $('.modal-backdrop').remove();
        
        $('body').removeClass('modal-open');
    });
    
    $(document).on('click', function(e) {
        if ($(e.target).closest('#modalScanner .btn-secondary').length) {
            console.log('🟡 Botón Cancelar clickeado');
            $('#modalScanner').modal('hide');
        }
    });
    
    function cerrarModalScanner() {
        console.log('🔄 Ejecutando cerrarModalScanner()');
        
        $('#modalScanner').modal('hide');
        
        setTimeout(function() {
            $('#modalScanner').removeClass('show');
            $('#modalScanner').css('display', 'none');
            
            $('.modal-backdrop').remove();
            
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            
            console.log('✅ Modal cerrado manualmente');
        }, 100);
    }
    
    // =============================================
    // 13. INICIALIZACIÓN CORREGIDA
    // =============================================
    function inicializarSistema() {
    console.log('🚀 Inicializando sistema...');
    
    configurarSelect2Clientes();
    configurarNuevoCliente(); 
    cargarProductosDesdeDB();
    configurarMetodosPago();
    configurarBusquedaTiempoReal();
    configurarInputEfectivo();
    configurarAtajosTeclado();
    
    // Cargar productos frecuentes al iniciar (pero ocultos)
    setTimeout(function() {
        cargarProductosFrecuentes();
    }, 1000);
    
    $('#selectIva').on('change', function() {
        const subtotal = parseFloat(window.ventaSubtotalNumerico) || 0;
        actualizarTotales(subtotal);
    });
    
    $('#numeroFactura').text(numeroFactura);
    
    console.log('✅ Sistema inicializado');
    toastr.success('Sistema de punto de venta listo');
}
    
    // Iniciar sistema cuando el documento esté listo
    $(document).ready(function() {
        setTimeout(inicializarSistema, 500);
    });


// 1. VENTA RÁPIDA
window.iniciarVentaRapida = function() {
    console.log('🚀 Función: iniciarVentaRapida()');
    
    // Ocultar todas las secciones primero
    $('#seccionBusquedaRapida').hide();
    $('#seccionProductosFrecuentes').hide();
    
    // Enfocar en el campo de código del escáner
    setTimeout(function() {
        $('#modalScanner').modal('show');
        setTimeout(function() {
            $('#inputCodigoManual').focus();
            console.log('🔍 Escáner listo para venta rápida');
        }, 300);
    }, 100);
    
    // Mostrar mensaje de ayuda
    toastr.info('Modo venta rápida activado. Usa el escáner o ingresa códigos manualmente.', 'Venta Rápida', {
        timeOut: 5000,
        positionClass: "toast-top-center"
    });
};

// 2. BUSCAR
window.activarBusquedaRapida = function() {
    console.log('🔍 Función: activarBusquedaRapida()');
    
    // Mostrar sección de búsqueda
    $('#seccionBusquedaRapida').show();
    
    // Enfocar y seleccionar texto en campo de búsqueda
    setTimeout(function() {
        $('#busquedaRapida').focus().select();
        console.log('🔍 Campo de búsqueda enfocado');
    }, 100);
    
    // Ocultar otras secciones
    $('#seccionProductosFrecuentes').hide();
    
    toastr.info('Escribe para buscar productos por código, nombre o categoría', 'Búsqueda Rápida', {
        timeOut: 4000
    });
};

// 3. FRECUENTES
window.mostrarProductosFrecuentes = function() {
    console.log('⭐ Función: mostrarProductosFrecuentes()');
    
    // Alternar visibilidad de la sección
    const $seccionFrecuentes = $('#seccionProductosFrecuentes');
    if ($seccionFrecuentes.is(':visible')) {
        $seccionFrecuentes.hide();
        toastr.info('Productos frecuentes ocultos');
    } else {
        $seccionFrecuentes.show();
        
        // Si no hay productos frecuentes cargados, cargarlos
        if ($('#productosFrecuentes').is(':empty')) {
            cargarProductosFrecuentes();
        }
        
        toastr.success('Productos frecuentes mostrados', 'Frecuentes');
    }
    
    // Ocultar otras secciones
    $('#seccionBusquedaRapida').hide();
};

// 4. MÉTRICAS
window.mostrarMetricas = function() {
    console.log('📊 Función: mostrarMetricas()');
    
    // Calcular métricas actuales
    let totalProductos = 0;
    let totalVenta = 0;
    let productosUnicos = 0;
    
    if (carrito && Array.isArray(carrito)) {
        productosUnicos = carrito.length;
        carrito.forEach(function(item) {
            totalProductos += item.cantidad || 0;
            totalVenta += (item.precio || 0) * (item.cantidad || 0);
        });
    }
    
    // Crear contenido del modal de métricas
    const contenidoMetricas = `
        <div class="modal fade" id="modalMetricas" tabindex="-1" aria-labelledby="modalMetricasLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalMetricasLabel">
                            <i class="fas fa-chart-bar mr-2"></i>Métricas de Venta Actual
                        </h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary shadow-sm">
                                    <div class="card-body text-center py-3">
                                        <div class="text-primary mb-2">
                                            <i class="fas fa-boxes fa-2x"></i>
                                        </div>
                                        <h2 class="text-primary mb-1">${productosUnicos}</h2>
                                        <p class="card-text mb-0">Productos diferentes</p>
                                        <small class="text-muted">En el carrito</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-success shadow-sm">
                                    <div class="card-body text-center py-3">
                                        <div class="text-success mb-2">
                                            <i class="fas fa-layer-group fa-2x"></i>
                                        </div>
                                        <h2 class="text-success mb-1">${totalProductos}</h2>
                                        <p class="card-text mb-0">Unidades totales</p>
                                        <small class="text-muted">Suma de cantidades</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-warning shadow-sm">
                                    <div class="card-body text-center py-3">
                                        <div class="text-warning mb-2">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
                                        </div>
                                        <h3 class="text-warning mb-1">${formatoDinero(totalVenta)}</h3>
                                        <p class="card-text mb-0">Valor total</p>
                                        <small class="text-muted">Subtotal sin IVA</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumen de IVA y Total -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-secondary">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-percentage mr-1"></i>IVA Aplicado</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Porcentaje:</span>
                                            <span class="font-weight-bold">${$('#selectIva').val()}%</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span>Monto IVA:</span>
                                            <span class="font-weight-bold text-info">${formatoDinero(window.ventaIvaNumerico || 0)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-calculator mr-1"></i>Total Final</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Total con IVA:</span>
                                            <span class="font-weight-bold text-success">${formatoDinero(window.ventaTotalNumerico || 0)}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span>Método de pago:</span>
                                            <span class="badge badge-primary">${$('#metodoPago').val()}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detalle del carrito -->
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-list-alt mr-2"></i>Detalle del Carrito
                        </h5>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-right">Precio Unit.</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${carrito && carrito.length > 0 ? 
                                        carrito.map(function(item) {
                                            const subtotalItem = (item.precio || 0) * (item.cantidad || 0);
                                            return `
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-light rounded p-1 mr-2">
                                                                <i class="fas fa-box text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold">${item.nombre || 'Sin nombre'}</div>
                                                                <small class="text-muted">${item.codigo || 'Sin código'}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-pill badge-primary">${item.cantidad || 0}</span>
                                                    </td>
                                                    <td class="text-right align-middle">${formatoDinero(item.precio || 0)}</td>
                                                    <td class="text-right align-middle font-weight-bold">${formatoDinero(subtotalItem)}</td>
                                                </tr>
                                            `;
                                        }).join('') : 
                                        `<tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-shopping-cart fa-2x mb-3 d-block"></i>
                                                No hay productos en el carrito
                                            </td>
                                        </tr>`
                                    }
                                </tbody>
                                ${carrito && carrito.length > 0 ? `
                                    <tfoot class="thead-light">
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total Carrito:</strong></td>
                                            <td class="text-right font-weight-bold text-success">${formatoDinero(totalVenta)}</td>
                                        </tr>
                                    </tfoot>
                                ` : ''}
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                        <button type="button" class="btn btn-info" onclick="actualizarMetricasVista()">
                            <i class="fas fa-sync-alt mr-1"></i>Actualizar
                        </button>
                        <button type="button" class="btn btn-success" onclick="irACobrar()">
                            <i class="fas fa-cash-register mr-1"></i>Ir a Cobrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    if ($('#modalMetricas').length) {
        $('#modalMetricas').remove();
    }
    
    // Agregar modal al DOM y mostrarlo
    $('body').append(contenidoMetricas);
    $('#modalMetricas').modal('show');
    
    // Configurar evento para actualizar al cerrar
    $('#modalMetricas').on('hidden.bs.modal', function() {
        $(this).remove();
    });
};

// Función auxiliar para actualizar métricas desde el modal
window.actualizarMetricasVista = function() {
    if ($('#modalMetricas').is(':visible')) {
        $('#modalMetricas').modal('hide');
        setTimeout(mostrarMetricas, 300);
    }
};

// Función para ir directamente a cobrar
window.irACobrar = function() {
    if ($('#modalMetricas').is(':visible')) {
        $('#modalMetricas').modal('hide');
    }
    
    // Desplazar a la sección de cobro
    $('html, body').animate({
        scrollTop: $('#seccionCobro').offset().top - 20
    }, 500);
    
    // Enfocar en el método de pago
    setTimeout(function() {
        $('#metodoPago').focus();
        toastr.info('Listo para procesar el cobro', 'Cobro');
    }, 600);
};

// =============================================
// 16. ATAAJOS DE TECLADO - VENTANA MODAL F1
// =============================================

function configurarAtajosTeclado() {
    console.log('⌨️ Configurando atajos de teclado...');
    
    // Detectar tecla F1 para mostrar atajos
    $(document).on('keydown', function(e) {
        // F1 - Mostrar atajos
        if (e.key === 'F1' || e.keyCode === 112) {
            e.preventDefault();
            console.log('⌨️ Tecla F1 presionada');
            mostrarModalAtajos();
            return false;
        }
        
        // Atajo Ctrl+1 - Venta Rápida
        if (e.ctrlKey && e.key === '1') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+1 - Venta Rápida');
            iniciarVentaRapida();
            return false;
        }
        
        // Atajo Ctrl+2 - Buscar
        if (e.ctrlKey && e.key === '2') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+2 - Buscar');
            activarBusquedaRapida();
            return false;
        }
        
        // Atajo Ctrl+3 - Frecuentes
        if (e.ctrlKey && e.key === '3') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+3 - Frecuentes');
            mostrarProductosFrecuentes();
            return false;
        }
        
        // Atajo Ctrl+4 - Métricas
        if (e.ctrlKey && e.key === '4') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+4 - Métricas');
            mostrarMetricas();
            return false;
        }
        
        // Atajo Ctrl+B - Alternar búsqueda
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+B - Alternar búsqueda');
            if ($('#seccionBusquedaRapida').is(':visible')) {
                $('#seccionBusquedaRapida').hide();
                toastr.info('Búsqueda ocultada');
            } else {
                activarBusquedaRapida();
            }
            return false;
        }
        
        // Atajo Ctrl+F - Frecuentes
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+F - Frecuentes');
            mostrarProductosFrecuentes();
            return false;
        }
        
        // Atajo Ctrl+M - Métricas
        if (e.ctrlKey && e.key === 'm') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+M - Métricas');
            mostrarMetricas();
            return false;
        }
        
        // Atajo Ctrl+S - Procesar venta
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+S - Procesar venta');
            $('#btnProcesarVenta').click();
            return false;
        }
    });
}

function mostrarModalAtajos() {
    console.log('🔄 Mostrando modal de atajos');
    
    const contenidoAtajos = `
        <div class="modal fade" id="modalAtajos" tabindex="-1" aria-labelledby="modalAtajosLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light ">
                        <h5 class="modal-title" id="modalAtajosLabel">
                            <i class="fas fa-keyboard mr-2"></i>Atajos de Teclado - Sistema POS
                        </h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-primary">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Presiona F1</strong> en cualquier momento para ver esta ventana de atajos
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Rápido
                                </h5>
                                <table class="table table-sm table-hover">
                                    <tr class="table-primary">
                                        <td width="100"><span class="badge badge-primary">Ctrl + 1</span></td>
                                        <td><strong>Venta Rápida</strong></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-outline-primary" onclick="iniciarVentaRapida()">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><span class="badge badge-success">Ctrl + 2</span></td>
                                        <td><strong>Buscar Productos</strong></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-outline-success" onclick="activarBusquedaRapida()">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><span class="badge badge-warning">Ctrl + 3</span></td>
                                        <td><strong>Productos Frecuentes</strong></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-outline-warning" onclick="mostrarProductosFrecuentes()">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><span class="badge badge-info">Ctrl + 4</span></td>
                                        <td><strong>Ver Métricas</strong></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-outline-info" onclick="mostrarMetricas()">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-cogs mr-2"></i>Atajos Generales
                                </h5>
                                <table class="table table-sm table-hover">
                                    <tr>
                                        <td width="100"><span class="badge badge-dark">F2</span></td>
                                        <td>Abrir escáner de código</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-dark">Ctrl + E</span></td>
                                        <td>Abrir escáner (alternativo)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-dark">Ctrl + S</span></td>
                                        <td>Procesar venta (Cobrar)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-dark">Ctrl + N</span></td>
                                        <td>Nueva venta</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-dark">Ctrl + L</span></td>
                                        <td>Limpiar carrito</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-lightbulb mr-2"></i>Consejos Prácticos
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Haz clic en cualquier producto para agregarlo al carrito</li>
                                            <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Usa el escáner (F2) para agregar productos rápidamente</li>
                                            <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Las métricas se actualizan automáticamente</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Presiona Enter en búsqueda para buscar productos</li>
                                            <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Tab en el escáner procesa el código automáticamente</li>
                                            <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>ESC cierra cualquier modal abierto</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-rocket mr-2"></i>Acción Rápida
                                        </h6>
                                        <button class="btn btn-primary mr-2" onclick="iniciarVentaRapida()">
                                            <i class="fas fa-bolt mr-1"></i>Venta Rápida
                                        </button>
                                        <button class="btn btn-success mr-2" onclick="activarBusquedaRapida()">
                                            <i class="fas fa-search mr-1"></i>Buscar
                                        </button>
                                        <button class="btn btn-warning mr-2" onclick="mostrarProductosFrecuentes()">
                                            <i class="fas fa-star mr-1"></i>Frecuentes
                                        </button>
                                        <button class="btn btn-info" onclick="mostrarMetricas()">
                                            <i class="fas fa-chart-bar mr-1"></i>Métricas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Cerrar (ESC)
                        </button>
                        <button type="button" class="btn btn-dark" onclick="cerrarYRecordarAtajos()">
                            <i class="fas fa-check mr-1"></i>Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    if ($('#modalAtajos').length) {
        $('#modalAtajos').remove();
    }
    
    // Agregar modal al DOM
    $('body').append(contenidoAtajos);
    
    // Mostrar modal
    $('#modalAtajos').modal('show');
    
    // Configurar evento para cerrar con ESC
    $('#modalAtajos').on('shown.bs.modal', function() {
        $(this).off('keydown').on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('#modalAtajos').modal('hide');
            }
        });
    });
    
    // Remover modal al cerrar
    $('#modalAtajos').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

// Función para cerrar y recordar atajos
window.cerrarYRecordarAtajos = function() {
    if ($('#modalAtajos').is(':visible')) {
        $('#modalAtajos').modal('hide');
    }
    toastr.info('Recuerda: F1 para ver atajos en cualquier momento', '💡 Atajos disponibles', {
        timeOut: 4000,
        positionClass: "toast-top-center"
    });
};


// =============================================
// 17. BOTÓN ATAJOS (F1) - VENTANA MODAL
// =============================================

// Función para mostrar el modal de atajos
function mostrarModalAtajos() {
    console.log('🔄 Mostrando modal de atajos');
    
    const contenidoAtajos = `
        <div class="modal fade" id="modalAtajos" tabindex="-1" aria-labelledby="modalAtajosLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-ligth">
                        <h5 class="modal-title" id="modalAtajosLabel">
                            <i class="fas fa-keyboard mr-2"></i>Atajos de Teclado - Sistema POS
                        </h5>
                        <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Presiona F1</strong> en cualquier momento para abrir esta ventana de atajos
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-shopping-cart mr-2"></i>Venta y Carrito</h5>
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-primary mr-2">F1</span>
                                                <strong>Mostrar atajos</strong>
                                            </div>
                                            <small class="text-muted">Abre esta ventana</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-primary mr-2">F2</span>
                                                <strong>Abrir escáner</strong>
                                            </div>
                                            <small class="text-muted">Escáner de códigos</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-primary mr-2">Ctrl + S</span>
                                                <strong>Procesar venta</strong>
                                            </div>
                                            <small class="text-muted">Cobrar</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-primary mr-2">Ctrl + N</span>
                                                <strong>Nueva venta</strong>
                                            </div>
                                            <small class="text-muted">Reiniciar</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-primary mr-2">Ctrl + L</span>
                                                <strong>Limpiar carrito</strong>
                                            </div>
                                            <small class="text-muted">Vaciar carrito</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="fas fa-tachometer-alt mr-2"></i>Dashboard Rápido</h5>
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-success mr-2">Ctrl + B</span>
                                                <strong>Buscar productos</strong>
                                            </div>
                                            <small class="text-muted">Activar búsqueda</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-success mr-2">Ctrl + E</span>
                                                <strong>Abrir escáner</strong>
                                            </div>
                                            <small class="text-muted">Alternativo a F2</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-success mr-2">Enter</span>
                                                <strong>Confirmar/Aceptar</strong>
                                            </div>
                                            <small class="text-muted">En formularios</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-success mr-2">ESC</span>
                                                <strong>Cerrar modal</strong>
                                            </div>
                                            <small class="text-muted">Cerrar ventana</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge badge-success mr-2">Tab</span>
                                                <strong>Procesar código</strong>
                                            </div>
                                            <small class="text-muted">En escáner</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-lightbulb mr-2"></i>Consejos Rápidos</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="mb-0">
                                                    <li><i class="fas fa-check text-success mr-2"></i>Haz clic en productos para agregar</li>
                                                    <li><i class="fas fa-check text-success mr-2"></i>Usa el escáner (F2) para agregar rápido</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="mb-0">
                                                    <li><i class="fas fa-check text-success mr-2"></i>Las métricas se actualizan automáticamente</li>
                                                    <li><i class="fas fa-check text-success mr-2"></i>Presiona Enter para buscar productos</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cerrarYRecordarAtajos()">
                            <i class="fas fa-check mr-1"></i>Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    if ($('#modalAtajos').length) {
        $('#modalAtajos').remove();
    }
    
    // Agregar modal al DOM
    $('body').append(contenidoAtajos);
    
    // Mostrar modal
    $('#modalAtajos').modal('show');
    
    // Configurar evento para cerrar con ESC
    $('#modalAtajos').on('shown.bs.modal', function() {
        $(this).off('keydown').on('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                $('#modalAtajos').modal('hide');
            }
        });
    });
    
    // Remover modal al cerrar
    $('#modalAtajos').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

// Función para cerrar y recordar atajos
window.cerrarYRecordarAtajos = function() {
    if ($('#modalAtajos').is(':visible')) {
        $('#modalAtajos').modal('hide');
    }
    toastr.info('Recuerda: F1 para ver atajos en cualquier momento', '💡 Atajos disponibles');
};

// Configurar atajos de teclado (incluyendo F1)
function configurarAtajosTeclado() {
    console.log('⌨️ Configurando atajos de teclado...');
    
    // Detectar tecla F1 para mostrar atajos
    $(document).on('keydown', function(e) {
        // F1 - Mostrar atajos
        if (e.key === 'F1' || e.keyCode === 112) {
            e.preventDefault();
            console.log('⌨️ Tecla F1 presionada - Mostrando atajos');
            mostrarModalAtajos();
            return false;
        }
        
        // Mantener tus otros atajos existentes...
        // Atajo Ctrl+S - Procesar venta
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+S - Procesar venta');
            $('#btnProcesarVenta').click();
            return false;
        }
        
        // Atajo Ctrl+B - Buscar
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+B - Buscar productos');
            $('#busquedaRapida').focus().select();
            return false;
        }
        
        // Atajo Ctrl+E - Abrir escáner
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+E - Abrir escáner');
            $('#modalScanner').modal('show');
            setTimeout(function() {
                $('#inputCodigoManual').focus();
            }, 300);
            return false;
        }
        
        // Atajo Ctrl+L - Limpiar carrito
        if (e.ctrlKey && e.key === 'l') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+L - Limpiar carrito');
            if (carrito.length > 0 && confirm('¿Limpiar carrito?')) {
                carrito = [];
                actualizarCarrito();
                actualizarMetricas();
                toastr.success('Carrito limpiado');
            }
            return false;
        }
        
        // Atajo Ctrl+N - Nueva venta
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            console.log('⌨️ Atajo Ctrl+N - Nueva venta');
            if (carrito.length > 0 && confirm('¿Iniciar nueva venta? Se perderá el carrito actual.')) {
                reiniciarVenta();
            }
            return false;
        }
    });
}

// Función para el botón "Atajos" (agrega esto si tienes un botón físico)
function configurarBotonAtajos() {
    // Si tienes un botón con id="btnAtajos" en tu HTML
    $(document).on('click', '#btnAtajos', function(e) {
        e.preventDefault();
        console.log('🖱️ Botón Atajos clickeado');
        mostrarModalAtajos();
    });
    
    // O si prefieres una función global para usar con onclick
    window.mostrarAtajos = function() {
        console.log('🔧 Mostrando atajos desde botón');
        mostrarModalAtajos();
    };
}

// Actualizar la función inicializarSistema
function inicializarSistema() {
    console.log('🚀 Inicializando sistema...');
    
    configurarSelect2Clientes();
    configurarNuevoCliente(); 
    cargarProductosDesdeDB();
    configurarMetodosPago();
    configurarBusquedaTiempoReal();
    configurarInputEfectivo();
    
    // AGREGAR ESTAS LÍNEAS:
    configurarAtajosTeclado();    // Configura F1 y otros atajos
    configurarBotonAtajos();      // Configura el botón físico (si existe)
    
    $('#selectIva').on('change', function() {
        const subtotal = parseFloat(window.ventaSubtotalNumerico) || 0;
        actualizarTotales(subtotal);
    });
    
    $('#numeroFactura').text(numeroFactura);
    
    console.log('✅ Sistema inicializado');
    toastr.success('Sistema de punto de venta listo');
    
    // Mostrar mensaje de atajos al iniciar (opcional)
    setTimeout(function() {
        toastr.info('Presiona F1 para ver atajos de teclado', '💡 Consejo rápido');
    }, 2000);
}

    
    // =============================================
    // 14. FUNCIONES GLOBALES
    // =============================================
    window.agregarProductoFrecuente = function(id) {
        const producto = productos[id];
        if (producto) {
            agregarAlCarrito(producto);
        } else {
            toastr.error('Producto no encontrado');
        }
    };
    
    window.recargarFrecuentes = function() {
        cargarProductosFrecuentes();
        toastr.info('Productos frecuentes actualizados');
    };
    
})(jQuery);
</script>

@stop