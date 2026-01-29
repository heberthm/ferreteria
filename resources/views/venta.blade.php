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

    function cargarProductosFrecuentes() {
        $.ajax({
            url: '{{ route("productos/frecuentes") }}',
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
        
        productosFrecuentes.forEach(function(producto) {
            const card = `
                <div class="col-6 col-md-4 mb-3">
                    <div class="producto-card" onclick="window.agregarProductoFrecuente(${producto.id})">
                        <div class="text-center">
                            <i class="fas fa-star text-warning mb-2"></i>
                            <h6 class="mb-1">${producto.nombre}</h6>
                            <small class="text-muted d-block">${producto.codigo}</small>
                            <span class="badge badge-success">${formatoDinero(producto.precio)}</span>
                            <small class="d-block mt-1">Stock: ${producto.stock}</small>
                        </div>
                    </div>
                </div>
            `;
            contenedor.append(card);
        });
    }

    // =============================================
    // 8. PROCESAR VENTA - FUNCIÓN PRINCIPAL CORREGIDA
    // =============================================


$(document).on('click', '#btnProcesarVenta', function(e) {
    e.preventDefault();
    console.log('🖱️ Clic en botón COBRAR');
    
    // Verificar que hay productos en el carrito
    if (carrito.length === 0) {
        toastr.error('El carrito está vacío', 'Error');
        return;
    }
    
    console.log('📋 Productos en carrito:', carrito.length);
    
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
    
    $(document).on('click', '#modalScanner .close, #modalScanner [data-dismiss="modal"]', function(e) {
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
        
        // AGREGAR ESTAS LÍNEAS:
        configurarInputEfectivo();
        
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