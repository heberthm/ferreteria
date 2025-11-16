@extends('layouts.app')
@section('title', 'Caja Menor')

@section('content')
<br>
    <!-- Estado de Caja -->
    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-cash-register"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Estado Caja</span>
                    <span class="info-box-number" id="estado-caja">
                        @if($cajaActual)
                            <span class="badge badge-success">ABIERTA</span>
                        @else
                            <span class="badge badge-danger">CERRADA</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Saldo Actual</span>
                    <span class="info-box-number" id="saldo-actual">
                        ${{ number_format($cajaActual->monto_actual ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Última Apertura</span>
                    <span class="info-box-number" id="fecha-apertura">
                        @if($cajaActual)
                            {{ $cajaActual->fecha_apertura->format('d/m/Y H:i') }}
                        @else
                            No disponible
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mb-4">
        <div class="col-12">
            @if(!$cajaActual)
                <button class="btn btn-success" data-toggle="modal" data-target="#modalAbrirCaja">
                    <i class="fas fa-lock-open"></i> Abrir Caja
                </button>
            @else
                <button class="btn btn-danger" data-toggle="modal" data-target="#modalCerrarCaja">
                    <i class="fas fa-lock"></i> Cerrar Caja
                </button>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalMovimiento">
                    <i class="fas fa-exchange-alt"></i> Registrar Movimiento
                </button>
            @endif
            <button class="btn btn-info" data-toggle="modal" data-target="#modalReporte">
                <i class="fas fa-chart-bar"></i> Generar Reporte
            </button>
        </div>
    </div>

   
 <!-- Lista de Movimientos -->
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-exchange-alt"></i> Movimientos de Caja
            </h5>
        </div>
        <div class="card-body">
            <!-- Tabla CON ESTRUCTURA COMPLETA -->
            <div class="table-responsive">
                <table id="tablaMovimientos" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Monto</th>                             
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables llenará esto automáticamente -->
                    </tbody>
                </table>
            </div>
        </div> <!-- Cierre de card-body -->
    </div> <!-- Cierre de card -->
</div> <!-- Cierre de container-fluid -->


 <!-- Modal Abrir Caja -->
<div class="modal fade" id="modalAbrirCaja">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Abrir Caja Menor</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formAbrirCaja">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="monto_inicial">Monto Inicial:</label>
                        <input type="number" class="form-control" id="monto_inicial" name="monto_inicial" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="observaciones_apertura">Observaciones:</label>
                        <textarea class="form-control" id="observaciones_apertura" name="observaciones" rows="3" placeholder="Observaciones de apertura..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnAbrirCaja">
                        <i class="fas fa-lock-open"></i> Abrir Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Movimiento - VERSIÓN CORREGIDA -->

<div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt"></i> Registrar Movimiento de Caja
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formMovimiento" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tipo" class="font-weight-bold">Tipo de Movimiento *</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="">-- Seleccione el tipo --</option>
                            <option value="ingreso">💰 Ingreso (Entrada de dinero)</option>
                            <option value="egreso">💸 Egreso (Salida de dinero)</option>
                        </select>
                        <div class="invalid-feedback">Seleccione el tipo de movimiento</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="monto" class="font-weight-bold">Monto *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="monto" name="monto" 
                                step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div class="invalid-feedback">El monto debe ser mayor a 0</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="concepto" class="font-weight-bold">Concepto *</label>
                        <input type="text" class="form-control" id="concepto" name="concepto" 
                               placeholder="Descripción breve del movimiento" required>
                        <div class="invalid-feedback">Ingrese un concepto para el movimiento</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion" class="font-weight-bold">Descripción (Opcional)</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3" placeholder="Detalles adicionales del movimiento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnRegistrarMovimiento">
                        <i class="fas fa-save"></i> Registrar Movimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cerrar Caja -->

<div class="modal fade" id="modalCerrarCaja">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title bg-ligth">Cerrar Caja Menor</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="formCerrarCaja" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (opcional):</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Saldo final en caja:</label>
                        
                        {{-- VERIFICACIÓN SEGURA --}}
                        @php
                            $monto_actual = $cajaActual->monto_actual ?? 0;
                            $hayCajaAbierta = !empty($cajaActual);
                        @endphp
                        
                        <h4 id="saldo-cierre" class="text-{{ $hayCajaAbierta ? 'success' : 'danger' }}">
                            ${{ number_format($monto_actual, 2) }}
                        </h4>
                        
                        <input type="hidden" name="saldo_final" id="saldo_final" value="{{ $monto_actual }}">
                        
                        @if(!$hayCajaAbierta)
                            <div class="alert alert-warning mt-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                No hay caja abierta actualmente
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnCerrarCaja" class="btn btn-danger" 
                            {{ !$hayCajaAbierta ? 'disabled' : '' }}>
                        <i class="fas fa-lock"></i> Cerrar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug información
    console.log('🔧 Caja actual:', @json($cajaActual));
    console.log('💰 Monto actual:', {{ $monto_actual }});
    console.log('📦 Hay caja abierta:', {{ $hayCajaAbierta ? 'true' : 'false' }});
});



</script>

    <!-- Modal Reporte -->
    <div class="modal fade" id="modalReporte">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title >Generar Reporte</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formReporte">
                 @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_reporte">Tipo de Reporte:</label>
                                    <select class="form-control" id="tipo_reporte" name="tipo" required>
                                        <option value="diario">Diario</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="mensual">Mensual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_reporte">Fecha:</label>
                                    <input type="date" class="form-control" id="fecha_reporte" name="fecha" required>
                                </div>
                            </div>
                        </div>
                        <div id="reporte-resultado" style="display: none;">
                            <!-- Aquí se mostrará el reporte -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-info">Generar Reporte</button>
                        <button type="button" class="btn btn-success" onclick="imprimirReporte()" style="display: none;" id="btnImprimir">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')



<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">

    <style>
    .movimiento-ingreso { border-left: 4px solid #28a745; }
    .movimiento-egreso { border-left: 4px solid #dc3545; }

       .badge { font-size: 0.85em; }
       .table-actions { min-width: 120px; }
       .ingreso { background-color: #d4edda !important; }
       .egreso { background-color: #f8d7da !important; }

    /* Estilos para la paginación */
    .pagination {
        margin-bottom: 0;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .dataTable  {
     font-size: 0.8em; 
    }

    .page-link {
        color: #007bff;
        cursor: pointer;
    }

    .page-link:hover {
        color: #0056b3;
    }
</style>
@stop


@section('js')

<!-- Luego DataTables y dependencias -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>

<!-- Librerías específicas para cada tipo de exportación -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
// VERIFICACIÓN DE DEPENDENCIAS
console.log('jQuery cargado:', typeof jQuery !== 'undefined');
console.log('DataTables cargado:', typeof $.fn.DataTable !== 'undefined');


$(document).ready(function() {
    
    // Configurar CSRF Token globalmente
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (csrfToken) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        console.log('✅ CSRF Token configurado correctamente');
    } else {
        console.error('❌ No se encontró el token CSRF');
    }

    // Variable global para DataTable
    window.dataTableMovimientos = null;

    // Inicializar DataTable
    function inicializarDataTable() {
        try {
            console.log('🔄 Inicializando DataTable...');
            
            // VALIDACIÓN: Verificar que la tabla existe
            if ($('#tablaMovimientos').length === 0) {
                console.error('❌ Error: No se encontró la tabla #tablaMovimientos en el DOM');
                mostrarMensaje('error', 'Error: La tabla de movimientos no está disponible');
                return false;
            }

            // VALIDACIÓN: Verificar que la tabla tiene estructura correcta
            if ($('#tablaMovimientos thead').length === 0) {
                console.error('❌ Error: La tabla no tiene elemento <thead>');
                mostrarMensaje('error', 'Error: Estructura de tabla incorrecta');
                return false;
            }

            // Destruir instancia previa si existe
            if ($.fn.DataTable.isDataTable('#tablaMovimientos')) {
                $('#tablaMovimientos').DataTable().destroy();
                console.log('🗑️ Instancia anterior destruida');
            }

            // Inicializar DataTable
            window.dataTableMovimientos = $('#tablaMovimientos').DataTable({
                processing: true,
                serverSide: true,
                stateSave: false, // Desactivar guardado de estado para evitar conflictos

                ajax: {
                    url: "{{ route('movimientos.datatable') }}",
                    type: "POST",
                    error: function(xhr, error, thrown) {
                        console.error('❌ Error cargando datos:', {xhr, error, thrown});
                        mostrarMensaje('error', 'Error al cargar los datos. Por favor, recarga la página.');
                    }
                },
                columns: [
                    { data: 'id_movimiento_caja', name: 'id_movimiento_caja', orderable: true, searchable: true },
                    { data: 'fecha_formateada', name: 'created_at', orderable: true, searchable: true },
                    { data: 'descripcion', name: 'descripcion', searchable: true, searchable: true },
                    { data: 'tipo_badge', name: 'tipo', orderable: false, searchable: false },
                    { data: 'monto_formateado', name: 'monto' },
                    { data: 'usuario_nombre', name: 'usuario.name', orderable: true, searchable: true },                   
                ],

                // ✅ CONFIGURACIÓN DE ORDENACIÓN MEJORADA
                order: [[0, 'desc']], // Ordenar por ID descendente por defecto
                ordering: true, // ✅ Asegurar que la ordenación esté activada
                orderMulti: true, // Permitir multi-ordenación con Shift+click

                language: {                   
                   "emptyTable": "No hay movimientos registrados.",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
                    }
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                autoWidth: false,

                // ✅ CONFIGURACIÓN DOM
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

                // ✅ CONFIGURACIÓN DE BOTONES
                buttons: {
                    dom: {
                        button: {
                            className: 'btn btn-sm mx-1'
                        }
                    },
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i>',
                            className: 'btn-success',
                            title: 'Movimientos de Caja',
                            filename: 'movimientos_' + new Date().getTime() + '.xlsx',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                modifier: {
                                    page: 'all',
                                    search: 'applied'
                                }
                            },
                             titleAttr: 'Exportar a Excel',
                            
                            // ✅ Configuración adicional para Excel
                            sheetName: 'Movimientos caja',
                            header: true,
                            autoFilter: true,
                            customize: function (xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                // Personalizaciones opcionales
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf"></i>',
                            className: 'btn-danger',
                            title: function() {
                                return 'Movimientos de Caja - ' + new Date().toLocaleString('es-ES');
                            },
                            filename: function() {
                                return 'movimientos_caja' + new Date().getTime() + '.pdf';
                            },
                            orientation: 'portrait',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                modifier: {
                                    page: 'all'
                                }
                            },
                             download: 'open',
                             titleAttr: 'Exportar PDF',
                           
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i>',
                            className: 'btn-info',
                            title: 'Movimientos de Caja - ' + new Date().toLocaleString('es-ES'),
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                modifier: {
                                    page: 'all'
                                }
                            },
                             titleAttr: 'Imprimir listado',
                            
                            customize: function (win) {
                                $(win.document.body)
                                    .css('font-size', '10pt')
                                    .prepend(
                                        '<div style="text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">' +
                                        '<h2 style="margin: 0; color: #2c3e50;">Movimientos de Caja</h2>' +
                                        '<p style="margin: 5px 0; font-size: 12px;">Generado: ' + new Date().toLocaleString('es-ES') + '</p>' +
                                        '</div>'
                                    );
                                
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', '9pt');
                                
                                $(win.document.body).find('h1').remove();
                            }
                        },
                        {
                            extend: 'copy',
                            text: '<i class="fas fa-copy"></i>',
                            className: 'btn-secondary',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                modifier: {
                                    page: 'all'
                                }
                            },
                             titleAttr: 'Copiar listado',
                             message: 'Copiando listado...',
                            title: 'Movimientos de Caja - ' + new Date().toLocaleString('es-ES')
                        }
                    ]
                }, // ← Coma que cierra el objeto buttons

                // ✅ PROPIEDADES FINALES
                initComplete: function(settings, json) {
                    console.log('✅ DataTable inicializado correctamente');
                },
                drawCallback: function(settings) {
                    console.log('📊 Tabla dibujada');
                }
            }); // ← CIERRE CORRECTO DEL DataTable()

            return true;

        } catch (error) {
            console.error('❌ Error inicializando DataTable:', error);
            mostrarMensaje('error', 'Error al inicializar la tabla. Por favor, recarga la página.');
            return false;
        }
    }

    // ✅ INICIALIZAR LA TABLA
    inicializarDataTable();



    // Manejar envío del formulario de movimiento
    
   $(document).ready(function() {
    'use strict';
    
    console.log('🚀 INICIANDO SISTEMA CON CIERRE DE MODAL');
    
    let movimientoProcesando = false;
    const BOTON_HTML_INICIAL = '<i class="fas fa-save"></i> Registrar Movimiento';
    
    // ELIMINAR HANDLERS EXISTENTES
    $('#formMovimiento').off('submit');
    
    $('#formMovimiento').on('submit', function() {
        event.preventDefault();
        event.stopImmediatePropagation();
        
        if (movimientoProcesando) {
            console.log('🚫 Movimiento en proceso, ignorando...');
            return false;
        }
        
        movimientoProcesando = true;
        const $boton = $('#btnRegistrarMovimiento');
        const $form = $(this);
        
        console.log('💾 INICIANDO GUARDADO...');
        
        // ESTADO DE CARGA
        $boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        // ENVIAR DATOS
        $.ajax({
            url: "{{ route('movimiento_caja') }}",
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('✅ RESPUESTA EXITOSA');

                 window.location.reload();
                
                if (response.success) {
                    // **PROCESAR ÉXITO CON CIERRE DE MODAL**
                    procesarExitoConCierre(response, $form, $boton);
                } else {
                    procesarError('Error: ' + response.message, $boton);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ ERROR AJAX:', error);
                procesarError('Error de conexión al servidor', $boton);
            }
        });
        
        return false;
    });
    
    function procesarExitoConCierre(response, $form, $boton) {
        console.log('🎉 PROCESANDO ÉXITO Y CERRANDO MODAL');
        
        // **1. CERRAR EL MODAL INMEDIATAMENTE - MÉTODO PRINCIPAL**
        cerrarModalGarantizado();
        
        // **2. RESTAURAR BOTÓN DESPUÉS DE CERRAR**
        setTimeout(() => {
            $boton.prop('disabled', false).html(BOTON_HTML_INICIAL);
            movimientoProcesando = false;
            console.log('🔄 BOTÓN RESTAURADO');
        }, 500);
        
        // **3. LIMPIAR FORMULARIO**
        setTimeout(() => {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            console.log('🧹 FORMULARIO LIMPIADO');
        }, 400);
        
        // **4. RECARGAR TABLA**
        if (window.dataTableMovimientos) {
            setTimeout(() => {
                window.dataTableMovimientos.ajax.reload(null, false);
                console.log('📊 TABLA RECARGADA');
            }, 600);
        }
        
        // **5. MOSTRAR MENSAJE DE ÉXITO**
        setTimeout(() => {
            Swal.fire({
                title: '¡Éxito! ✅',
                text: response.message,
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#28a745'
            });
        }, 700);
    }
    
    function procesarError(mensaje, $boton) {
        console.error('💥 ERROR:', mensaje);
        
        // Restaurar botón en error
        $boton.prop('disabled', false).html(BOTON_HTML_INICIAL);
        movimientoProcesando = false;
        
        Swal.fire({
            title: 'Error',
            text: mensaje,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
    
    // **FUNCIÓN PARA CERRAR MODAL GARANTIZADO**
    function cerrarModalGarantizado() {
        console.log('🔒 INICIANDO CIERRE DE MODAL...');
        
        const modalId = 'modalMovimiento';
        const $modal = $('#' + modalId);
        
        // MÉTODO 1: Bootstrap modal hide
        $modal.modal('hide');
        console.log('✅ Método 1: Bootstrap hide ejecutado');
        
        // MÉTODO 2: Forzar cierre después de 100ms
        setTimeout(() => {
            // Remover backdrop
            $('.modal-backdrop').remove();
            
            // Remover clases de modal abierto
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            
            // Ocultar modal directamente
            $modal.css('display', 'none');
            $modal.removeClass('show');
            
            console.log('✅ Método 2: Cierre forzado ejecutado');
        }, 100);
        
        // MÉTODO 3: Verificación final
        setTimeout(() => {
            if ($modal.is(':visible')) {
                console.warn('⚠️ Modal aún visible, aplicando método de emergencia');
                // Método de emergencia
                $modal.hide();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            } else {
                console.log('✅ MODAL CERRADO CORRECTAMENTE');
            }
        }, 300);
    }


    // CONFIGURACIÓN ADICIONAL DEL MODAL
    $('#modalMovimiento').on('show.bs.modal', function() {
        console.log('🔧 MODAL ABIERTO - Inicializando estado');
        movimientoProcesando = false;
        $('#btnRegistrarMovimiento').prop('disabled', false).html(BOTON_HTML_INICIAL);
    });
    
    console.log('✅ SISTEMA CON CIERRE DE MODAL CONFIGURADO');
});


// ========== FUNCION ABRIR CAJA ===========
let procesandoApertura = false;

function abrirCaja() {
    if (procesandoApertura) {
        Swal.fire('Información', 'La apertura de caja ya está en proceso', 'info');
        return;
    }
    
    // Validar formulario
    const monto = $('#monto_inicial').val();
    if (!monto || monto <= 0) {
        Swal.fire('Error', 'Ingrese un monto inicial válido', 'error');
        return;
    }
    
    procesandoApertura = true;
    const formData = $('#formAbrirCaja').serialize();
    const urlAbrir = 'abrir_caja';
    
    // Deshabilitar botón
    $('#btnAbrirCaja').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
    
    $.post(urlAbrir, formData, function(response) {
        if (response.success) {
            $('#modalAbrirCaja').modal('hide');
            Swal.fire('Éxito', response.message, 'success');
            
            // Recargar después de breve delay
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    }).fail(function(xhr) {
        const errorMsg = xhr.responseJSON?.message || 'Error al abrir caja';
        Swal.fire('Error', errorMsg, 'error');
    }).always(function() {
        // Rehabilitar botón y variable de control
        procesandoApertura = false;
        $('#btnAbrirCaja').prop('disabled', false).html('<i class="fas fa-cash-register"></i> Abrir Caja');
    });
}

// Asegurar un solo event listener
$(document).ready(function() {
    $('#btnAbrirCaja').off('click').on('click', abrirCaja);
});




// ========== FUNCIÓN CERRAR CAJA  ==========

    if (window.cerrarCaja) {
        delete window.cerrarCaja;
        console.log('🗑️ Función cerrarCaja anterior eliminada');
    }


window.cerrarCaja = function(event) {
    console.log('🎯 CERRAR CAJA - Iniciando proceso...');
    
    if (event) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
    
    // 1. OBTENER ELEMENTOS
    const btn = document.getElementById('btnCerrarCaja');
    const form = document.getElementById('formCerrarCaja');
    const saldoFinalInput = document.getElementById('saldo_final');
    
    if (!btn || !form || !saldoFinalInput) {
        console.error('❌ Elementos críticos no encontrados');
        Swal.fire('Error', 'Error interno del sistema', 'error');
        return false;
    }
    
    // 2. VALIDAR SALDO ANTES DE ENVIAR
    const saldoFinal = parseFloat(saldoFinalInput.value);
    console.log('💰 Saldo final a enviar:', saldoFinal);
    
    if (!saldoFinal || saldoFinal <= 0 || isNaN(saldoFinal)) {
        console.error('❌ Saldo final inválido:', saldoFinal);
        Swal.fire({
            title: 'Error',
            text: `El saldo final (${saldoFinal}) no es válido. Debe ser mayor a cero.`,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        return false;
    }
    
    // 3. MOSTRAR SPINNER
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Cerrando caja...';
    
    // 4. PREPARAR DATOS
    const formData = new FormData(form);
    const datosFormulario = Object.fromEntries(formData);
    
    console.log('🔍 Datos del formulario:', datosFormulario);
    
    const urlCerrar = "{{ route('cerrar_caja') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        console.error('❌ Token CSRF no encontrado');
        Swal.fire('Error', 'Error de seguridad. Recarga la página.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        return false;
    }
    
    // 5. ENVIAR PETICIÓN
    fetch(urlCerrar, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        console.log('📨 Respuesta del servidor - Status:', response.status);
        
        const data = await response.json().catch(() => null);
        
        if (!response.ok) {
            if (response.status === 422 && data && data.errors) {
                const errores = Object.values(data.errors).flat().join(', ');
                throw new Error(errores);
            }
            throw new Error(data?.message || `Error ${response.status}`);
        }
        
        return data;
    })
    .then(data => {
        console.log('✅ Respuesta exitosa:', data);
        
        if (data.success) {
            $('#modalCerrarCaja').modal('hide');
            form.reset();
            
            Swal.fire({
                title: '¡Éxito! 🎉',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Error del servidor');
        }
    })
    .catch(error => {
        console.error('❌ Error en el cierre:', error);
        
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        
        Swal.fire({
            title: 'Error al Cerrar Caja',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    });
    
    return false;
};


// ========== EXPORTAR EXCEL ===========
function exportarExcel() {
    // Mostrar loading
    Swal.fire({
        title: 'Generando Excel',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Usar la ruta con nombre
    const url = '/caja-menor/exportar-excel';
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        return response.blob();
    })
    .then(blob => {
        // Crear URL temporal y descargar
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'caja-menor-' + new Date().toISOString().split('T')[0] + '.xlsx';
        
        document.body.appendChild(a);
        a.click();
        
        // Limpiar
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        Swal.close();
        
        // Opcional: mostrar mensaje de éxito
        Swal.fire('Éxito', 'Archivo Excel generado correctamente', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo generar el archivo Excel: ' + error.message, 'error');
    });
}

// Botón en tu HTML
$(document).ready(function() {
    $('#btnExportarExcel').on('click', function(e) {
        e.preventDefault();
        exportarExcel();
    });
});

    // En tu script principal, asegura este event handler
$(document).ready(function() {
    console.log('🔧 Configurando event handler para cerrar caja...');
    
    // ELIMINAR HANDLERS EXISTENTES
    $('#formCerrarCaja').off('submit');
    
    // AGREGAR NUEVO HANDLER QUE USA NUESTRA FUNCIÓN
    $('#formCerrarCaja').on('submit', function(e) {
        console.log('🎯 Formulario cerrar caja enviado - Ejecutando nuestra función');
        window.cerrarCaja(e);
    });
        
        // CONFIGURACIÓN DEL MODAL
        $('#modalMovimiento').on('show.bs.modal', function() {
            console.log('🔧 Modal movimiento abierto');
            movimientoProcesando = false;
            $('#formMovimiento')[0].reset();
            $('#formMovimiento').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('#btnRegistrarMovimiento').prop('disabled', false).html('<i class="fas fa-save"></i> Registrar Movimiento');
        });
        
        console.log('✅ Event handlers configurados correctamente');
    });

    // ========== FUNCIONES PARA REPORTES ==========

    $(document).ready(function() {
        // Establecer fecha actual por defecto
        $('#fecha_reporte').val(new Date().toISOString().split('T')[0]);
        
        // Evento submit del formulario de reporte
        $('#formReporte').on('submit', function(e) {
            e.preventDefault();
            generarReporte();
        });
        
        // Limpiar resultados cuando se cierre el modal
        $('#modalReporte').on('hidden.bs.modal', function() {
            $('#reporte-resultado').hide().empty();
            $('#btnImprimir').hide();
        });

        // Event handlers para formularios
        $('#formAbrirCaja').on('submit', function(e) {
            e.preventDefault();
            abrirCaja(); 
        });
        
        $('#formCerrarCaja').on('submit', function(e) {
            e.preventDefault();
            cerrarCaja();
        });
        
        // Validación en tiempo real para movimiento
        $('#tipo').on('change', function() {
            const valor = $(this).val();
            if (valor) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Limpiar formulario al abrir modal
        $('#modalMovimiento').on('show.bs.modal', function() {
            $('#formMovimiento')[0].reset();
            $('#formMovimiento').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        });
        
        // Permitir Enter para enviar
        $('#formMovimiento').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                registrarMovimiento();
            }
        });
        
        console.log('Inicialización completada');
    });

    function generarReporte() {
        const btn = $('#formReporte button[type="submit"]');
        const originalText = btn.html();
        
        // Mostrar estado de procesamiento
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');
        
        const formData = $('#formReporte').serialize();
        const urlReporte = "{{ route('reporte_caja') }}";
        
        $.post(urlReporte, formData)
        .done(function(response) {
            if (response.success) {
                mostrarReporte(response);
                $('#btnImprimir').show();
            } else {
                Swal.fire('Info', response.message || 'No hay datos para mostrar', 'info');
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'Error al generar el reporte';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMessage, 'error');
        })
        .always(function() {
            btn.prop('disabled', false).html('Generar Reporte');
        });
    }

    // Función para mostrar el reporte en el modal
    function mostrarReporte(data) {
        console.log('Datos recibidos:', data); // Para debug
        
        const resumen = data.resumen || {
            total_cajas: 0,
            total_ingresos: 0,
            total_egresos: 0,
            saldo_final: 0
        };
        
        let html = `
            <div class="reporte-contenido mt-4">
                <h4>Resumen del Reporte - ${data.periodo?.descripcion || 'Período seleccionado'}</h4>
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>${resumen.total_cajas}</h3>
                                <p>Cajas Procesadas</p>
                            </div>
                            <div class="icon"><i class="fas fa-cash-register"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>$${parseFloat(resumen.total_ingresos).toFixed(2)}</h3>
                                <p>Total Ingresos</p>
                            </div>
                            <div class="icon"><i class="fas fa-arrow-up"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>$${parseFloat(resumen.total_egresos).toFixed(2)}</h3>
                                <p>Total Egresos</p>
                            </div>
                            <div class="icon"><i class="fas fa-arrow-down"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>$${parseFloat(resumen.saldo_final).toFixed(2)}</h3>
                                <p>Saldo Final</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale"></i></div>
                        </div>
                    </div>
                </div>
        `;
        
        // Verificar si hay cajas para mostrar
        if (data.cajas && data.cajas.length > 0) {
            data.cajas.forEach(function(caja) {
                let duracion = 'No disponible';
                if (caja.fecha_apertura && caja.fecha_cierre) {
                    const fechaApertura = new Date(caja.fecha_apertura);
                    const fechaCierre = new Date(caja.fecha_cierre);
                    const diffMs = fechaCierre - fechaApertura;
                    const diffSegundos = Math.floor(diffMs / 1000);
                    duracion = calcularDuracion(diffSegundos);
                }
                
                // Asegurar que los datos existan
                const usuarioApertura = caja.usuario_apertura || { name: 'N/A' };
                const usuarioCierre = caja.usuario_cierre || { name: 'N/A' };
                const movimientos = caja.movimientos || [];
                
                html += `
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Caja del ${new Date(caja.fecha_apertura).toLocaleDateString('es-ES')}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Estado:</strong> <span class="badge badge-${caja.estado === 'abierta' ? 'success' : 'secondary'}">${caja.estado?.toUpperCase() || 'N/A'}</span></p>
                                    <p><strong>Monto Inicial:</strong> $${parseFloat(caja.monto_inicial || 0).toFixed(2)}</p>
                                    <p><strong>Monto Final:</strong> $${parseFloat(caja.monto_actual || 0).toFixed(2)}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Duración:</strong> ${duracion}</p>
                                    <p><strong>Abierta por:</strong> ${usuarioApertura.name}</p>
                                    <p><strong>Cerrada por:</strong> ${caja.fecha_cierre ? usuarioCierre.name : 'N/A'}</p>
                                </div>
                            </div>
                            
                            <h6>Movimientos (${movimientos.length}):</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fecha/Hora</th>
                                            <th>Tipo</th>
                                            <th>Concepto</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;
                
                if (movimientos.length > 0) {
                    movimientos.forEach(function(mov) {
                        html += `
                            <tr class="${mov.tipo === 'ingreso' ? 'table-success' : 'table-danger'}">
                                <td>${new Date(mov.created_at).toLocaleString('es-ES')}</td>
                                <td><span class="badge badge-${mov.tipo === 'ingreso' ? 'success' : 'danger'}">${mov.tipo?.toUpperCase() || 'N/A'}</span></td>
                                <td>${mov.concepto || 'Sin concepto'}</td>
                                <td>$${parseFloat(mov.monto || 0).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay movimientos registrados</td>
                        </tr>
                    `;
                }
                
                html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="alert alert-info text-center mt-3">
                    <i class="fas fa-info-circle"></i>
                    No hay cajas registradas para el período seleccionado.
                </div>
            `;
        }
        
        html += '</div>';
        $('#reporte-resultado').html(html).show();
    }

    // Función auxiliar para calcular duración
    function calcularDuracion(segundos) {
        if (!segundos || segundos <= 0) return 'No disponible';
        
        const horas = Math.floor(segundos / 3600);
        const minutos = Math.floor((segundos % 3600) / 60);
        
        if (horas > 0) {
            return `${horas}h ${minutos}m`;
        } else {
            return `${minutos}m`;
        }
    }

    // Función para imprimir el reporte
    function imprimirReporte() {
        const contenido = $('.reporte-contenido').html();
        if (!contenido) {
            Swal.fire('Info', 'No hay reporte para imprimir', 'info');
            return;
        }
        
        const ventana = window.open('', '_blank', 'width=1000,height=600');
        ventana.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Reporte de Caja Menor</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                    <style>
                        @media print {
                            .no-print { display: none !important; }
                            body { font-size: 12px; margin: 0; padding: 20px; }
                            .table { font-size: 11px; }
                            .small-box { margin-bottom: 10px; break-inside: avoid; }
                            .card { break-inside: avoid; margin-bottom: 15px; }
                            h4 { color: #333; }
                        }
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .small-box { border-radius: 5px; padding: 15px; margin-bottom: 15px; color: white; }
                        .small-box .inner h3 { margin: 0 0 10px 0; font-size: 1.5em; }
                        .small-box .icon { font-size: 2em; opacity: 0.3; float: right; }
                        .bg-info { background-color: #17a2b8 !important; }
                        .bg-success { background-color: #28a745 !important; }
                        .bg-danger { background-color: #dc3545 !important; }
                        .bg-warning { background-color: #ffc107 !important; color: #333 !important; }
                    </style>
                </head>
                <body>
                    <div class="container-fluid">
                        <h2 class="text-center mb-4">Reporte de Caja Menor</h2>
                        <p class="text-center text-muted">Generado el: ${new Date().toLocaleDateString('es-ES')}</p>
                        ${contenido}
                    </div>
                    <div class="text-center mt-4 no-print">
                        <button onclick="window.print()" class="btn btn-primary me-2">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <button onclick="window.close()" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </body>
            </html>
        `);
        ventana.document.close();
    }

    console.log('Script de caja menor cargado correctamente');
})();

</script>


@stop