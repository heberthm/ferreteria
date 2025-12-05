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

                    <input type="text" id="cliente_nombre" name="cliente_nombre">
                    <input type="text" id="cliente_cedula" name="cliente_cedula">
                    <input type="text" id="cliente_email" name="cliente_email">
                    <input type="text" id="cliente_direccion" name="cliente_direccion">
                    <input type="text" id="cliente_telefono" name="cliente_telefono">

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


<script>
$(document).ready(function() {
    console.log('🚀 Punto de Venta - Sistema cargado');

    // Variables globales
    let productos = {};
    let carrito = [];
    let numeroFactura = generarNumeroFactura();
    let clienteSeleccionado = null;
    let timeoutBusqueda = null;

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

    // =============================================
    // 1. FUNCIONES DE INICIALIZACIÓN
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
                response.productos.forEach(producto => {
                    // IMPORTANTE: Usa 'id_producto' como 'id'
                    const id = producto.id_producto || producto.id;
                    
                    productos[id] = {
                        id: id,  // Usa el id correcto
                        codigo: producto.codigo || '',
                        nombre: producto.nombre || 'Sin nombre',
                        precio: parseFloat(producto.precio) || 0,
                        stock: parseInt(producto.stock) || 0,
                        categoria: producto.categoria || 'Sin categoría',
                        unidad: producto.unidad || 'unidad',
                        stock_minimo: producto.stock_minimo || 5
                    };
                });
                
                console.log(`✅ ${Object.keys(productos).length} productos cargados`);
                
                // INICIALIZAR Y MOSTRAR
                inicializarCategorias();
                cargarProductosFrecuentes();
                
                // MOSTRAR TODOS LOS PRODUCTOS INMEDIATAMENTE
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
                return { 
                    q: params.term || ''
                };
            },
            processResults: function(data) {
                console.log('📋 Clientes encontrados:', data);
                
                // Formatear resultados para Select2 - SOLO NOMBRE Y CÉDULA
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
        
        // REMOVER EFECTOS VISUALES DE COLORES
        templateResult: function(cliente) {
            if (cliente.loading) {
                return 'Buscando...';
            }
            
            // SIN COLORES - solo texto simple
            return $(
                `<div>
                    <div>${cliente.text}</div>
                </div>`
            );
        },
        
        templateSelection: function(cliente) {
            if (!cliente.id) {
                return 'Seleccionar cliente...';
            }
            // Solo mostrar nombre y cédula
            return cliente.text;
        },
        
        // REMOVER ESTILOS DE HIGHLIGHT
        escapeMarkup: function(markup) {
            return markup;
        }
    });
    
    // Evento change para cargar información del cliente
    selectElement.on('change', function() {
        const selectedData = $(this).select2('data')[0];
        
        console.log('Cliente seleccionado:', selectedData);
        
        if (selectedData && selectedData.id) {
            // GUARDAR DATOS DEL CLIENTE EN CAMPOS OCULTOS
            $('#cliente_nombre').val(selectedData.nombre || '');
            $('#cliente_cedula').val(selectedData.cedula || '');
            $('#cliente_email').val(selectedData.email || '');
            $('#cliente_direccion').val(selectedData.direccion || '');
            $('#cliente_telefono').val(selectedData.telefono || '');
            
            // MOSTRAR SOLO NOMBRE Y CÉDULA DEL CLIENTE
            mostrarInfoClienteBasica(selectedData);
            
            // Guardar en variable global
            clienteSeleccionado = {
                id: selectedData.id,
                nombre: selectedData.nombre,
                cedula: selectedData.cedula,
                email: selectedData.email,
                direccion: selectedData.direccion,
                telefono: selectedData.telefono
            };
            
            // Mostrar botón para quitar cliente
            $('#btnQuitarCliente').show();
            toastr.success(`Cliente ${selectedData.nombre} seleccionado`);
        } else {
            // Limpiar si no hay cliente seleccionado
            limpiarClienteSeleccionado();
        }
    });
}

// MODIFICAR SOLO ESTA FUNCIÓN:
function mostrarInfoClienteBasica(clienteData) {
    // Remover info anterior si existe
    if ($('#infoClienteSeleccionado').length) {
        $('#infoClienteSeleccionado').remove();
    }
    
    // Crear nueva información - SOLO NOMBRE Y CÉDULA
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
    
    // Insertar después del select de cliente
    $('#selectCliente').closest('.form-group').after(infoHtml);
    
    // Configurar evento click en el botón quitar cliente - USANDO event delegation
    $(document).on('click', '#btnQuitarClienteInfo', function(e) {
        e.stopPropagation(); // Importante: evita propagación
        limpiarClienteSeleccionado();
        toastr.info('Cliente removido');
        return false;
    });
    
    // Mostrar botón para quitar cliente en el header
    $('#btnQuitarCliente').show();
}
    // =============================================
    // 2. FUNCIONES DE CLIENTE - MOSTRAR INFORMACIÓN
    // =============================================
    
    function mostrarInfoClienteSeleccionado(clienteData) {
        // Remover info anterior si existe
        if ($('#infoClienteSeleccionado').length) {
            $('#infoClienteSeleccionado').remove();
        }
        
        // Crear nueva información
        const infoHtml = `
            <div id="infoClienteSeleccionado" class="mt-3 p-3 bg-light rounded border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 font-weight-bold text-success">
                        <i class="fas fa-user-check mr-2"></i>
                        Cliente Seleccionado
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarClienteSeleccionadoJS()">
                        <i class="fas fa-times"></i> Quitar
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Nombre:</strong> ${clienteData.nombre}</p>
                        <p class="mb-1"><strong>Cédula:</strong> ${clienteData.cedula || 'No registrada'}</p>
                        <p class="mb-1"><strong>Email:</strong> ${clienteData.email || 'No registrado'}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Teléfono:</strong> ${clienteData.telefono || 'No registrado'}</p>
                        <p class="mb-1"><strong>Dirección:</strong> ${clienteData.direccion || 'No registrada'}</p>
                    </div>
                </div>
            </div>
        `;
        
        // Insertar después del select de cliente
        $('#selectCliente').closest('.form-group').after(infoHtml);
        
        // Mostrar botón para quitar cliente en el header
        $('#btnQuitarCliente').show();
    }

   function limpiarClienteSeleccionado() {
    // Limpiar el select2
    $('#selectCliente').val('').trigger('change');
    
    // Limpiar campos ocultos
    $('#cliente_nombre').val('');
    $('#cliente_cedula').val('');
    $('#cliente_email').val('');
    $('#cliente_direccion').val('');
    $('#cliente_telefono').val('');
    
    // Remover información visual
    $('#infoClienteSeleccionado').remove();
    
    // Ocultar botón de quitar cliente en el header
    $('#btnQuitarCliente').hide();
    
    // Limpiar variable global
    clienteSeleccionado = null;
}
    // =============================================
    // 3. MOSTRAR TODOS LOS PRODUCTOS Y CATEGORÍAS
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
        // Obtener categorías únicas de los productos
        const categorias = new Set();
        Object.values(productos).forEach(p => {
            if (p.categoria && p.categoria.trim() !== '') {
                categorias.add(p.categoria);
            }
        });
        
        console.log('Categorías encontradas:', Array.from(categorias));
        
        // Actualizar botones de filtro
        const botonesContainer = $('#filtrosCategoria .btn-group');
        botonesContainer.empty();
        
        // Agregar botón "Todas"
        botonesContainer.append(`
            <button type="button" class="btn btn-outline-primary active" data-categoria="todas">
                Todas
            </button>
        `);
        
        // Agregar botones para cada categoría
        Array.from(categorias).sort().forEach(categoria => {
            botonesContainer.append(`
                <button type="button" class="btn btn-outline-secondary" data-categoria="${categoria}">
                    ${categoria}
                </button>
            `);
        });
        
        // Asegurar que los filtros estén visibles
        $('#filtrosCategoria').show();
        
        // Agregar event listeners
        botonesContainer.find('button').on('click', function() {
            const categoria = $(this).data('categoria');
            
            // Actualizar estado activo
            botonesContainer.find('button').removeClass('active btn-primary')
                .addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary')
                .addClass('active btn-primary');
            
            // Filtrar productos
            filtrarProductosPorCategoria(categoria);
        });
    }

    function configurarBusquedaTiempoReal() {
        $('#busquedaRapida').on('input', function() {
            const termino = $(this).val().trim();
            
            // Limpiar timeout anterior
            if (timeoutBusqueda) {
                clearTimeout(timeoutBusqueda);
            }
            
            // Esperar 300ms después de que el usuario deje de escribir
            timeoutBusqueda = setTimeout(() => {
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
        const resultados = Object.values(productos).filter(producto => {
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
            productosFiltrados = Object.values(productos).filter(p => p.categoria === categoria);
        }
        
        mostrarResultadosBusqueda(productosFiltrados);
        $('#busquedaRapida').val('');
        
        toastr.info(`${productosFiltrados.length} productos en ${categoria === 'todas' ? 'todas las categorías' : categoria}`);
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
        resultados.forEach(producto => {
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
                    <td class="align-middle font-weight-bold text-success">$${precio.toFixed(2)}</td>
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

        // Configurar eventos
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
    // 4. FUNCIONES DEL CARRITO - SIN PARPADEO
    // =============================================

    function agregarAlCarrito(producto) {
        if (!producto || producto.stock <= 0) {
            toastr.error('Producto sin stock disponible');
            return;
        }

        const productoEnCarrito = carrito.find(item => item.id === producto.id);
        
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
        
        // ACTUALIZAR SIN EFECTOS DE PARPADEO
        actualizarCarrito();
        actualizarMetricas();
        toastr.success(`${producto.nombre} agregado al carrito`);
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
        
        carrito.forEach((item, index) => {
            const itemSubtotal = item.precio * item.cantidad;
            subtotal += itemSubtotal;
            
            // BOTONES -/+ ALREDEDOR DEL INPUT (menos a la izquierda, más a la derecha)
            const fila = `
                <tr>
                    <td class="align-middle">
                        <div class="font-weight-bold">${item.nombre}</div>
                        <small class="text-muted">${item.codigo}</small>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <!-- BOTÓN MENOS (IZQUIERDA) -->
                            <button class="btn btn-outline-secondary btn-sm btn-restar" data-index="${index}">-</button>
                            
                            <!-- INPUT CANTIDAD (CENTRO) -->
                            <input type="number" class="form-control text-center mx-1" 
                                   value="${item.cantidad}" readonly style="width: 50px;">
                            
                            <!-- BOTÓN MÁS (DERECHA) -->
                            <button class="btn btn-outline-secondary btn-sm btn-sumar" data-index="${index}">+</button>
                        </div>
                    </td>
                    <td class="align-middle font-weight-bold">$${itemSubtotal.toFixed(2)}</td>
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
        
        // Configurar eventos del carrito
        $('.btn-sumar').off('click').on('click', function() {
            const index = $(this).data('index');
            const producto = carrito[index];
            if (producto.cantidad < producto.stock) {
                producto.cantidad++;
                actualizarCarrito();
                actualizarMetricas();
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
            toastr.info(`${producto.nombre} eliminado`);
        });
    }

    // =============================================
    // 5. FUNCIONES AUXILIARES (sin cambios)
    // =============================================
    
    function actualizarTotales(subtotal = 0) {
        const ivaPorcentaje = parseFloat($('#selectIva').val()) || 0;
        const iva = subtotal * (ivaPorcentaje / 100);
        const total = subtotal + iva;
        
        $('#subtotalVenta').text(subtotal.toFixed(2));
        $('#ivaVenta').text(iva.toFixed(2));
        $('#totalVenta').text(total.toFixed(2));
        $('#porcentajeIva').text(ivaPorcentaje);
        
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
        
        carrito.forEach(item => {
            totalProductos += item.cantidad;
            totalVenta += item.precio * item.cantidad;
        });
        
        $('#metricTotalProductos').text(totalProductos);
        $('#metricVentaActual').text('$' + totalVenta.toFixed(2));
    }

    function calcularCambio() {
        const total = parseFloat($('#totalVenta').text()) || 0;
        const efectivo = parseFloat($('#efectivoRecibido').val()) || 0;
        const cambio = efectivo - total;
        
        if (cambio >= 0) {
            $('#cambioVenta').text('$' + cambio.toFixed(2));
        } else {
            $('#cambioVenta').text('-$' + Math.abs(cambio).toFixed(2));
        }
    }

    function calcularTotalMixto() {
        const efectivo = parseFloat($('#montoEfectivoMixto').val()) || 0;
        const tarjeta = parseFloat($('#montoTarjetaMixto').val()) || 0;
        const totalMixto = efectivo + tarjeta;
        
        $('#totalMixto').text('$' + totalMixto.toFixed(2));
    }

    function configurarMetodosPago() {
        $('#metodoPago').on('change', function() {
            $('.metodo-pago-detalle').addClass('d-none');
            
            const metodo = $(this).val();
            $(`#pago${metodo.charAt(0).toUpperCase() + metodo.slice(1)}`).removeClass('d-none');
            
            if (metodo === 'efectivo') {
                calcularCambio();
            } else if (metodo === 'mixto') {
                calcularTotalMixto();
            }
        });
        
        $('#efectivoRecibido').on('input', calcularCambio);
        $('#montoEfectivoMixto, #montoTarjetaMixto').on('input', calcularTotalMixto);
    }

    function cargarProductosFrecuentes() {
        $.ajax({
            url: '{{ route("productos.frecuentes") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.productos) {
                    mostrarProductosFrecuentes(response.productos);
                }
            },
            error: function(xhr) {
                console.error('Error al cargar productos frecuentes:', xhr);
            }
        });
    }

    function mostrarProductosFrecuentes(productosFrecuentes) {
        const contenedor = $('#productosFrecuentes');
        contenedor.empty();
        
        if (productosFrecuentes.length === 0) {
            contenedor.html('<p class="text-muted text-center">No hay productos frecuentes</p>');
            return;
        }
        
        productosFrecuentes.forEach(producto => {
            const card = `
                <div class="col-6 col-md-4 mb-3">
                    <div class="producto-card" onclick="agregarProductoFrecuente(${producto.id})">
                        <div class="text-center">
                            <i class="fas fa-star text-warning mb-2"></i>
                            <h6 class="mb-1">${producto.nombre}</h6>
                            <small class="text-muted d-block">${producto.codigo}</small>
                            <span class="badge badge-success">$${parseFloat(producto.precio).toFixed(2)}</span>
                            <small class="d-block mt-1">Stock: ${producto.stock}</small>
                        </div>
                    </div>
                </div>
            `;
            contenedor.append(card);
        });
    }

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

    function generarNumeroFactura() {
        let contador = localStorage.getItem('contadorFacturas') || 1;
        contador = parseInt(contador);
        localStorage.setItem('contadorFacturas', contador + 1);
        return `F-${contador.toString().padStart(5, '0')}`;
    }

    // =============================================
    // 6. INICIALIZACIÓN
    // =============================================

    function inicializarSistema() {
        console.log('🚀 Inicializando sistema...');
        
        configurarSelect2Clientes();
        cargarProductosDesdeDB();
        configurarMetodosPago();
        configurarBusquedaTiempoReal();
        
        
        // Eventos básicos
        $('#btnLimpiarCarrito').on('click', function() {
            if (carrito.length > 0 && confirm('¿Limpiar carrito?')) {
                carrito = [];
                actualizarCarrito();
                actualizarMetricas();
                toastr.success('Carrito limpiado');
            }
        });
        
        $('#selectIva').on('change', function() {
            const subtotal = parseFloat($('#subtotalVenta').text()) || 0;
            actualizarTotales(subtotal);
        });
        
        console.log('✅ Sistema inicializado');
        toastr.success('Sistema de punto de venta listo');
    }

    // Iniciar sistema
    setTimeout(inicializarSistema, 500);
});
</script>

@stop