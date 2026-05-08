@extends('layouts.app')

@section('content')

<style>
    .text-success { color: green; }
    .text-danger { color: red; }
    .badge-estado { font-size: 0.85em; padding: 4px 8px; }
    .filtro-ventas { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .filtro-ventas .form-group { margin-bottom: 10px; }
    .btn-accion-venta { margin-right: 5px; }
    .modal-lg-custom { max-width: 900px; }
    .table-ventas th { background-color: #f1f5f9; }
    .total-venta { font-weight: bold; color: #2c3e50; }
    
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
   .ticket-preview, .ticket-print {
    font-family: 'Courier New', monospace;
    width: 300px;
    margin: 0 auto;
    padding: 10px;
    font-size: 12px;
    line-height: 1.2;
    border: none !important;           /* Eliminar borde */
    background-color: white;
    box-shadow: none !important;       /* Eliminar sombra */
    max-height: none !important;
    overflow: visible !important;
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
    
    /* Estilos para factura - SIN RECUADRO EXTERNO */
   .factura-preview, .factura-print {
    font-family: Arial, sans-serif;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    background: white;
    box-sizing: border-box;
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
    
    /* Botones de vista previa */
    .vista-previa-buttons {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 15px;
        border-top: 1px solid #ddd;
        margin-top: 20px;
        z-index: 1000;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    
    /* Navegación entre vistas */
    .view-navigation {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    /* Contenedor de vista previa - fondo blanco sin sombras */
.preview-container {
    max-height: none !important;
    overflow-y: visible !important;
    padding: 20px;
    background: white !important;      /* Fondo blanco en lugar de gris */
    border-radius: 0 !important;       /* Sin bordes redondeados */
    box-shadow: none !important;       /* Sin sombra */
}
    
    /* Título de vista previa */
    .preview-title {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }
</style>

<br><br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history"></i> Historial de Ventas</h5>
        <div class="pull-right">
            <button type="button" id="descargarReporte" class="btn btn-success btn-sm">
                <span class="fa fa-download"></span> Exportar
            </button>
        </div>
    </div>
  
    <div class="card-body">
        <div class="filtro-ventas">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_desde" name="fecha_desde">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_hasta">Fecha hasta</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_hasta" name="fecha_hasta">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="estado_venta">Estado</label>
                        <select class="form-control form-control-sm" id="estado_venta" name="estado_venta">
                            <option value="">Todos</option>
                            <option value="completada">Completada</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="metodo_pago">Método de pago</label>
                        <select class="form-control form-control-sm" id="metodo_pago" name="metodo_pago">
                            <option value="">Todos</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="buscar_cliente">Buscar por cliente</label>
                        <input type="text" class="form-control form-control-sm" id="buscar_cliente" 
                               placeholder="Nombre o documento del cliente">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="buscar_factura">Buscar factura</label>
                        <input type="text" class="form-control form-control-sm" id="buscar_factura" 
                               placeholder="Número de factura">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="button" id="btnFiltrar" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" id="btnLimpiarFiltros" class="btn btn-secondary btn-sm w-100 mt-1">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de ventas -->
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="tablaVentas" style="width:100%;font-size:12.5px;">
                <thead class="thead-light">
                    <tr>
                        <th>Factura</th>
                        <th>Fecha/Hora</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Cant.</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos se cargarán via AJAX -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total general:</strong></td>
                        <td id="totalGeneral" class="total-venta">$0</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalle de Venta -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" role="dialog" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalDetalleVentaLabel">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Detalle de Venta
                </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- VISTA PREVIA DE TICKET (oculta inicialmente) -->
                <div id="vistaPreviaTicket" style="display: none;">
                    <div class="preview-title">
                        <h4><i class="fas fa-receipt"></i> Vista Previa - Ticket de Venta</h4>
                        <p class="text-muted">Revise el ticket antes de imprimir</p>
                    </div>
                    
                    <div class="view-navigation">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Volver al Detalle
                        </button>
                        <div>
                            <button type="button" class="btn btn-info" onclick="ajustarTicket('disminuir')">
                                <i class="fas fa-search-minus"></i> Alejar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarTicket('aumentar')">
                                <i class="fas fa-search-plus"></i> Acercar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarTicket('reset')">
                                <i class="fas fa-sync-alt"></i> Tamaño Original
                            </button>
                        </div>
                    </div>
                    
                    <div class="preview-container" style="max-height: none !important; overflow-y: visible !important;">
                        <div class="print-content">
                            <div class="ticket-preview" id="ticketPreview" style="transform: scale(1); transform-origin: top center;">
                                <!-- Contenido del ticket se generará aquí -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="vista-previa-buttons">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="imprimirTicket()">
                            <i class="fas fa-print"></i> Imprimir Ticket
                        </button>
                    </div>
                </div>
                
                <!-- VISTA PREVIA DE FACTURA (oculta inicialmente) -->
                <div id="vistaPreviaFactura" style="display: none;">
                    <div class="preview-title">
                        <h4><i class="fas fa-file-invoice"></i> Vista Previa - Factura de Venta</h4>
                        <p class="text-muted">Revise la factura antes de imprimir</p>
                    </div>
                    
                    <div class="view-navigation">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Volver al Detalle
                        </button>
                        <div>
                            <button type="button" class="btn btn-info" onclick="ajustarFactura('disminuir')">
                                <i class="fas fa-search-minus"></i> Alejar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarFactura('aumentar')">
                                <i class="fas fa-search-plus"></i> Acercar
                            </button>
                            <button type="button" class="btn btn-info" onclick="ajustarFactura('reset')">
                                <i class="fas fa-sync-alt"></i> Tamaño Original
                            </button>
                        </div>
                    </div>
                    
                    <div class="preview-container">
                        <div class="print-content">
                            <div class="factura-preview" id="facturaPreview" style="transform: scale(0.8); transform-origin: top center;">
                                <!-- Contenido de la factura se generará aquí -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="vista-previa-buttons">
                        <button type="button" class="btn btn-secondary" onclick="volverAlDetalle()">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="imprimirFactura()">
                            <i class="fas fa-print"></i> Imprimir Factura
                        </button>
                    </div>
                </div>
                
                <!-- CONTENIDO DETALLE (visible inicialmente) -->
                <div id="contenidoDetalle">
                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">INFORMACIÓN DE LA VENTA</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="font-weight-bold" style="width: 40%;">Factura:</td>
                                    <td id="modalFactura">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Fecha:</td>
                                    <td id="modalFecha">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Hora:</td>
                                    <td id="modalHora">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Estado:</td>
                                    <td><span id="modalEstado" class="badge">-</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">INFORMACIÓN DEL CLIENTE</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="font-weight-bold" style="width: 40%;">Cliente:</td>
                                    <td id="modalCliente">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Documento:</td>
                                    <td id="modalDocumento">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Método de Pago:</td>
                                    <td id="modalMetodoPago">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Vendedor:</td>
                                    <td id="modalVendedor">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Detalle de Productos -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-boxes mr-2"></i>Productos Vendidos</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Código</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-right">Precio Unit.</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalDetalleProductos">
                                        <!-- Los productos se llenarán aquí -->
                                    </tbody>
                                   <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="text-right font-weight-bold">Subtotal:</td>
                                            <td class="text-right" id="modalSubtotalProductos">$0.00</td>
                                        </tr>
                                        <tr id="filaDescuento" style="display:none;">
                                            <td colspan="3"></td>
                                            <td class="text-right">Descuento:</td>
                                            <td class="text-right text-success" id="modalDescuento">-$0.00</td>
                                        </tr>
                                        <tr id="filaIVA" style="display:none;">
                                            <td colspan="3"></td>
                                            <td class="text-right">IVA (19%):</td>
                                            <td class="text-right" id="modalIVA">$0.00</td>
                                        </tr>
                                        <tr id="filaOtrosCargos" style="display:none;">
                                            <td colspan="3"></td>
                                            <td class="text-right">IVA:</td>
                                            <td class="text-right" id="modalOtrosCargos">$0.00</td>
                                        </tr>
                                        <tr class="font-weight-bold" style="border-top: 2px solid #dee2e6;">
                                            <td colspan="3"></td>
                                            <td class="text-right" style="font-size: 1.1em;">TOTAL:</td>
                                            <td class="text-right" style="font-size: 1.1em;" id="modalTotalVenta">$0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mt-3" id="modalObservacionesContainer" style="display: none;">
                        <h6 class="text-muted"><i class="fas fa-sticky-note mr-2"></i>Observaciones</h6>
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <p class="mb-0" id="modalObservaciones"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <div class="modal-footer no-print">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
                
                <button type="button" class="btn btn-danger ms-2 d-none" id="btnEliminarVentaModal" onclick="eliminarVenta(modalVentaId)">
                    <i class="fas fa-trash mr-1"></i> Eliminar Factura
                </button>

                <!-- Botones de impresión SEPARADOS pero uno al lado del otro -->
                <button type="button" class="btn btn-primary me-2" onclick="mostrarVistaPrevia('ticket')">
                    <i class="fas fa-receipt mr-1"></i> Vista Previa Ticket
                </button>
                
                <button type="button" class="btn btn-info" onclick="mostrarVistaPrevia('factura')">
                    <i class="fas fa-file-invoice mr-1"></i> Vista Previa Factura
                </button>
                
                <!-- Botón para cancelar venta (solo para ventas no canceladas) -->
                <button type="button" class="btn btn-danger ms-2 d-none" id="btnCancelarVentaModal" onclick="cancelarVenta(modalVentaId)">
                    <i class="fas fa-ban mr-1"></i> Cancelar Venta
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para imprimir/compartir reporte -->
<div class="modal fade" id="modalReporte" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-file-export"></i> Exportar Reporte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="formatoReporte">Formato de exportación</label>
                    <select class="form-control" id="formatoReporte">
                        <option value="pdf">PDF (Recomendado)</option>
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="rangoReporte">Rango de fechas</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="fechaInicioReporte">
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="fechaFinReporte">
                        </div>
                    </div>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="incluirDetalles" checked>
                    <label class="form-check-label" for="incluirDetalles">
                        Incluir detalles de productos
                    </label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="incluirTotales" checked>
                    <label class="form-check-label" for="incluirTotales">
                        Incluir totales y resúmenes
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarReporte">
                    <i class="fas fa-download"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

<script>
var tablaVentas;
var modalVentaId = null;
var datosVenta = null;
var datosCliente = null;
var datosVendedor = null;
var detallesVenta = null;
var escalaTicket = 1;
var escalaFactura = 0.8;

// Variables para almacenar el contenido generado
var contenidoTicketGenerado = '';
var contenidoFacturaGenerado = '';

// Variables globales - Agregar datos de empresa
var datosEmpresa = {
    nombre: 'SUPERMERCADO XYZ',
    nit: '123456789-0',
    telefono: '(601) 123-4567',
    direccion: 'Calle 123 #45-67',
    email: 'info@superxyz.com',
    mensaje: '¡Gracias por su compra!',
    logo_url: null
};

// ============================================
// FUNCIÓN PARA FORMATEAR NÚMEROS SIN DECIMALES
// ============================================
function formatSinDecimales(numero) {
    var entero = Math.round(numero);
    return entero.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

// ============================================
// VER DETALLE DE VENTA
// ============================================
function verDetalleVenta(id) {
    modalVentaId = id;
    console.log('Cargando detalle venta ID:', id);
    
    $.ajax({
        url: "{{ url('ventas/detalle') }}/" + id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            console.log('Respuesta recibida:', response);
            
            if (response.success) {
                datosVenta = response.data.venta;
                datosCliente = response.data.cliente;
                datosVendedor = response.data.vendedor;
                detallesVenta = response.data.detalles;
                
                // Cargar datos de la empresa
                if (response.data.empresa) {
                   datosEmpresa = response.data.empresa;
                    // También actualizar la variable global window.datosEmpresa
                    window.datosEmpresa = datosEmpresa;
                    console.log('✅ Datos empresa cargados en historial:', datosEmpresa);
                }
                
                // MOSTRAR DATOS DE EMPRESA EN EL MODAL - Agregar sección
                var empresaHtml = `
                    <div class="card mb-3 bg-light">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Empresa:</small>
                                    <strong>${datosEmpresa.nombre}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">NIT:</small>
                                    <strong>${datosEmpresa.nit}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Teléfono:</small>
                                    <strong>${datosEmpresa.telefono}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Dirección:</small>
                                    <strong>${datosEmpresa.direccion}</strong>
                                </div>
                            </div>
                            ${datosEmpresa.email ? `<div class="row mt-1"><div class="col-12"><small class="text-muted">Email:</small> <strong>${datosEmpresa.email}</strong></div></div>` : ''}
                        </div>
                    </div>
                `;
                
                // Insertar datos de empresa al inicio del contenidoDetalle
                if ($('#empresaInfo').length === 0) {
                    $('#contenidoDetalle').prepend('<div id="empresaInfo">' + empresaHtml + '</div>');
                } else {
                    $('#empresaInfo').html(empresaHtml);
                }
                
                // Información de la venta
                $('#modalFactura').text(datosVenta.numero_factura || 'N/A');
                $('#modalFecha').text(datosVenta.fecha || 'N/A');
                $('#modalHora').text(datosVenta.hora || 'N/A');
                
                // Estado con badge
                var estado = datosVenta.estado || 'pendiente';
                var estadoTexto = '';
                var badgeClass = '';
                
                switch(estado) {
                    case 'completada':
                        estadoTexto = 'Completada';
                        badgeClass = 'badge-success';
                        break;
                    case 'pendiente':
                        estadoTexto = 'Pendiente';
                        badgeClass = 'badge-warning';
                        break;
                    case 'cancelada':
                        estadoTexto = 'Cancelada';
                        badgeClass = 'badge-danger';
                        break;
                    default:
                        estadoTexto = estado.charAt(0).toUpperCase() + estado.slice(1);
                        badgeClass = 'badge-secondary';
                }
                
                $('#modalEstado').removeClass().addClass('badge ' + badgeClass).text(estadoTexto);
                
                // Información del cliente
                $('#modalCliente').text(datosCliente ? datosCliente.nombre : 'Cliente General');
                $('#modalDocumento').text(datosCliente ? (datosCliente.cedula || 'N/A') : 'N/A');
                $('#modalMetodoPago').text(datosVenta.metodo_pago ? datosVenta.metodo_pago.charAt(0).toUpperCase() + datosVenta.metodo_pago.slice(1) : 'N/A');
                $('#modalVendedor').text(datosVendedor ? datosVendedor.nombre : 'N/A');
                
                // Detalle de productos
                var htmlProductos = '';
                var subtotalProductos = 0;
                
                if (detallesVenta && detallesVenta.length > 0) {
                    detallesVenta.forEach(function(p, index) {
                        var cantidad = parseFloat(p.cantidad) || 0;
                        var precioUnitario = parseFloat(p.precio_unitario) || 0;
                        var subtotal = parseFloat(p.subtotal) || 0;
                        
                        subtotalProductos += subtotal;
                        
                        htmlProductos += '<tr>' +
                            '<td>' + (p.nombre || 'Producto sin nombre') + '</td>' +
                            '<td>' + (p.codigo || 'N/A') + '</td>' +
                            '<td class="text-center">' + cantidad + '</td>' +
                            '<td class="text-right">$' + precioUnitario.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + '</td>' +
                            '<td class="text-right">$' + subtotal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + '</td>' +
                            '</tr>';
                    });
                } else {
                    htmlProductos = '<tr><td colspan="5" class="text-center text-muted">No hay productos registrados</td></tr>';
                }
                
                $('#modalDetalleProductos').html(htmlProductos);
                
                // Obtener valores numéricos
                var totalVenta = parseFloat(datosVenta.total_numero || datosVenta.total || 0);
                var subtotal = parseFloat(datosVenta.subtotal_numero || subtotalProductos);
                var iva = parseFloat(datosVenta.iva_numero || 0);
                var descuento = parseFloat(datosVenta.descuento_numero || 0);
                
                // Mostrar subtotal
                $('#modalSubtotalProductos').text('$' + subtotal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                
                // Ocultar todas las filas extras primero
                $('#filaIVA, #filaDescuento, #filaOtrosCargos').hide();
                
                // Mostrar IVA si existe
                if (iva > 0) {
                    $('#modalIVA').text('$' + iva.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                    $('#filaIVA').show();
                }
                
                // Mostrar Descuento si existe
                if (descuento > 0) {
                    $('#modalDescuento').text('-$' + descuento.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                    $('#filaDescuento').show();
                }
                
                // Mostrar TOTAL FINAL
                $('#modalTotalVenta').text('$' + totalVenta.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                
                // Observaciones
                var observaciones = datosVenta.observaciones || '';
                if (observaciones && observaciones.trim() !== '') {
                    $('#modalObservaciones').text(observaciones);
                    $('#modalObservacionesContainer').show();
                } else {
                    $('#modalObservacionesContainer').hide();
                }
                
                // Mostrar/ocultar botón de cancelar
                var btnCancelar = $('#btnCancelarVentaModal');
                if (estado !== 'cancelada' && estado !== 'completada') {
                    btnCancelar.removeClass('d-none');
                } else {
                    btnCancelar.addClass('d-none');
                }
                
                // Mostrar modal
                mostrarContenidoDetalle();
                $('#modalDetalleVenta').modal('show');
                
            } else {
                var errorMsg = response.message || 'No se pudo cargar el detalle de la venta';
                alert('Error: ' + errorMsg);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            alert('Error al cargar el detalle de la venta. Por favor, intente nuevamente.');
        }
    });
}  


// ============================================
// FUNCIONES DE VISTA PREVIA
// ============================================

// Función para mostrar vista previa
function mostrarVistaPrevia(tipo) {
    prepararDatosVistaPrevia(tipo);
    
    if (tipo === 'ticket') {
        mostrarVistaPreviaTicket();
    } else {
        mostrarVistaPreviaFactura();
    }
}

// ============================================
// PREPARAR DATOS DE VISTA PREVIA
// ============================================
function prepararDatosVistaPrevia(tipo) {
    if (!datosVenta) return;
    
    // Usar los valores numéricos correctos
    var totalVenta = datosVenta.total_numero || 0;
    var subtotalProductos = datosVenta.subtotal_numero || 0;
    var iva = datosVenta.iva_numero || 0;
    var descuento = datosVenta.descuento_numero || 0;
    
    // Calcular total de productos vendidos
    var totalProductosVendidos = 0;
    if (detallesVenta && detallesVenta.length > 0) {
        detallesVenta.forEach(function(p) {
            totalProductosVendidos += parseInt(p.cantidad) || 0;
        });
    }
    
    // Tiene IVA o descuento
    var tieneIVA = iva > 0;
    var tieneDescuento = descuento > 0;
    var valorIVA = iva;
    var valorDescuento = descuento;
    
    // Logo de empresa (si existe)
    var logoSrc = null;
    if (typeof datosEmpresa !== 'undefined' && datosEmpresa.logo_url) {
        logoSrc = datosEmpresa.logo_url;
        if (logoSrc && logoSrc.includes('/storage/storage/')) {
            logoSrc = logoSrc.replace('/storage/storage/', '/storage/');
        }
    }
    
    var logoTicketHTML = logoSrc ? `<img src="${logoSrc}" alt="Logo" style="max-height: 40px; margin-bottom: 5px;">` : '';
    var logoFacturaHTML = logoSrc ? `<div style="text-align: center; margin-bottom: 20px;"><img src="${logoSrc}" alt="Logo" style="max-height: 100px;"></div>` : '';
    
    var nombreEmpresa = (typeof datosEmpresa !== 'undefined' && datosEmpresa.nombre) ? datosEmpresa.nombre : 'SUPERMERCADO XYZ';
    var nitEmpresa = (typeof datosEmpresa !== 'undefined' && datosEmpresa.nit) ? datosEmpresa.nit : '123456789-0';
    var telefonoEmpresa = (typeof datosEmpresa !== 'undefined' && datosEmpresa.telefono) ? datosEmpresa.telefono : '(601) 123-4567';
    var direccionEmpresa = (typeof datosEmpresa !== 'undefined' && datosEmpresa.direccion) ? datosEmpresa.direccion : 'Calle 123 #45-67';
    var emailEmpresa = (typeof datosEmpresa !== 'undefined' && datosEmpresa.email) ? datosEmpresa.email : '';
    var mensajeEmpresa = (typeof datosEmpresa !== 'undefined' && datosEmpresa.mensaje) ? datosEmpresa.mensaje : '¡GRACIAS POR SU COMPRA!';
    
    if (tipo === 'ticket') {
        contenidoTicketGenerado = `
           <!-- HEADER CON DATOS DE EMPRESA -->
            <div style="text-align: center; padding-bottom: 8px; border-bottom: 1px dashed #000; margin-bottom: 8px;">
                ${datosEmpresa.logo_url ? `<img src="${datosEmpresa.logo_url}" style="max-height: 40px; margin-bottom: 5px;">` : ''}
                <h4 style="margin: 0; font-size: 14px; font-weight: bold;">${datosEmpresa.nombre}</h4>
                <p style="margin: 2px 0; font-size: 10px;">NIT: ${datosEmpresa.nit}</p>
                <p style="margin: 2px 0; font-size: 9px;">${datosEmpresa.direccion}</p>
                <p style="margin: 2px 0; font-size: 9px;">Tel: ${datosEmpresa.telefono}</p>
                ${datosEmpresa.email ? `<p style="margin: 2px 0; font-size: 9px;">${datosEmpresa.email}</p>` : ''}
                <hr style="border-top: 1px dashed #000; margin: 5px 0;">
                <p style="margin: 2px 0;"><strong>FACTURA:</strong> ${datosVenta.numero_factura || 'N/A'}</p>
                <p style="margin: 2px 0; display: flex; justify-content: center; gap: 10px;">
                    <span><strong>FECHA:</strong> ${datosVenta.fecha || 'N/A'}</span>
                    <span><strong>HORA:</strong> ${datosVenta.hora || 'N/A'}</span>
                </p>
            </div>
                    
                    <div style="margin: 6px 0;">
                        <p><strong>CLIENTE:</strong> ${datosCliente ? datosCliente.nombre : 'Cliente General'}</p>
                        <p><strong>VENDEDOR:</strong> ${datosVendedor ? datosVendedor.nombre : 'N/A'}</p>
                    </div>
                    
                    <hr style="margin: 5px 0;">
                    
                    <div style="display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #000; padding: 4px 0;">
                        <div style="width: 40%;">PRODUCTO</div>
                        <div style="width: 15%; text-align: center;">CANT</div>
                        <div style="width: 20%; text-align: right;">P.UNIT</div>
                        <div style="width: 25%; text-align: right;">TOTAL</div>
                    </div>
        `;
        
        if (detallesVenta && detallesVenta.length > 0) {
            detallesVenta.forEach(function(p) {
                var nombre = p.nombre || 'Producto';
                if (nombre.length > 20) nombre = nombre.substring(0, 17) + '...';
                contenidoTicketGenerado += `
                    <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dotted #ccc;">
                        <div style="width: 40%;">${nombre}</div>
                        <div style="width: 15%; text-align: center;">${p.cantidad}</div>
                        <div style="width: 20%; text-align: right;">$${Math.round(p.precio_unitario).toLocaleString('es-CO')}</div>
                        <div style="width: 25%; text-align: right;">$${Math.round(p.subtotal).toLocaleString('es-CO')}</div>
                    </div>
                `;
            });
        }
        
        contenidoTicketGenerado += `
                    <hr style="margin: 5px 0;">
                    <div style="text-align: right;">
                        <p>TOTAL PRODUCTOS: ${totalProductosVendidos} und</p>
                        <p>Subtotal: $${Math.round(subtotalProductos).toLocaleString('es-CO')}</p>
                        ${tieneIVA ? `<p>IVA (19%): $${Math.round(valorIVA).toLocaleString('es-CO')}</p>` : ''}
                        ${tieneDescuento ? `<p>Descuento: -$${Math.round(valorDescuento).toLocaleString('es-CO')}</p>` : ''}
                        <p style="font-weight: bold; border-top: 1px dashed #000; padding-top: 3px;">TOTAL: $${Math.round(totalVenta).toLocaleString('es-CO')}</p>
                        <p><strong>PAGO:</strong> ${datosVenta.metodo_pago || 'N/A'}</p>
                    </div>
                    
                    <div style="text-align: center; border-top: 1px dashed #000; padding-top: 8px; margin-top: 8px;">
                        <p><strong>${mensajeEmpresa}</strong></p>
                        <p>${new Date().toLocaleDateString('es-CO')} ${new Date().toLocaleTimeString('es-CO')}</p>
                    </div>
                </div>
            </div>
        `;
        $('#ticketPreview').html(contenidoTicketGenerado);
        
    } else {
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
                <div style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #27292a;">
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
// GENERAR FACTURA - MISMO FORMATO QUE PUNTO DE VENTA
// CON TODOS LOS DATOS DE LA EMPRESA
// ============================================
function generarFacturaHistorial(datos) {
    const venta = datos.venta;
    const cliente = datos.cliente;
    const vendedor = datos.vendedor;
    const detalles = datos.detalles;
    const empresa = datos.empresa;
    
    // Calcular totales
    let subtotalProductos = 0;
    let totalProductosVendidos = 0;
    
    if (detalles && detalles.length > 0) {
        detalles.forEach(function(p) {
            subtotalProductos += parseFloat(p.subtotal) || 0;
            totalProductosVendidos += parseInt(p.cantidad) || 0;
        });
    }
    
    const totalVenta = parseFloat(venta.total_numero || venta.total || 0);
    const iva = parseFloat(venta.iva_numero || venta.iva || 0);
    const descuento = parseFloat(venta.descuento_numero || venta.descuento || 0);
    const tieneIVA = iva > 0;
    const tieneDescuento = descuento > 0;
    
    // LOGO DE LA EMPRESA
    let logoSrc = empresa ? empresa.logo_url : null;
    let logoFacturaHTML = '';
    
    if (logoSrc && logoSrc !== 'null' && logoSrc !== '') {
        if (logoSrc.includes('/storage/storage/')) {
            logoSrc = logoSrc.replace('/storage/storage/', '/storage/');
        }
        logoFacturaHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="${logoSrc}" alt="Logo" style="max-height: 80px; max-width: 150px; object-fit: contain;">
        </div>
        `;
    }
    
    // DATOS DE LA EMPRESA
    const nombreEmpresa = empresa ? empresa.nombre : 'SUPERMERCADO XYZ';
    const nitEmpresa = empresa ? empresa.nit : '123456789-0';
    const telefonoEmpresa = empresa ? empresa.telefono : '(601) 123-4567';
    const direccionEmpresa = empresa ? empresa.direccion : 'Calle 123 #45-67';
    const emailEmpresa = empresa ? empresa.email : '';
    const mensajeEmpresa = empresa ? empresa.mensaje : '¡Gracias por su compra!';
    
    // Fechas
    const fechaFormateada = venta.fecha || new Date().toLocaleDateString('es-CO');
    const horaFormateada = venta.hora || new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
    
    // Datos del cliente
    const nombreCliente = cliente ? cliente.nombre : 'Cliente General';
    const documentoCliente = cliente ? (cliente.cedula || 'N/A') : 'N/A';
    
    // Vendedor y pago
    const nombreVendedor = vendedor ? vendedor.nombre : 'N/A';
    const metodoPago = venta.metodo_pago ? venta.metodo_pago.charAt(0).toUpperCase() + venta.metodo_pago.slice(1) : 'N/A';
    
    function formatNumber(num) {
        return Math.round(num).toLocaleString('es-CO');
    }
    
    // ============================================
    // FACTURA CON MÁRGENES CORRECTOS
    // ============================================
    return `
    <div style="font-family: Arial, sans-serif; width: 100%; max-width: 1000px; margin: 0 auto; padding: 0; box-sizing: border-box;">
        
        <!-- CONTENEDOR PRINCIPAL CON MÁRGENES LATERALES -->
        <div style="padding: 20px 35px; background: white; box-sizing: border-box;">
            
            ${logoFacturaHTML}
            
            <!-- ENCABEZADO -->
            <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px;">
                <h1 style="margin: 0; font-size: 22px;">FACTURA DE VENTA</h1>
                <h3 style="margin: 8px 0; font-size: 16px;">${nombreEmpresa}</h3>
                <div style="margin-top: 5px; font-size: 11px; line-height: 1.4;">
                    <div>NIT: ${nitEmpresa} | Tel: ${telefonoEmpresa}</div>
                    <div>${direccionEmpresa}</div>
                    ${emailEmpresa ? `<div>Email: ${emailEmpresa}</div>` : ''}
                </div>
            </div>

            <!-- INFORMACIÓN FACTURA Y CLIENTE - 2 COLUMNAS EQUILIBRADAS -->
            <div style="display: flex; gap: 20px; margin-bottom: 20px; border: 1px solid #aaa; padding: 12px 15px; background: #fef9e6;">
                
                <!-- Columna izquierda: Factura -->
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 8px 0; font-size: 12px; background: #000; color: white; padding: 4px 8px; display: inline-block;">📄 INFORMACIÓN FACTURA</h4>
                    <div style="font-size: 12px; margin-top: 8px;">
                        <div style="margin: 5px 0;"><strong>No. Factura:</strong> ${venta.numero_factura || 'N/A'}</div>
                        <div style="margin: 5px 0;"><strong>Fecha:</strong> ${fechaFormateada}</div>
                        <div style="margin: 5px 0;"><strong>Hora:</strong> ${horaFormateada}</div>
                        <div style="margin: 5px 0;"><strong>Estado:</strong> ${venta.estado ? venta.estado.charAt(0).toUpperCase() + venta.estado.slice(1) : 'Completada'}</div>
                        <div style="margin: 5px 0;"><strong>Vendedor:</strong> ${nombreVendedor}</div>
                    </div>
                </div>
                
                <!-- Columna derecha: Cliente -->
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 8px 0; font-size: 12px; background: #000; color: white; padding: 4px 8px; display: inline-block;">👤 INFORMACIÓN CLIENTE</h4>
                    <div style="font-size: 12px; margin-top: 8px;">
                        <div style="margin: 5px 0;"><strong>Nombre:</strong> ${nombreCliente}</div>
                        <div style="margin: 5px 0;"><strong>Documento:</strong> ${documentoCliente}</div>
                        <div style="margin: 5px 0;"><strong>Método de Pago:</strong> ${metodoPago}</div>
                    </div>
                </div>
            </div>

            <!-- TOTAL DE PRODUCTOS -->
            <div style="margin-bottom: 15px; padding: 6px 12px; background: #f8f9fa; border-left: 4px solid #000;">
                <strong style="font-size: 12px;">📦 TOTAL DE PRODUCTOS VENDIDOS:</strong> ${totalProductosVendidos} unidades
            </div>

            <!-- TABLA DE PRODUCTOS - CON ESPACIADO ADECUADO -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="border-bottom: 2px solid #000; padding: 8px 6px; text-align: left;">PRODUCTO</th>
                        <th style="border-bottom: 2px solid #000; padding: 8px 6px; text-align: left;">CÓDIGO</th>
                        <th style="border-bottom: 2px solid #000; padding: 8px 6px; text-align: center;">CANT</th>
                        <th style="border-bottom: 2px solid #000; padding: 8px 6px; text-align: right;">P.UNIT</th>
                        <th style="border-bottom: 2px solid #000; padding: 8px 6px; text-align: right;">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    ${detalles && detalles.length > 0 ? detalles.map(p => `
                    <tr>
                        <td style="border-bottom: 1px solid #ddd; padding: 6px;">${p.nombre || 'Producto'}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 6px;">${p.codigo || 'N/A'}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 6px; text-align: center;">${p.cantidad}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 6px; text-align: right;">$${formatNumber(p.precio_unitario)}</td>
                        <td style="border-bottom: 1px solid #ddd; padding: 6px; text-align: right;">$${formatNumber(p.subtotal)}</td>
                    </tr>
                    `).join('') : '<tr><td colspan="5" style="padding: 20px; text-align: center;">No hay productos registrados</td></tr>'}
                </tbody>
            </table>

            <!-- TOTALES - ALINEADOS A LA DERECHA -->
            <div style="margin-top: 5px; border-top: 2px solid #000; padding-top: 12px;">
                <div style="display: flex; justify-content: flex-end;">
                    <div style="width: 100%; max-width: 280px;">
                        <table style="width: 100%; font-size: 12px;">
                            <tr><td style="padding: 4px;"><strong>Subtotal:</strong></td><td style="text-align: right;">$${formatNumber(subtotalProductos)}</td></tr>
                            ${tieneIVA ? `<tr><td style="padding: 4px;"><strong>IVA (19%):</strong></td><td style="text-align: right;">$${formatNumber(iva)}</td></tr>` : ''}
                            ${tieneDescuento ? `<tr><td style="padding: 4px;"><strong>Descuento:</strong></td><td style="text-align: right;">-$${formatNumber(descuento)}</td></tr>` : ''}
                            <tr style="border-top: 1px solid #000;"><td style="padding: 6px 4px 0 4px;"><strong>TOTAL:</strong></td><td style="text-align: right; padding: 6px 4px 0 4px;"><strong>$${formatNumber(totalVenta)}</strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MENSAJE PERSONALIZADO -->
            <div style="margin-top: 25px; text-align: center; font-size: 11px; background: #f9f9f9; padding: 8px; border-radius: 3px;">
                <p style="margin: 0;">💚 ${mensajeEmpresa} 💚</p>
            </div>

            <!-- FIRMAS -->
            <div style="display: flex; justify-content: space-between; margin-top: 35px; gap: 20px;">
                <div style="flex: 1; text-align: center;">
                    <hr style="border-top: 1px solid #000; width: 70%; margin: 0 auto;">
                    <p style="margin-top: 8px; font-size: 11px;">Firma del Cliente</p>
                </div>
                <div style="flex: 1; text-align: center;">
                    <hr style="border-top: 1px solid #000; width: 70%; margin: 0 auto;">
                    <p style="margin-top: 8px; font-size: 11px;">Firma del Vendedor</p>
                </div>
            </div>

            <!-- PIE DE PÁGINA -->
            <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
                <p style="margin: 3px 0;">📅 Documento generado el: ${new Date().toLocaleDateString('es-CO')}</p>
                <p style="margin: 3px 0;">✅ Este documento es válido como factura de venta</p>
            </div>
            
        </div>
    </div>
    `;
}

// ============================================
// MOSTRAR VISTA PREVIA DEL TICKET (80mm CENTRADO)
// ============================================
function mostrarVistaPreviaTicket() {
    prepararDatosVistaPrevia('ticket');
    ocultarTodosContenidos();
    $('#vistaPreviaTicket').show();

    // FORZAR contenedor centrado
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

    // APLICAR ESCALA INICIAL
    aplicarEscalaTicket();
}

// ============================================
// MOSTRAR VISTA PREVIA DE LA FACTURA
// ============================================
function mostrarVistaPreviaFactura() {
    if (!datosVenta) return;
    
    console.log('🖨️ Generando vista previa de factura con datos:', datosEmpresa);
    
    const datos = {
        venta: datosVenta,
        cliente: datosCliente,
        vendedor: datosVendedor,
        detalles: detallesVenta,
        empresa: datosEmpresa || null
    };
    
    // Generar el HTML de la factura
    const facturaHTML = generarFacturaHistorial(datos);
    $('#facturaPreview').html(facturaHTML);
    
    ocultarTodosContenidos();
    $('#vistaPreviaFactura').show();
    aplicarEscalaFactura();
    
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
// IMPRIMIR TICKET (80mm)
// ============================================
function imprimirTicket() {
    // Guardar el estado actual
    var currentScale = escalaTicket;
    
    // Resetear la escala temporalmente
    $('#ticketPreview').css('transform', 'scale(1)');
    
    // Crear contenido para impresión
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ticket de Venta</title>
            <style>
                @page { 
                    size: 80mm auto; 
                    margin: 0; 
                }
                body { 
                    margin: 0; 
                    padding: 0; 
                    font-family: 'Courier New', monospace; 
                    background: white; 
                    width: 80mm;
                }
                .ticket-print { 
                    width: 80mm; 
                    margin: 0 auto; 
                    padding: 10px; 
                    box-sizing: border-box; 
                }
                @media print {
                    body { 
                        margin: 0 !important; 
                        padding: 0 !important; 
                        width: 80mm !important; 
                    }
                    .ticket-print { 
                        width: 80mm !important; 
                        padding: 10px !important; 
                    }
                }
            </style>
        </head>
        <body>
            <div class="ticket-print">
                ${contenidoTicketGenerado}
            </div>
        </body>
        </html>
    `;
    
    // Crear iframe temporal
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
    
    // Imprimir
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            $('#ticketPreview').css('transform', 'scale(' + currentScale + ')');
            document.body.removeChild(iframe);
        }, 100);
    }, 100);
}

// ============================================
// IMPRIMIR FACTURA (A4) - CORREGIDO
// ============================================
function imprimirFactura() {
    console.log('🖨️ Imprimiendo factura con datos empresa:', datosEmpresa);
    
    // Preparar los datos para la factura
    const datos = {
        venta: datosVenta,
        cliente: datosCliente,
        vendedor: datosVendedor,
        detalles: detallesVenta,
        empresa: datosEmpresa  // Usar los datos de empresa cargados
    };
    
    // Generar el HTML de la factura usando la función dedicada
    const facturaHTML = generarFacturaHistorial(datos);
    
    // Crear contenido para impresión
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Factura de Venta</title>
            <meta charset="UTF-8">
            <style>
                @page { 
                    size: A4; 
                    margin: 0; 
                }
                body { 
                    margin: 0; 
                    padding: 20px; 
                    font-family: Arial, sans-serif; 
                    background: white; 
                    border: none !important;
                    box-shadow: none !important;
                }
                .factura-print { 
                    border: none !important;
                    box-shadow: none !important;
                    padding: 10px !important;
                    margin: 0 auto !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-left { text-align: left; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 8px; }
                .border-bottom { border-bottom: 1px solid #ddd; }
                .border-top { border-top: 2px solid #000; }
                .bg-light { background: #f8f9fa; }
                .font-bold { font-weight: bold; }
                hr { margin: 10px 0; }
                @media print {
                    body { margin: 0; padding: 0; }
                    .factura-print { margin: 0; padding: 15px; }
                }
            </style>
        </head>
        <body>
            <div class="factura-print">
                ${facturaHTML}
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 500);
                }
            <\/script>
        </body>
        </html>
    `;
    
   // Crear iframe temporal
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
    
    // Imprimir
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            $('#facturaPreview').css('transform', 'scale(' + currentScale + ')');
            document.body.removeChild(iframe);
        }, 100);
    }, 100);
}

// ============================================
// CONTROL DE VISTAS
// ============================================

function mostrarContenidoDetalle() {
    ocultarTodosContenidos();
    $('#contenidoDetalle').show();
    
    // Eliminar la sección de empresa si existe
    if ($('#empresaInfo').length) {
        $('#empresaInfo').remove();
    }
    
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

// ============================================
// CANCELAR VENTA
// ============================================
function cancelarVenta(id) {
    id = id || modalVentaId;
    
    if (!id) {
        alert('No se puede cancelar la venta. ID no válido.');
        return;
    }
    
    if (confirm('¿Está seguro de cancelar esta venta? Esta acción no se puede deshacer.')) {
        $.ajax({
            url: "{{ url('ventas/cancelar') }}/" + id,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    alert('Venta cancelada exitosamente');
                    $('#modalDetalleVenta').modal('hide');
                    if (typeof tablaVentas !== 'undefined') {
                        tablaVentas.ajax.reload();
                    }
                } else {
                    alert('Error: ' + (response.message || 'No se pudo cancelar la venta'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cancelar venta:', error);
                alert('Error en la solicitud. Por favor, intente nuevamente.');
            }
        });
    }
}


// ============================================
// ELIMINAR VENTA Y RESTABLECER STOCK
// ============================================
function eliminarVenta(id) {
    if (!id) {
        Swal.fire('Error', 'No se puede eliminar la venta. ID no válido.', 'error');
        return;
    }
    
    Swal.fire({
        title: '¿Eliminar factura permanentemente?',
        html: '<div style="text-align: left;">' +
                '<p class="text-danger"><strong>⚠️ ¡ADVERTENCIA! ⚠️</strong></p>' +
                '<p>Esta acción eliminará completamente la factura del sistema y:</p>' +
                '<ul style="margin-top: 10px; text-align: left;">' +
                    '<li>✅ Restablecerá el stock de todos los productos</li>' +
                    '<li>🗑️ Eliminará permanentemente el registro de la venta</li>' +
                    '<li>📝 Eliminará todos los detalles de la venta</li>' +
                '</ul>' +
                '<p><strong>¿Está completamente seguro de continuar?</strong></p>' +
            '</div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then(function(result) {  // ← CAMBIO AQUÍ: arrow function a function tradicional
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando venta...',
                text: 'Restableciendo stock y eliminando registros',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });
            
            var url = '/historial-ventas/eliminar/' + id;
            var token = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: token
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '¡Eliminada!',
                            html: '<p class="text-success">✓ Venta eliminada correctamente</p>' +
                                '<p>Stock restablecido para <strong>' + (response.productos_restablecidos || 0) + '</strong> productos</p>',
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            // Recargar la tabla
                            if (typeof tablaVentas !== 'undefined' && tablaVentas) {
                                tablaVentas.ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                            
                            // Si el modal de detalle está abierto, cerrarlo
                            $('#modalDetalleVenta').modal('hide');
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'No se pudo eliminar la venta',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar venta:', error);
                    console.error('Respuesta completa:', xhr);
                    
                    var mensaje = 'Error en la solicitud. Por favor, intente nuevamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        mensaje = 'La venta no existe o ya fue eliminada.';
                    } else if (xhr.status === 500) {
                        mensaje = 'Error en el servidor. Contacte al administrador.';
                    } else if (xhr.status === 419) {
                        mensaje = 'Sesión expirada. Por favor, recargue la página.';
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: mensaje,
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}

// ============================================
// INICIALIZACIÓN DATATABLE
// ============================================
jQuery(document).ready(function($) {
    console.log('Inicializando sistema de ventas...');
    
    // Inicializar DataTable
    tablaVentas = $('#tablaVentas').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ route('historial.ventas.data') }}",
            "type": "GET",
            "data": function(d) {
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
                d.estado = $('#estado_venta').val();
                d.metodo_pago = $('#metodo_pago').val();
                d.cliente = $('#buscar_cliente').val();
                d.factura = $('#buscar_factura').val();
                return d;
            }
        },
        "columns": [
            { 
                "data": "numero_factura",
                "name": "ventas.numero_factura",
                "defaultContent": "N/A"
            },
            { 
                "data": "fecha_venta",
                "name": "ventas.fecha_venta",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return row.fecha_formateada + '<br><small class="text-muted">' + row.hora_formateada + '</small>';
                    }
                    return data;
                }
            },
            { 
                "data": "cliente_nombre",
                "name": "cliente_nombre"
            },
            { 
                "data": "vendedor_nombre",
                "name": "vendedor_nombre"
            },
            { 
                "data": "total_productos",
                "name": "total_productos",
                "className": "text-center"
            },
            { 
                "data": "total",
                "name": "ventas.total",
                "className": "text-right",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<span data-total="' + data + '">' + data + '</span>';
                    }
                    return data;
                }
            },
            { 
                "data": "estado",
                "name": "ventas.estado"
            },
            { 
                "data": "metodo_pago",
                "name": "ventas.metodo_pago"
            },
            { 
                "data": "acciones",
                "name": "acciones",
                "orderable": false,
                "searchable": false
            }
        ],
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "Sin resultados encontrados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "pageLength": 10,
        "drawCallback": function(settings) {
            var api = this.api();
            var total = 0;
            
            api.rows({page: 'current'}).every(function() {
                var data = this.data();
                var valorTotal = 0;
                
                if (data.total) {
                    if (typeof data.total === 'string') {
                        var valorLimpio = data.total.replace(/[\$\.]/g, '').replace(',', '.');
                        valorTotal = parseFloat(valorLimpio);
                    } else {
                        valorTotal = parseFloat(data.total);
                    }
                }
                
                if (!isNaN(valorTotal)) {
                    total += valorTotal;
                }
            });
            
            if ($('#totalGeneral').length) {
                $('#totalGeneral').text('$' + total.toLocaleString('es-CO', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));
            }
        }
    });
    
    // Eventos de filtros
    $('#fecha_desde, #fecha_hasta, #estado_venta, #metodo_pago').on('change', function() {
        tablaVentas.ajax.reload();
    });
    
    $('#buscar_cliente, #buscar_factura').on('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() {
            tablaVentas.ajax.reload();
        }, 500);
    });
    
    $('#btnFiltrar').on('click', function() {
        tablaVentas.ajax.reload();
    });
    
    $('#btnLimpiarFiltros').on('click', function() {
        $('#fecha_desde').val('');
        $('#fecha_hasta').val('');
        $('#estado_venta').val('');
        $('#metodo_pago').val('');
        $('#buscar_cliente').val('');
        $('#buscar_factura').val('');
        tablaVentas.ajax.reload();
    });
    
    // Reportes
   // ============================================
// EXPORTAR A EXCEL (NUEVO CÓDIGO)
// ============================================
// Reportes
$('#descargarReporte').on('click', function(e) {
    e.preventDefault();
    
    // 1. Obtener los valores de los filtros actuales
    var params = new URLSearchParams({
        fecha_desde: $('#fecha_desde').val(),
        fecha_hasta: $('#fecha_hasta').val(),
        estado: $('#estado_venta').val(),
        metodo_pago: $('#metodo_pago').val(),
        cliente: $('#buscar_cliente').val(),
        factura: $('#buscar_factura').val()
    });

    // 2. Mostrar loading
    Swal.fire({
        title: 'Generando reporte...',
        text: 'Por favor, espera.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // 3. Hacer la petición AJAX para obtener el archivo
    $.ajax({
        url: "{{ route('historial.ventas.exportar.excel') }}?" + params.toString(),
        type: 'GET',
        xhrFields: {
            responseType: 'blob' // Importante: esperar un blob (archivo binario)
        },
        success: function(data, status, xhr) {
            Swal.close();
            
            // Obtener el nombre del archivo del header Content-Disposition
            var filename = '';
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Si no se pudo obtener el nombre, usar uno por defecto
            if (!filename) {
                filename = 'ventas_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.xlsx';
            }
            
            // Crear URL del blob y forzar la descarga
            var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            
            // Limpiar
            window.URL.revokeObjectURL(link.href);
        },
        error: function(xhr, status, error) {
            Swal.close();
            
            var errorMsg = 'Error al generar el reporte';
            try {
                var response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch(e) {
                errorMsg = xhr.responseText || errorMsg;
            }
            
            Swal.fire({
                title: 'Error',
                text: errorMsg,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    });
});
    
    $('#btnGenerarReporte').on('click', function() {
        var params = new URLSearchParams({
            formato: $('#formatoReporte').val(),
            fecha_inicio: $('#fechaInicioReporte').val(),
            fecha_fin: $('#fechaFinReporte').val(),
            detalles: $('#incluirDetalles').is(':checked') ? 1 : 0,
            totales: $('#incluirTotales').is(':checked') ? 1 : 0,
            estado: $('#estado_venta').val(),
            metodo_pago: $('#metodo_pago').val()
        });
        
        window.open("{{ route('ventas.reporte') }}?" + params.toString(), '_blank');
        $('#modalReporte').modal('hide');
    });
});

</script>
@endpush