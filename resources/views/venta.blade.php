@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cash-register"></i> Punto de Venta</h1>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary btn-sm" id="btnAtajos">
                <i class="fas fa-keyboard"></i> Atajos (F1)
            </button>
            <span class="badge bg-info text-lg">Factura #: <span id="numeroFactura">F-00001</span></span>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- COLUMNA IZQUIERDA: Búsqueda y Productos -->
    <div class="col-lg-7">
        <!-- Dashboard Rápido -->
        <div class="row mb-3" id="dashboardRapido">
            <div class="col-md-3">
                <div class="small-box bg-primary" style="cursor: pointer;" onclick="iniciarVentaRapida()">
                    <div class="inner p-2 text-center">
                       
                            <i class="fas fa-bolt"></i>
                       
                        <h6 class="mb-0" style="font-size: 0.8rem;">Venta Rápida</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success" style="cursor: pointer;" onclick="activarBusquedaRapida()">
                    <div class="inner p-2 text-center">
                      
                            <i class="fas fa-search"></i>
                        
                        <h6 class="mb-0" style="font-size: 0.8rem;">Buscar</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning" style="cursor: pointer;" onclick="mostrarProductosFrecuentes()">
                    <div class="inner p-2 text-center">
                      
                            <i class="fas fa-star"></i>
                        
                        <h6 class="mb-0" style="font-size: 0.8rem;">Frecuentes</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-info" style="cursor: pointer;" onclick="mostrarMetricas()">
                    <div class="inner p-2 text-center">
                       
                            <i class="fas fa-chart-bar"></i>
                        
                        <h6 class="mb-0" style="font-size: 0.8rem;">Métricas</h6>
                    </div>
                </div>
            </div>
        </div>

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
                    <button class="btn btn-sm btn-outline-primary" id="btnScanner">
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
                                <th width="100">Código</th>
                                <th>Producto</th>
                                <th width="80">Precio</th>
                                <th width="80">Stock</th>
                                <th width="80">Acción</th>
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
        <!-- Métricas en Tiempo Real -->
        <div class="row mb-3" id="metricasTiempoReal">
            <div class="col-6">
                <div class="info-box bg-light">
                    <span class="info-box-icon bg-success"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Venta Actual</span>
                        <span class="info-box-number" id="metricVentaActual">$0</span>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="info-box bg-light">
                    <span class="info-box-icon bg-warning"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Productos</span>
                        <span class="info-box-number" id="metricTotalProductos">0</span>
                    </div>
                </div>
            </div>
        </div>

       <!-- Carrito de Compras - CORREGIDO: Misma altura que cliente -->
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
                <button type="button" class="close" data-dismiss="modal">&times;</button>
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

<!-- Modal Scanner -->
<div class="modal fade" id="modalScanner" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-camera"></i> Escanear Código</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div id="areaScanner" style="width: 100%; height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <div class="text-muted">
                        <i class="fas fa-camera fa-3x mb-2"></i>
                        <p>Área de escaneo</p>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="inputCodigoManual" placeholder="O ingresa código manualmente">
                </div>
                <button class="btn btn-primary btn-block" onclick="procesarCodigoEscaneado()">
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

                    <input type="hidden" id="cliente_nombre" name="cliente_nombre">
                    <input type="hidden" id="cliente_cedula" name="cliente_cedula">

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

<div class="modal fade" id="modalVistaPrevia" tabindex="-1" role="dialog" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="modalVistaPreviaLabel">
                    <i class="fas fa-print"></i> Vista Previa - Comprobante
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btnCerrarModalX">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="vistaPreviaComprobante"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCerrarModal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnImprimir">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button type="button" class="btn btn-success" id="btnNuevaVenta">
                    <i class="fas fa-plus"></i> Nueva Venta
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
        width: 60px !important;
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
 
/* Estilos para los resultados del Select2 */
.select2-result-cliente {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.select2-result-cliente:hover {
    background-color: #f8f9fa;
}

.select2-result-cliente__nombre {
    font-weight: 600;
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 4px;
}

.select2-result-cliente__info {
    font-size: 12px;
    color: #7f8c8d;
    font-style: italic;
}

/* Estilos para la opción resaltada */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #007bff !important;
    color: white;
}

.select2-container--default .select2-results__option--highlighted .select2-result-cliente__nombre,
.select2-container--default .select2-results__option--highlighted .select2-result-cliente__info {
    color: white;
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



</style>

@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/i18n/es.js"></script>
<script>


$(document).ready(function() {
    console.log('Punto de Venta - Sistema optimizado cargado');

$(document).ready(function() {
    // Destruir cualquier instancia previa
    if ($.fn.select2) {
        $('#selectCliente').select2('destroy');
    }
    
    // CONFIGURACIÓN DE SELECT2 CON BÚSQUEDA AJAX
    $('#selectCliente').select2({
        ajax: {
            url: 'buscar_cliente',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                var resultados = Array.isArray(data) ? data : [];
                return {
                    results: $.map(resultados, function (cliente) {
                        return {
                            id: cliente.id,
                            text: cliente.nombre + (cliente.cedula ? ' - ' + cliente.cedula : ''),
                            nombre: cliente.nombre,
                            cedula: cliente.cedula,
                            email: cliente.email,
                            telefono: cliente.telefono
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Escribe para buscar cliente...',
        minimumInputLength: 2,
        language: "es",
        allowClear: true,
        width: '100%'
    });

    // ENFOCAR AUTOMÁTICAMENTE AL ABRIR SELECT2
    $('#selectCliente').on('select2:open', function() {
        setTimeout(function() {
            $('#selectCliente').focus();
        }, 100);
    });

    // EVENTO CHANGE - GUARDAR EN INPUTS OCULTOS Y MOSTRAR INFO
    $('#selectCliente').on('change', function() {
        var selectedValue = $(this).val();
        var selectedText = $(this).find('option:selected').text();
        
        if (selectedValue && selectedValue !== '') {
            // Cerrar dropdown
            $('#selectCliente').select2('close');
            
            // Extraer nombre y cédula del texto
            var partes = selectedText.split(' - ');
            var nombreCliente = partes[0] || selectedText;
            var cedulaCliente = partes[1] || '';
            
            // GUARDAR EN INPUTS OCULTOS PARA TICKET/FACTURA
            $('#cliente_nombre').val(nombreCliente);
            $('#cliente_cedula').val(cedulaCliente);
            
            console.log('💾 Datos guardados en inputs ocultos:', {
                nombre: nombreCliente,
                cedula: cedulaCliente
            });
            
            // Mostrar información del cliente seleccionado
            if ($('#infoClienteSeleccionado').length === 0) {
                $('#selectCliente').closest('.form-group').after(
                    '<div id="infoClienteSeleccionado" class="mt-2"></div>'
                );
            }
            
            $('#infoClienteSeleccionado').html(`
                <div class="alert alert-light py-2 mb-0">
                    <i class="fas fa-user-check mr-2"></i>
                    <strong>Cliente:</strong> ${nombreCliente}
                    ${cedulaCliente ? `<span class="ml-2"><strong>Cédula:</strong> ${cedulaCliente}</span>` : ''}
                </div>
            `);
            
        } else {
            // Limpiar inputs ocultos e información
            $('#cliente_nombre').val('');
            $('#cliente_cedula').val('');
            $('#infoClienteSeleccionado').html('');
        }
    });

    // FORMATEAR CÉDULA CON PUNTOS EN INPUT
    $('#cedula').on('input', function() {
        var cedula = $(this).val();
        
        // Guardar longitud anterior para no interferir al borrar
        if (cedula.length < $(this).data('longitud-anterior')) {
            $(this).data('longitud-anterior', cedula.length);
            return;
        }
        
        // Quitar todos los puntos existentes
        var sinPuntos = cedula.replace(/\./g, '');
        
        // Aplicar formato: 1.234.232.355
        var formateado = '';
        var contador = 0;
        
        for (var i = sinPuntos.length - 1; i >= 0; i--) {
            formateado = sinPuntos.charAt(i) + formateado;
            contador++;
            
            if (contador === 3 && i > 0) {
                formateado = '.' + formateado;
                contador = 0;
            }
        }
        
        // Actualizar solo si cambió
        if (cedula !== formateado) {
            $(this).val(formateado);
            $(this).data('longitud-anterior', formateado.length);
        }
    });

    // Guardar nuevo cliente
    $('#form_guardar_cliente').on('submit', function(e) {
        e.preventDefault();
        guardarCliente();
    });
});


// Función mejorada para guardar cliente
function guardarCliente() {
    var btn = $('#BtnGuardar_cliente');
    
    // Prevenir doble ejecución
    if (btn.prop('disabled')) {
        return;
    }
    
    var originalHTML = btn.html();
    
    btn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Guardando...').prop('disabled', true);

    $.ajax({
        url: 'guardar_clientes',
        method: 'POST',
        data: $('#form_guardar_cliente').serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            btn.html(originalHTML).prop('disabled', false);
            
            if (response.success) {
               
              //  $('#modalNuevoCliente').modal('hide').removeClass('show');
                $('#modalNuevoCliente').modal('hide');
               
                $('#form_guardar_cliente')[0].reset();

                if (response.cliente) {
                    var nuevoCliente = response.cliente;
                    var clienteId = nuevoCliente.id_cliente || nuevoCliente.id;
                    var optionText = nuevoCliente.nombre + (nuevoCliente.cedula ? ' - ' + nuevoCliente.cedula : '');
                    
                    // Verificar si ya existe antes de agregar
                    if (!$('#selectCliente option[value="' + clienteId + '"]').length) {
                        var newOption = new Option(optionText, clienteId, false, false);
                        $('#selectCliente').append(newOption);
                    }
                    
                    $('#selectCliente').val(clienteId).trigger('change');
                    
                    // También guardar en inputs ocultos
                    $('#cliente_nombre').val(nuevoCliente.nombre);
                    $('#cliente_cedula').val(nuevoCliente.cedula || '');
                }

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    didClose: () => {
                        // Limpiar formulario después de cerrar el modal
                        $('#form_guardar_cliente')[0].reset();
                    }
                });
            } else {
                // Manejar errores de validación del servidor
                let mensajeError = response.message || 'Error al guardar el cliente';
                
                if (response.errors) {
                    // Mostrar el primer error de validación
                    const primerError = Object.values(response.errors)[0][0];
                    mensajeError = primerError;
                    
                    // Resaltar el campo de cédula si hay error
                    if (response.errors.cedula) {
                        $('#cedula').addClass('is-invalid');
                        $('#error-cedula').remove();
                        $('#cedula').after(
                            '<div class="invalid-feedback">' + response.errors.cedula[0] + '</div>'
                        );
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensajeError,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(xhr) {
            btn.html(originalHTML).prop('disabled', false);
            
            let mensaje = 'Error al guardar el cliente';
            
            if (xhr.status === 422) {
                // Error de validación del servidor
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    const primerError = Object.values(response.errors)[0][0];
                    mensaje = primerError;
                } else if (response && response.message) {
                    mensaje = response.message;
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Prevenir doble envío en el formulario
$('#form_guardar_cliente').on('submit', function(e) {
    e.preventDefault();
    guardarCliente();
});

   
    let carrito = [];
    let numeroFactura = generarNumeroFactura();
    let clienteSeleccionado = null;

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

    // Inicializar número de factura
    $('#numeroFactura').text(numeroFactura);

    // Configurar Select2 para clientes
    function configurarSelect2Clientes() {
        $('#selectCliente').select2({
            placeholder: "Buscar cliente por nombre, RFC o teléfono...",
            allowClear: true,
            width: '100%',
            language: "es",
            templateResult: formatClienteResult,
            templateSelection: formatClienteSelection
        });
    



        // Evento cuando se selecciona un cliente
        $('#selectCliente').on('select2:select', function(e) {
            const clienteId = $(this).val();
            if (clienteId && clientes[clienteId]) {
                clienteSeleccionado = clientes[clienteId];
                mostrarInfoCliente();
                toastr.success(`Cliente seleccionado: ${clienteSeleccionado.nombre}`, 'Cliente');
            } else {
                ocultarInfoCliente();
            }
        });

        // Evento cuando se limpia la selección
        $('#selectCliente').on('select2:clear', function() {
            ocultarInfoCliente();
        });
    }

    // Evento para enfocar automáticamente al abrir el Select2
    $('#selectCliente').on('select2:open', function() {
        // Esperar un poco para que el dropdown se abra completamente
       
           this.focus();
          
    });

    // Formatear resultado en el dropdown de Select2
    function formatClienteResult(cliente) {
        if (!cliente.id) return cliente.text;
        
        const clienteData = clientes[cliente.id];
        if (!clienteData) return cliente.text;

        return $(
            `<div>
                <strong>${clienteData.nombre}</strong>
                <div class="text-muted">
                    <small>RFC: ${clienteData.rfc} | Tel: ${clienteData.telefono}</small>
                </div>
            </div>`
        );
    }

    // Formatear selección en el Select2
    function formatClienteSelection(cliente) {
        if (!cliente.id) return cliente.text;
        
        const clienteData = clientes[cliente.id];
        if (!clienteData) return cliente.text;

        return clienteData.nombre;
    }







    // Configurar atajos de teclado
    function configurarAtajosTeclado() {
        $(document).on('keydown', function(e) {
            // F1 - Mostrar atajos
            if (e.key === 'F1') {
                e.preventDefault();
                $('#modalAtajos').modal('show');
            }
            // F2 - Buscar producto
            if (e.key === 'F2') {
                e.preventDefault();
                activarBusquedaRapida();
            }
            // F3 - Procesar venta
            if (e.key === 'F3') {
                e.preventDefault();
                $('#btnProcesarVenta').click();
            }
            // F9 - Limpiar carrito
            if (e.key === 'F9') {
                e.preventDefault();
                limpiarCarrito();
            }
            // Ctrl + N - Nueva venta
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                reiniciarVenta();
            }
            // Ctrl + B - Buscar
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                activarBusquedaRapida();
            }
            // Esc - Cancelar
            if (e.key === 'Escape') {
                e.preventDefault();
                $('#btnCancelar').click();
            }
        });
    }

    // =============================================
    // FUNCIONES DEL DASHBOARD RÁPIDO - CORREGIDAS
    // =============================================

    window.iniciarVentaRapida = function() {
        // Limpiar búsqueda y mostrar todos los productos
        $('#busquedaRapida').val('');
        $('#filtrosCategoria button[data-categoria="todas"]').click();
        $('#busquedaRapida').focus();
        
        // Mostrar mensaje de confirmación
        toastr.info('Modo venta rápida activado. Puedes buscar productos o usar los filtros.', 'Venta Rápida', {
            timeOut: 3000
        });
        
        console.log('Venta rápida activada');
    }

    window.activarBusquedaRapida = function() {
        // Enfocar en el campo de búsqueda
        $('#busquedaRapida').focus();
        
        // Mostrar estado de búsqueda activa
        $('#busquedaRapida').addClass('border-primary');
        setTimeout(() => {
            $('#busquedaRapida').removeClass('border-primary');
        }, 2000);
        
        toastr.info('Campo de búsqueda activado. Escribe para buscar productos.', 'Búsqueda', {
            timeOut: 2000
        });
        
        console.log('Búsqueda rápida activada');
    }

    window.mostrarProductosFrecuentes = function() {
        // Activar la sección de productos frecuentes
        cargarProductosFrecuentes();
        
        // Hacer scroll suave a la sección
        $('html, body').animate({
            scrollTop: $('#productosFrecuentes').offset().top - 100
        }, 500);
        
        toastr.info('Mostrando productos frecuentes', 'Frecuentes', {
            timeOut: 2000
        });
        
        console.log('Productos frecuentes mostrados');
    }

    window.mostrarMetricas = function() {
        const totalVentasHoy = 12580;
        const ventasCount = 15;
        const ticketPromedio = Math.round(totalVentasHoy / ventasCount);
        const productosVendidosHoy = 87;
        
        Swal.fire({
            title: '📊 Métricas del Día',
            html: `
                <div class="text-left">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Ventas Hoy:</strong></p>
                            <p><strong>Total Ventas:</strong></p>
                            <p><strong>Ticket Promedio:</strong></p>
                            <p><strong>Productos Vendidos:</strong></p>
                        </div>
                        <div class="col-6 text-right">
                            <p class="text-success">$${totalVentasHoy}</p>
                            <p>${ventasCount}</p>
                            <p class="text-info">$${ticketPromedio}</p>
                            <p class="text-warning">${productosVendidosHoy}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-2">
                        <p><strong>Venta Actual:</strong> $${carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0)}</p>
                        <p><strong>Productos en Carrito:</strong> ${carrito.reduce((sum, item) => sum + item.cantidad, 0)}</p>
                    </div>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Cerrar',
            width: 500
        });
        
        console.log('Métricas mostradas');
    }

    // Búsqueda en tiempo real
    function configurarBusquedaTiempoReal() {
        $('#busquedaRapida').on('input', function() {
            const termino = $(this).val().trim();
            if (termino.length >= 2) {
                buscarProductos(termino);
            } else if (termino.length === 0) {
                mostrarEstadoInicialTabla();
            }
        });

        $('#btnBuscarRapido').click(function() {
            const termino = $('#busquedaRapida').val().trim();
            if (termino.length >= 2) {
                buscarProductos(termino);
            } else {
                toastr.warning('Ingresa al menos 2 caracteres para buscar', 'Búsqueda');
            }
        });

        // Enter para buscar
        $('#busquedaRapida').on('keypress', function(e) {
            if (e.which === 13) {
                const termino = $(this).val().trim();
                if (termino.length >= 2) {
                    buscarProductos(termino);
                } else {
                    toastr.warning('Ingresa al menos 2 caracteres para buscar', 'Búsqueda');
                }
            }
        });
    }

    // Función de búsqueda de productos
    function buscarProductos(termino) {
        console.log('Buscando productos con término:', termino);
        
        const resultados = Object.values(productos).filter(producto => 
            producto.codigo.toLowerCase().includes(termino.toLowerCase()) ||
            producto.nombre.toLowerCase().includes(termino.toLowerCase()) ||
            producto.categoria.toLowerCase().includes(termino.toLowerCase())
        );

        console.log('Resultados encontrados:', resultados.length);
        mostrarResultadosBusqueda(resultados);
    }

    // Mostrar resultados de búsqueda
    function mostrarResultadosBusqueda(resultados) {
        const tbody = $('#resultadosProductos');
        tbody.empty();
        
        if (resultados.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">No se encontraron productos</td></tr>');
        } else {
            resultados.forEach(producto => {
                const claseStock = producto.stock <= producto.stock_minimo ? 'stock-bajo' : 'stock-normal';
                
                const fila = `
                    <tr class="producto-encontrado fade-in">
                        <td><small class="text-muted">${producto.codigo}</small></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${producto.imagen}" alt="${producto.nombre}" 
                                     class="mr-2 rounded" style="width: 30px; height: 30px;">
                                <div>
                                    <div class="font-weight-bold">${producto.nombre}</div>
                                    <small class="text-muted">${producto.categoria}</small>
                                </div>
                            </div>
                        </td>
                        <td class="font-weight-bold text-success">$${producto.precio}</td>
                        <td class="${claseStock}">
                            ${producto.stock} ${producto.unidad}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success btn-agregar-rapido" 
                                    data-id="${producto.id}" title="Agregar al carrito">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(fila);
            });

            // Eventos para botones de agregar
            $('.btn-agregar-rapido').click(function() {
                const productoId = $(this).data('id');
                agregarProductoDesdeBusqueda(productoId);
            });

            // Click en fila completa
            $('.producto-encontrado').click(function(e) {
                if (!$(e.target).closest('.btn-agregar-rapido').length) {
                    const productoId = $(this).find('.btn-agregar-rapido').data('id');
                    agregarProductoDesdeBusqueda(productoId);
                }
            });
        }
    }

    // Agregar producto desde búsqueda
    function agregarProductoDesdeBusqueda(productoId) {
        const producto = productos[productoId];
        if (producto && agregarProductoAlCarrito(producto)) {
          //  toastr.success(`${producto.nombre} agregado al carrito`, 'Producto Agregado');
        }
    }

    // Filtros por categoría - CORREGIDO: "Todas" muestra todos los productos
    function configurarFiltrosCategoria() {
        $('#filtrosCategoria button').click(function() {
            $('#filtrosCategoria button').removeClass('active');
            $(this).addClass('active');
            
            const categoria = $(this).data('categoria');
            if (categoria === 'todas') {
                // Mostrar todos los productos en la tabla
                const todosLosProductos = Object.values(productos);
                mostrarResultadosBusqueda(todosLosProductos);
                $('#busquedaRapida').val('');
              //  toastr.info('Mostrando todos los productos', 'Filtro');
            } else {
                const resultados = Object.values(productos).filter(p => p.categoria === categoria);
                mostrarResultadosBusqueda(resultados);
                $('#busquedaRapida').val('');
              //  toastr.info(`Mostrando productos de: ${categoria}`, 'Filtro');
            }
        });
    }

    // Scanner de código de barras
    function configurarScanner() {
        $('#btnScanner').click(function() {
            $('#modalScanner').modal('show');
            setTimeout(() => {
                $('#inputCodigoManual').focus();
            }, 500);
        });

        $('#inputCodigoManual').on('keypress', function(e) {
            if (e.which === 13) {
                procesarCodigoEscaneado();
            }
        });
    }

    window.procesarCodigoEscaneado = function() {
        const codigo = $('#inputCodigoManual').val().trim();
        if (codigo) {
            const producto = Object.values(productos).find(p => p.codigo === codigo);
            if (producto) {
                if (agregarProductoAlCarrito(producto)) {
                    $('#modalScanner').modal('hide');
                    $('#inputCodigoManual').val('');
                 //   toastr.success(`Producto escaneado: ${producto.nombre}`, 'Escaneo Exitoso');
                }
            } else {
                toastr.error('Código no encontrado en el sistema', 'Error de Escaneo');
            }
        }
    }

    // Cargar productos frecuentes
    window.cargarProductosFrecuentes = function() {
        const productosFrecuentes = Object.values(productos).filter(p => p.frecuente);
        const container = $('#productosFrecuentes');
        container.empty();
        
        if (productosFrecuentes.length === 0) {
            container.append('<div class="col-12 text-center text-muted">No hay productos frecuentes</div>');
        } else {
            productosFrecuentes.forEach(producto => {
                const card = `
                    <div class="col-md-6 mb-2">
                        <div class="producto-card" data-id="${producto.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${producto.nombre}</h6>
                                    <small class="text-muted">${producto.codigo}</small>
                                    <div class="precio text-success">$${producto.precio}</div>
                                </div>
                                <img src="${producto.imagen}" alt="${producto.nombre}" style="width: 40px; height: 40px;">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="${producto.stock <= producto.stock_minimo ? 'text-danger' : 'text-success'}">
                                    Stock: ${producto.stock}
                                </small>
                                <button class="btn btn-sm btn-primary btn-agregar-rapido-frecuente" data-id="${producto.id}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.append(card);
            });

            // Eventos para productos frecuentes
            $('.btn-agregar-rapido-frecuente').click(function(e) {
                e.stopPropagation();
                const productoId = $(this).data('id');
                const producto = productos[productoId];
                if (producto) {
                    agregarProductoAlCarrito(producto);
                }
            });

            $('.producto-card').click(function(e) {
                if (!$(e.target).closest('.btn-agregar-rapido-frecuente').length) {
                    const productoId = $(this).data('id');
                    const producto = productos[productoId];
                    if (producto) {
                        agregarProductoAlCarrito(producto);
                    }
                }
            });
        }
    }

    window.recargarFrecuentes = function() {
        cargarProductosFrecuentes();
        toastr.info('Productos frecuentes actualizados', 'Actualizado');
    }

    // Función para mostrar estado inicial de la tabla
    function mostrarEstadoInicialTabla() {
        const tbody = $('#resultadosProductos');
        tbody.empty();
        tbody.append('<tr><td colspan="5" class="text-center text-muted"><i class="fas fa-search"></i> Escribe para buscar productos</td></tr>');
    }

    // Función para verificar stock - CORREGIDA: Mensaje cuando stock es cero
    function verificarStock(producto, cantidadRequerida = 1) {
        const productoEnCarrito = carrito.find(item => item.codigo === producto.codigo);
        const cantidadEnCarrito = productoEnCarrito ? productoEnCarrito.cantidad : 0;
        const stockDisponible = producto.stock - cantidadEnCarrito;
        
        if (stockDisponible <= 0) {
            return { 
                disponible: false, 
                mensaje: `❌ El stock del producto "${producto.nombre}" está en cero. No se puede agregar.` 
            };
        }
        
        if (cantidadRequerida > stockDisponible) {
            return { 
                disponible: false, 
                mensaje: `Stock insuficiente. Disponible: ${stockDisponible} unidades` 
            };
        }
        
        if (producto.stock <= producto.stock_minimo) {
            return { 
                disponible: true, 
                mensaje: `Stock bajo: ${producto.stock} unidades`,
                advertencia: true
            };
        }
        
        return { 
            disponible: true
        };
    }

    // Función para agregar producto al carrito
    function agregarProductoAlCarrito(producto, cantidad = 1) {
        const verificacionStock = verificarStock(producto, cantidad);
        
        if (!verificacionStock.disponible) {
            toastr.error(verificacionStock.mensaje, 'Stock Insuficiente');
            return false;
        }
        
        const existente = carrito.find(item => item.codigo === producto.codigo);
        
        if (existente) {
            existente.cantidad += cantidad;
        } else {
            carrito.push({
                ...producto,
                cantidad: cantidad
            });
        }
        
        actualizarCarrito();
        
        if (verificacionStock.advertencia) {
            toastr.warning(verificacionStock.mensaje, 'Stock Bajo');
        }
        
        return true;
    }

    // Actualizar carrito de compras - CORREGIDA: Eliminación de última fila funciona correctamente
function actualizarCarrito() {
    const tbody = $('#itemsCarrito');
    
    if (carrito.length === 0) {
        tbody.html('<tr><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>Carrito vacío</td></tr>');
    } else {
        let nuevoHTML = '';
        
        carrito.forEach((item, index) => {
            const total = Math.round(item.precio * item.cantidad); // NÚMEROS ENTEROS
            
            nuevoHTML += `
                <tr data-index="${index}">
                    <td>
                        <small class="text-muted d-block">${item.codigo}</small>
                        <strong>${item.nombre}</strong>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-outline-secondary btn-cantidad" 
                                    onclick="modificarCantidad(${index}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control form-control-sm input-cantidad" 
                                   value="${item.cantidad}" min="1" max="${item.stock}"
                                   onchange="actualizarCantidadManual(${index}, this.value)">
                            <button class="btn btn-sm btn-outline-secondary btn-cantidad" 
                                    onclick="modificarCantidad(${index}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="font-weight-bold text-success">$${total}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        // Actualizar todo de una vez sin parpadeo
        tbody.html(nuevoHTML);
    }
    
    calcularTotales();
    actualizarMetricasTiempoReal();
}


// Métricas en tiempo real - CORREGIDO: Sin decimales, sin signo $ y con puntos de mil
function actualizarMetricasTiempoReal() {
    const totalVenta = Math.round(carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0));
    const totalProductos = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    
    $('#metricVentaActual').text(formatearNumero(totalVenta));
    $('#metricTotalProductos').text(formatearNumero(totalProductos));
}

    // Funciones globales para el carrito - CORREGIDAS: Mensaje de stock cero
    window.modificarCantidad = function(index, cambio) {
        if (carrito[index]) {
            const nuevaCantidad = carrito[index].cantidad + cambio;
            if (nuevaCantidad >= 1 && nuevaCantidad <= carrito[index].stock) {
                carrito[index].cantidad = nuevaCantidad;
                actualizarCarrito();
            } else if (nuevaCantidad < 1) {
                // No permitir cantidades menores a 1
                carrito[index].cantidad = 1;
                actualizarCarrito();
            } else if (nuevaCantidad > carrito[index].stock) {
                // Mostrar mensaje cuando se intenta exceder el stock disponible
                toastr.error(`❌ El stock del producto "${carrito[index].nombre}" está en ${carrito[index].stock} unidades. No hay más disponible.`, 'Stock Agotado');
            }
        }
    }

    window.actualizarCantidadManual = function(index, nuevaCantidad) {
        nuevaCantidad = parseInt(nuevaCantidad);
        if (carrito[index] && nuevaCantidad >= 1 && nuevaCantidad <= carrito[index].stock) {
            carrito[index].cantidad = nuevaCantidad;
            actualizarCarrito();
        } else if (nuevaCantidad > carrito[index].stock) {
            // Mostrar mensaje cuando se intenta exceder el stock disponible
            toastr.error(`❌ El stock del producto "${carrito[index].nombre}" está en ${carrito[index].stock} unidades. No hay más disponible.`, 'Stock Agotado');
            // Restaurar la cantidad anterior
            carrito[index].cantidad = carrito[index].cantidad;
            actualizarCarrito();
        }
    }

 // Función para eliminar producto - CORREGIDA: Elimina correctamente la última fila
window.eliminarProducto = function(index) {
    if (carrito[index]) {
        const productoEliminado = carrito[index].nombre;
        
        // Eliminar del array
        carrito.splice(index, 1);
        
        // Actualizar la vista del carrito
        actualizarCarrito();
        
      //  toastr.warning(`Producto eliminado: ${productoEliminado}`, 'Carrito');
    }
}

   // Calcular totales - CORREGIDO: Sin decimales, sin signo $ y con puntos de mil
    function calcularTotales() {
        const subtotal = Math.round(carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0));
        const porcentajeIva = parseFloat($('#selectIva').val());
        const iva = Math.round(subtotal * (porcentajeIva / 100));
        const total = subtotal + iva;
        
        $('#subtotalVenta').text(formatearNumero(subtotal));
        $('#ivaVenta').text(formatearNumero(iva));
        $('#totalVenta').text(formatearNumero(total));
        $('#porcentajeIva').text(porcentajeIva);
        
        // Calcular cambio según método de pago
        const metodoPago = $('#metodoPago').val();
        
        if (metodoPago === 'efectivo') {
            const efectivo = parseFloat($('#efectivoRecibido').val()) || 0;
            const cambio = Math.round(efectivo - total);
            $('#cambioVenta').text(formatearNumero(cambio >= 0 ? cambio : 0));
        } else if (metodoPago === 'mixto') {
            calcularPagoMixto();
        }
    }

    // Función para formatear números con puntos de mil
    function formatearNumero(numero) {
        return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // =============================================
    // FUNCIONALIDAD DE IMPRESIÓN - CÓDIGO ORIGINAL
    // =============================================

    // Imprimir directo (sin procesar venta)
    $('#btnImprimirDirecto').click(function() {
        if (carrito.length === 0) {
            toastr.error('No hay productos en el carrito para imprimir', 'Impresión');
            return;
        }
        mostrarVistaPrevia();
        toastr.info('Generando vista previa para impresión', 'Impresión');
    });

    // Procesar venta
    $('#btnProcesarVenta').click(function() {
        if (carrito.length === 0) {
            toastr.error('Agregue productos al carrito', 'Venta');
            return;
        }
        
        // Verificar stock final antes de procesar
        let stockValido = true;
        carrito.forEach(item => {
            const productoOriginal = productos[Object.keys(productos).find(key => productos[key].codigo === item.codigo)];
            if (productoOriginal && item.cantidad > productoOriginal.stock) {
                stockValido = false;
                toastr.error(`Stock insuficiente: ${item.nombre} (Solicitado: ${item.cantidad}, Disponible: ${productoOriginal.stock})`, 'Stock');
            }
        });
        
        if (!stockValido) {
            return;
        }
        
        const metodoPago = $('#metodoPago').val();
        const total = parseFloat($('#totalVenta').text().replace('$', ''));
        
        // Validaciones según método de pago
        let validacion = true;
        let mensajeError = '';
        
        switch(metodoPago) {
            case 'efectivo':
                const efectivo = parseFloat($('#efectivoRecibido').val()) || 0;
                if (efectivo < total) {
                    validacion = false;
                    mensajeError = 'El efectivo recibido es menor al total';
                }
                break;
                
            case 'tarjeta':
                if (!$('#numeroTarjeta').val() || !$('#fechaVencimiento').val() || !$('#cvvTarjeta').val()) {
                    validacion = false;
                    mensajeError = 'Complete todos los datos de la tarjeta';
                }
                break;
                
            case 'mixto':
                const totalMixto = parseFloat($('#totalMixto').text().replace('$', '')) || 0;
                if (totalMixto < total) {
                    validacion = false;
                    mensajeError = 'El total del pago mixto es menor al total de la venta';
                }
                break;
                
            case 'transferencia':
            case 'cheque':
                if (!$('#referenciaTransaccion').val()) {
                    validacion = false;
                    mensajeError = 'Ingrese la referencia/autorización';
                }
                break;
        }
        
        if (!validacion) {
            toastr.error(mensajeError, 'Validación de Pago');
            return;
        }
        
        // Procesar venta y mostrar vista previa
        procesarVenta();
    });

    // Procesar venta
    function procesarVenta() {
        // Actualizar stock
        actualizarStockVenta();
        
        // Mostrar mensaje de éxito
        toastr.success(`Venta procesada exitosamente - ${numeroFactura}`, '¡Éxito!');
        
        // Mostrar vista previa
        mostrarVistaPrevia();
    }

    // Actualizar stock después de la venta
    function actualizarStockVenta() {
        carrito.forEach(item => {
            if (productos[Object.keys(productos).find(key => productos[key].codigo === item.codigo)]) {
                const productoKey = Object.keys(productos).find(key => productos[key].codigo === item.codigo);
                productos[productoKey].stock -= item.cantidad;
                
                // Mostrar advertencia si el stock queda bajo después de la venta
                if (productos[productoKey].stock <= productos[productoKey].stock_minimo) {
                    toastr.warning(`Stock bajo después de venta: ${productos[productoKey].nombre} - ${productos[productoKey].stock} unidades`, 'Control de Stock');
                }
            }
        });
    }

    // Mostrar vista previa del comprobante
function mostrarVistaPrevia() {
    const tipoComprobante = $('#tipoComprobante').val();
    
    // Calcular valores actuales
    const subtotal = Math.round(carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0));
    const porcentajeIva = parseFloat($('#selectIva').val());
    const iva = Math.round(subtotal * (porcentajeIva / 100));
    const total = subtotal + iva;
    
    const ventaData = {
        numeroFactura: numeroFactura,
        cliente: clienteSeleccionado ? clienteSeleccionado.nombre : 'Consumidor Final',
        rfc: clienteSeleccionado ? clienteSeleccionado.rfc : 'XAXX010101000',
        telefono: clienteSeleccionado ? clienteSeleccionado.telefono : 'N/A',
        items: carrito,
        subtotal: subtotal, // Pasar el valor calculado directamente
        iva: iva, // Pasar el valor calculado directamente
        total: total, // Pasar el valor calculado directamente
        tipo: tipoComprobante,
        fecha: new Date().toLocaleString(),
        metodoPago: $('#metodoPago').val()
    };
    
    $('#vistaPreviaComprobante').html(generarComprobanteHTML(ventaData));
    $('#modalVistaPrevia').modal('show');
}

// Generar HTML del comprobante - CORREGIDO: Orden de columnas en ticket
function generarComprobanteHTML(ventaData) {
    const esFactura = ventaData.tipo !== 'ticket';
    const esTicket = ventaData.tipo === 'ticket';
    
    // Convertir a números enteros
    const subtotal = Math.round(ventaData.subtotal);
    const iva = Math.round(ventaData.iva);
    const total = Math.round(ventaData.total);
    
    // Calcular total de productos vendidos
    const totalProductos = ventaData.items.reduce((sum, item) => sum + item.cantidad, 0);
    
    // Nombre del vendedor (usuario registrado)
    const nombreVendedor = "Carlos Rodríguez"; // Puedes cambiar esto por el nombre del usuario logueado
    
    // Función para formatear números con puntos de mil
    const formatNumber = (num) => {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };
    
    if (esTicket) {
        return `
        <div class="comprobante-ticket" style="width: 80mm; font-family: 'Courier New', monospace; font-size: 12px;">
            <div class="text-center">
                <h4 style="margin: 5px 0; font-weight: bold;">FERRETERÍA</h4>
                <h5 style="margin: 3px 0; font-weight: bold;">"EL MARTILLO"</h5>
                <p style="margin: 2px 0;">RFC: FME850301XYZ</p>
                <p style="margin: 2px 0;">Tel: (555) 123-4567</p>
                <p style="margin: 2px 0;">Av. Principal #123</p>
            </div>
            
            <hr style="border-top: 1px dashed #000; margin: 8px 0;">
            
            <div style="margin: 5px 0;">
                <strong>TICKET:</strong> ${ventaData.numeroFactura}<br>
                <strong>FECHA:</strong> ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}<br>
                <strong>CLIENTE:</strong> ${ventaData.cliente}<br>
                <strong>VENDEDOR:</strong> ${nombreVendedor}<br>
                <strong>TOTAL PRODUCTOS:</strong> ${totalProductos}
            </div>
            
            <hr style="border-top: 1px dashed #000; margin: 8px 0;">
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; border-bottom: 1px dashed #000; padding: 3px 0;">DESC</th>
                        <th style="text-align: center; border-bottom: 1px dashed #000; padding: 3px 0;">CANT</th>
                        <th style="text-align: right; border-bottom: 1px dashed #000; padding: 3px 0;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    ${ventaData.items.map(item => {
                        const itemTotal = Math.round(item.precio * item.cantidad);
                        return `
                        <tr>
                            <td style="padding: 2px 0;">
                                ${item.nombre.substring(0, 20)}
                            </td>
                            <td style="text-align: center; padding: 2px 0;">
                                ${item.cantidad}
                            </td>
                            <td style="text-align: right; padding: 2px 0;">
                                ${formatNumber(itemTotal)}
                            </td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
            
            <hr style="border-top: 1px dashed #000; margin: 8px 0;">
            
            <!-- CORREGIDO: Totales, subtotales e IVA con puntos de mil -->
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: right;">SUBTOTAL: ${formatNumber(subtotal)}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">IVA: ${formatNumber(iva)}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td style="text-align: right;">TOTAL: ${formatNumber(total)}</td>
                </tr>
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
                    <td colspan="3">${ventaData.cliente}</td>
                </tr>
                <tr>
                    <td><strong>RFC:</strong></td>
                    <td>${ventaData.rfc}</td>
                    <td><strong>Teléfono:</strong></td>
                    <td>${ventaData.telefono}</td>
                </tr>
                <tr>
                    <td><strong>Vendedor:</strong></td>
                    <td colspan="3">${nombreVendedor}</td>
                </tr>
                <tr>
                    <td><strong>Total Productos:</strong></td>
                    <td colspan="3">${totalProductos}</td>
                </tr>
            </table>
            
            <table class="table table-bordered table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center; width: 15%;">Cant.</th>
                        <th style="text-align: center; width: 45%;">Descripción</th>
                        <th style="text-align: center; width: 20%;">P.Unit</th>
                        <th style="text-align: center; width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${ventaData.items.map(item => {
                        const precioUnitario = Math.round(item.precio);
                        const itemTotal = Math.round(item.precio * item.cantidad);
                        return `
                        <tr>
                            <td style="text-align: center;">${item.cantidad}</td>
                            <td>${item.nombre}</td>
                            <td style="text-align: right;">${formatNumber(precioUnitario)}</td>
                            <td style="text-align: right;">${formatNumber(itemTotal)}</td>
                        </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
            
            <!-- CORREGIDO: Totales, subtotales e IVA con puntos de mil -->
            <div class="d-flex justify-content-end">
                <table class="table table-bordered table-sm" style="width: 300px;">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-right" style="text-align: right !important;">${formatNumber(subtotal)}</td>
                    </tr>
                    <tr>
                        <td><strong>IVA:</strong></td>
                        <td class="text-right" style="text-align: right !important;">${formatNumber(iva)}</td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>TOTAL:</strong></td>
                        <td class="text-right" style="text-align: right !important;"><strong>${formatNumber(total)}</strong></td>
                    </tr>
                </table>
            </div>
            
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

    function calcularPagoMixto() {
            const montoEfectivo = parseFloat($('#montoEfectivoMixto').val()) || 0;
            const montoTarjeta = parseFloat($('#montoTarjetaMixto').val()) || 0;
            const totalMixto = Math.round(montoEfectivo + montoTarjeta);
            $('#totalMixto').text(formatearNumero(totalMixto));
        }
    
    // Imprimir comprobante desde modal
    $('#btnImprimir').click(function() {
        const tipoComprobante = $('#tipoComprobante').val();
        const esTicket = tipoComprobante === 'ticket';
        
        const ventana = window.open('', '_blank');
        const estilo = esTicket ? 
            `<style>
                @media print {
                    body { margin: 0; padding: 0; }
                    .comprobante-ticket { width: 80mm; font-family: 'Courier New', monospace; font-size: 12px; }
                }
            </style>` : 
            `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">`;
        
        ventana.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Comprobante - ${numeroFactura}</title>
                ${estilo}
            </head>
            <body>
                ${$('#vistaPreviaComprobante').html()}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(() => {
                            window.close();
                        }, 1000);
                    }
                <\/script>
            </body>
            </html>
        `);
        ventana.document.close();
        
        toastr.success('Comprobante enviado a impresión', 'Impresión');
    });

    // Nueva venta
    $('#btnNuevaVenta').click(function() {
        $('#modalVistaPrevia').modal('hide');
        reiniciarVenta();
        toastr.success('Nueva venta iniciada', 'Sistema');
    });

    // CORREGIDO: Configurar cierre del modal desde X y botón Cerrar
    $('#btnCerrarModal, #btnCerrarModalX').click(function() {
        $('#modalVistaPrevia').modal('hide');
    });

    // Cambiar método de pago
    $('#metodoPago').change(function() {
        $('.metodo-pago-detalle').addClass('d-none');
        const metodo = $(this).val();
        
        // Mostrar sección correspondiente
        $('#pago' + metodo.charAt(0).toUpperCase() + metodo.slice(1)).removeClass('d-none');
        
        // Mostrar referencia para transferencia y cheque
        if (metodo === 'transferencia' || metodo === 'cheque') {
            $('#referenciaPago').removeClass('d-none');
        }
        
        if (metodo === 'mixto') {
            calcularPagoMixto();
        }
        
        toastr.info(`Método de pago cambiado a: ${$(this).find('option:selected').text()}`, 'Pago');
    });

    // Eventos para cálculos
    $('#selectIva').change(calcularTotales);
    $('#efectivoRecibido').on('input', calcularTotales);
    $('#montoEfectivoMixto, #montoTarjetaMixto').on('input', calcularPagoMixto);

    // Limpiar carrito
    function limpiarCarrito() {
        if (carrito.length > 0) {
            Swal.fire({
                title: '¿Limpiar carrito?',
                text: "Se eliminarán todos los productos del carrito",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    carrito = [];
                    actualizarCarrito();
                    toastr.info('Carrito limpiado correctamente', 'Carrito');
                }
            });
        }
    }

    $('#btnLimpiarCarrito').click(limpiarCarrito);

    // Cancelar venta
    $('#btnCancelar').click(function() {
        if (carrito.length > 0) {
            Swal.fire({
                title: '¿Cancelar venta?',
                text: "Se perderán todos los productos del carrito",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'Continuar'
            }).then((result) => {
                if (result.isConfirmed) {
                    reiniciarVenta();
                    toastr.info('Venta cancelada', 'Sistema');
                }
            });
        }
    });

  
    // Mostrar información del cliente
    function mostrarInfoCliente() {
        $('#infoCliente').removeClass('d-none');
        $('#nombreClienteSeleccionado').text(clienteSeleccionado.nombre);
        $('#rfcClienteSeleccionado').text(clienteSeleccionado.cedula);
        $('#telefonoClienteSeleccionado').text(clienteSeleccionado.telefono);
        $('#btnQuitarCliente').show();
    }

    function ocultarInfoCliente() {
        $('#infoCliente').addClass('d-none');
        clienteSeleccionado = null;
        $('#btnQuitarCliente').hide();
    }

    $('#btnQuitarCliente, #btnQuitarClienteLinea').click(function() {
        $('#selectCliente').val('').trigger('change');
        ocultarInfoCliente();
        toastr.info('Cliente removido', 'Cliente');
    });

    // Función para generar número de factura
    function generarNumeroFactura() {
        let contador = localStorage.getItem('contadorFacturas');
        
        if (!contador) {
            contador = 1;
        } else {
            contador = parseInt(contador) + 1;
        }
        
        localStorage.setItem('contadorFacturas', contador);
        return `F-${contador.toString().padStart(5, '0')}`;
    }

    // Reiniciar venta
    function reiniciarVenta() {
        carrito = [];
        clienteSeleccionado = null;
        numeroFactura = generarNumeroFactura();
        
        $('#numeroFactura').text(numeroFactura);
        $('#selectCliente').val('').trigger('change');
        $('#infoCliente').addClass('d-none');
        $('#busquedaRapida').val('');
        $('#efectivoRecibido').val('0');
        $('#selectIva').val('16');
        $('#metodoPago').val('efectivo');
        $('#tipoComprobante').val('ticket');
        
        // Limpiar campos de pago
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
        mostrarEstadoInicialTabla();
    }

    // Inicializar el sistema
    function inicializarSistema() {
        configurarSelect2Clientes();
        configurarAtajosTeclado();
        configurarBusquedaTiempoReal();
        configurarFiltrosCategoria();
        configurarScanner();
        cargarProductosFrecuentes();
        mostrarEstadoInicialTabla();
        
        // Mostrar modal de atajos al cargar
        setTimeout(() => {
         //   toastr.info('Sistema POS optimizado cargado. Presiona F1 para ver atajos.', 'Bienvenido');
        }, 1000);
    }

    // Inicializar
    inicializarSistema();
});
</script>

  <!-- ==============================

// VERIFICAR SI EXISTE CLIENTE

===================================  -->

  <script>
    $(document).ready(function() {

         $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $('#cedula').blur(function() {
        var error_cedula = '';
        var cedula = $('#cedula').val();
        var _token = $('input[name="_token"]').val();
        var filter = /([0-9])/;
        if (!filter.test(cedula)) {
          $('#error_cedula').html('<label class="text-danger">Debe escribir número de cédula.</label>');
          $('#cedula').addClass('has-error');
          $('#agregar_cliente').attr('disabled', 'disabled');
        } else {
          $.ajax({
            url: 'verificar_cliente',
            method: "POST",
            data: {
              cedula: cedula
            },
            success: function(result) {
              if (result == 'unique') {
                $('#error_cedula').html('<label class="text-danger">El cliente ya existe.</label>');
                $('#cedula').addClass('has-error');
                $('#agregar_cliente').attr('disabled', 'disabled');
              } else {
               

              }
            }
          })
        }
      });
    });
  </script>


@stop