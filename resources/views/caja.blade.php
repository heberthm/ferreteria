@extends('layouts.app')
@section('title', 'Gestión de Caja Menor')

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
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Movimientos de Caja</h3>
            <div class="card-tools">
                <button class="btn btn-sm btn-default" onclick="cargarMovimientos()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="movimientos-container">
                <!-- Los movimientos se cargan via AJAX -->
            </div>
        </div>
    </div>

    <!-- Modal Abrir Caja -->
    <div class="modal fade" id="modalAbrirCaja">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Abrir Caja Menor</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formAbrirCaja">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="monto_inicial">Monto Inicial:</label>
                            <input type="number" step="0.01" class="form-control" id="monto_inicial" name="monto_inicial" required>
                        </div>
                        <div class="form-group">
                            <label for="observaciones">Observaciones:</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Abrir Caja</button>
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
                    <h4 class="modal-title">Cerrar Caja Menor</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formCerrarCaja">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Saldo actual:</strong> $<span id="saldo-cierre">{{ number_format($cajaActual->monto_actual ?? 0, 2) }}</span>
                        </div>
                        <div class="form-group">
                            <label for="observaciones_cierre">Observaciones:</label>
                            <textarea class="form-control" id="observaciones_cierre" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal Registrar Movimiento -->
<div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt"></i> Registrar Movimiento
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formMovimiento">
                    <div class="form-group">
                        <label for="tipo" class="font-weight-bold">Tipo de Movimiento *</label>
                        <select class="form-control select2" id="tipo" name="tipo" style="width: 100%;">
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
                                step="0.01" min="0.01" placeholder="0.00"
                                oninput="validarMonto(this)">
                        </div>
                        <div class="invalid-feedback">El monto debe ser mayor a 0</div>
                        <small class="form-text text-muted">Ej: 150.50, 75.00, 2000.00</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="concepto" class="font-weight-bold">Concepto *</label>
                        <input type="text" class="form-control" id="concepto" name="concepto" 
                               placeholder="Descripción breve del movimiento">
                        <div class="invalid-feedback">Ingrese un concepto para el movimiento</div>
                        <small class="form-text text-muted">Ej: Venta de tornillos, Compra de material, etc.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion" class="font-weight-bold">Descripción (Opcional)</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3" placeholder="Detalles adicionales del movimiento..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnRegistrarMovimiento" onclick="registrarMovimiento()">
                    <i class="fas fa-save"></i> Registrar Movimiento
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Reporte -->
    <div class="modal fade" id="modalReporte">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Generar Reporte</h4>
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
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
    <style>
        .movimiento-ingreso { border-left: 4px solid #28a745; }
        .movimiento-egreso { border-left: 4px solid #dc3545; }
    </style>
@stop

@section('js')

<script>


$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

function cargarMovimientos() {
    console.log('Cargando movimientos...');
    
    $('#movimientos-container').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-2">Cargando movimientos...</p>
        </div>
    `);

    // Datos de caja desde PHP
    const cajaData = @json($cajaActual ? ['id' => $cajaActual->id_caja, 'abierta' => true] : ['abierta' => false]);
    
    console.log('Datos de caja:', cajaData);
    
    if (!cajaData.abierta || !cajaData.id) {
        $('#movimientos-container').html(`
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No hay caja abierta. Abra una caja para ver los movimientos.
            </div>
        `);
        return;
    }

    const url = '/Obtener_movimientos/' + cajaData.id;
    console.log('URL de movimientos:', url);
    
    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            console.log('Respuesta de movimientos:', response);
            
            if (response.success) {
                if (response.movimientos && response.movimientos.length > 0) {
                    $('#movimientos-container').html(generarHTMLMovimientos(response.movimientos));
                } else {
                    $('#movimientos-container').html(`
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No hay movimientos registrados en esta caja.
                        </div>
                    `);
                }
            } else {
                $('#movimientos-container').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        ${response.message || 'Error al cargar movimientos'}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error completo:', {xhr: xhr, status: status, error: error});
            
            let errorDetail = 'Error desconocido';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorDetail = xhr.responseJSON.message;
            }
            
            $('#movimientos-container').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error al cargar los movimientos.
                    <br><small>Detalle: ${errorDetail}</small>
                    <br><small>Status: ${xhr.status} - ${xhr.statusText}</small>
                </div>
            `);
        }
    });
}

// Función para registrar movimiento - URL CORREGIDA
function registrarMovimiento() {
    console.log('=== INICIANDO REGISTRO DE MOVIMIENTO ===');
    
    // Obtener valores directamente
    const tipo = $('#tipo').val();
    const monto = $('#monto').val();
    const concepto = $('#concepto').val().trim();
    const descripcion = $('#descripcion').val().trim();
    
    console.log('Valores obtenidos:', { tipo, monto, concepto, descripcion });
    
    // Validación frontend
    let errors = [];
    
    if (!tipo) {
        $('#tipo').addClass('is-invalid');
        errors.push('El tipo de movimiento es obligatorio');
    } else {
        $('#tipo').removeClass('is-invalid').addClass('is-valid');
    }
    
    if (!monto || parseFloat(monto) <= 0) {
        $('#monto').addClass('is-invalid');
        errors.push('El monto debe ser mayor a 0');
    } else {
        $('#monto').removeClass('is-invalid').addClass('is-valid');
    }
    
    if (!concepto) {
        $('#concepto').addClass('is-invalid');
        errors.push('El concepto es obligatorio');
    } else {
        $('#concepto').removeClass('is-invalid').addClass('is-valid');
    }
    
    if (errors.length > 0) {
        Swal.fire('Error', errors.join('\n'), 'error');
        return;
    }
    
    // Preparar datos para enviar
    const datos = {
        _token: '{{ csrf_token() }}',
        tipo: tipo,
        monto: parseFloat(monto),
        concepto: concepto,
        descripcion: descripcion
    };
    
    console.log('Datos a enviar al servidor:', datos);
    
    // Mostrar loading
    const btn = $('#btnRegistrarMovimiento');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');
    
    // URL CORREGIDA para registrar movimiento
    const urlRegistrar = '/movimiento_caja';
    console.log('URL para registrar:', urlRegistrar);
    
    // Enviar petición
    $.ajax({
        url: urlRegistrar,
        type: 'POST',
        data: datos,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            if (response.success) {
                // Cerrar modal y resetear
                $('#modalMovimiento').modal('hide');
                $('#formMovimiento')[0].reset();
                $('#formMovimiento').find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Actualizar la interfaz
                actualizarInterfazDespuesMovimiento(response.nuevo_saldo);
            }
        },
        error: function(xhr) {
            console.error('Error en la petición:', xhr);
            
            let errorMessage = 'Error al registrar movimiento';
            
            if (xhr.responseJSON) {
                console.log('Respuesta de error:', xhr.responseJSON);
                
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    
                    $('#formMovimiento').find('.is-invalid').removeClass('is-invalid');
                    
                    $.each(errors, function(field, messages) {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        errorMessages.push(messages[0]);
                    });
                    
                    errorMessage = errorMessages.join('\n');
                }
            }
            
            Swal.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Registrar Movimiento');
        }
    });
}

function generarHTMLMovimientos(movimientos) {
    console.log('Generando HTML para movimientos:', movimientos);
    
    if (!movimientos || movimientos.length === 0) {
        return `
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i>
                No hay movimientos registrados en esta caja.
            </div>
        `;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    movimientos.forEach((mov) => {
        console.log('Procesando movimiento:', mov);
        
        const fecha = mov.created_at ? new Date(mov.created_at).toLocaleString('es-ES') : 'N/A';
        const tipoBadge = mov.tipo === 'ingreso' 
            ? '<span class="badge badge-success"><i class="fas fa-arrow-down"></i> INGRESO</span>'
            : '<span class="badge badge-danger"><i class="fas fa-arrow-up"></i> EGRESO</span>';
        
        const montoFormateado = mov.monto ? parseFloat(mov.monto).toFixed(2) : '0.00';
        const usuarioNombre = mov.usuario ? mov.usuario.name : (mov.userId || 'Sistema');
        const concepto = mov.concepto || 'Sin concepto';
        const descripcion = mov.descripcion || '';
        
        html += `
            <tr class="movimiento-${mov.tipo}">
                <td>
                    <small class="text-muted">${fecha}</small>
                </td>
                <td>${tipoBadge}</td>
                <td>
                    <div class="font-weight-bold">${concepto}</div>
                    ${descripcion ? `<small class="text-muted">${descripcion}</small>` : ''}
                </td>
                <td class="font-weight-bold ${mov.tipo === 'ingreso' ? 'text-success' : 'text-danger'}">
                    ${mov.tipo === 'ingreso' ? '+' : '-'} $${montoFormateado}
                </td>
                <td>
                    <small>${usuarioNombre}</small>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mostrando ${movimientos.length} movimiento(s)
            </small>
        </div>
    `;
    
    return html;
}


// Función para actualizar interfaz después de movimiento
function actualizarInterfazDespuesMovimiento(nuevoSaldo) {
    // Actualizar saldo
    $('#saldo-actual').text('$' + parseFloat(nuevoSaldo).toFixed(2));
    $('#saldo-cierre').text(parseFloat(nuevoSaldo).toFixed(2));
    
    // Recargar movimientos
    cargarMovimientos();
}

// Funciones de apertura/cierre - URLs CORREGIDAS
function abrirCaja() {
    const formData = $('#formAbrirCaja').serialize();
    const urlAbrir = 'abrir_caja';
    
    $.post(urlAbrir, formData, function(response) {
        if (response.success) {
            $('#modalAbrirCaja').modal('hide');
            Swal.fire('Éxito', response.message, 'success');
            location.reload();
        }
    }).fail(function(xhr) {
        Swal.fire('Error', xhr.responseJSON.message, 'error');
    });
}

function cerrarCaja() {
    const formData = $('#formCerrarCaja').serialize();
    const urlCerrar = 'cerrar_caja';
    
    $.post(urlCerrar, formData, function(response) {
        if (response.success) {
            detenerContador();
            $('#modalCerrarCaja').modal('hide');
            Swal.fire('Éxito', response.message, 'success');
            location.reload();
        }
    }).fail(function(xhr) {
        Swal.fire('Error', xhr.responseJSON.message, 'error');
    });
}

// Funciones de reportes - URL CORREGIDA
function generarReporte() {
    const formData = $('#formReporte').serialize();
    const urlReporte = 'reporte_caja';
    
    $.post(urlReporte, formData, function(response) {
        mostrarReporte(response);
        $('#btnImprimir').show();
    }).fail(function(xhr) {
        Swal.fire('Error', xhr.responseJSON.message, 'error');
    });
}

function mostrarReporte(data) {
    const resumen = data.resumen;
    let html = `
        <div class="reporte-contenido mt-4">
            <h4>Resumen del Reporte</h4>
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
                            <h3>$${resumen.total_ingresos.toFixed(2)}</h3>
                            <p>Total Ingresos</p>
                        </div>
                        <div class="icon"><i class="fas fa-arrow-up"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>$${resumen.total_egresos.toFixed(2)}</h3>
                            <p>Total Egresos</p>
                        </div>
                        <div class="icon"><i class="fas fa-arrow-down"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>$${resumen.saldo_final.toFixed(2)}</h3>
                            <p>Saldo Final</p>
                        </div>
                        <div class="icon"><i class="fas fa-balance-scale"></i></div>
                    </div>
                </div>
            </div>
    `;
    
    if (data.cajas && data.cajas.length > 0) {
        data.cajas.forEach(caja => {
            let duracion = 'No disponible';
            if (caja.fecha_apertura && caja.fecha_cierre) {
                const fechaApertura = new Date(caja.fecha_apertura);
                const fechaCierre = new Date(caja.fecha_cierre);
                const diffMs = fechaCierre - fechaApertura;
                const diffSegundos = Math.floor(diffMs / 1000);
                duracion = calcularDuracion(diffSegundos);
            }
            
            html += `
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Caja del ${new Date(caja.fecha_apertura).toLocaleDateString('es-ES')}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Estado:</strong> ${caja.estado.toUpperCase()}</p>
                                <p><strong>Monto Inicial:</strong> $${parseFloat(caja.monto_inicial).toFixed(2)}</p>
                                <p><strong>Monto Final:</strong> $${parseFloat(caja.monto_actual).toFixed(2)}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Duración:</strong> ${duracion}</p>
                                <p><strong>Abierta por:</strong> ${caja.usuario_apertura.name}</p>
                                <p><strong>Cerrada por:</strong> ${caja.usuario_cierre ? caja.usuario_cierre.name : 'N/A'}</p>
                            </div>
                        </div>
                        
                        <h6>Movimientos (${caja.movimientos ? caja.movimientos.length : 0}):</h6>
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
            
            if (caja.movimientos && caja.movimientos.length > 0) {
                caja.movimientos.forEach(mov => {
                    html += `
                        <tr class="${mov.tipo === 'ingreso' ? 'table-success' : 'table-danger'}">
                            <td>${new Date(mov.created_at).toLocaleString('es-ES')}</td>
                            <td>${mov.tipo.toUpperCase()}</td>
                            <td>${mov.concepto}</td>
                            <td>$${parseFloat(mov.monto).toFixed(2)}</td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hay movimientos</td>
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
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i>
                No hay cajas registradas para el período seleccionado.
            </div>
        `;
    }
    
    html += '</div>';
    $('#reporte-resultado').html(html).show();
}

function imprimirReporte() {
    const contenido = $('.reporte-contenido').html();
    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <html>
            <head>
                <title>Reporte de Caja Menor - Ferretería</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        .no-print { display: none !important; }
                        body { font-size: 12px; }
                        .table { font-size: 11px; }
                    }
                    body { font-family: Arial, sans-serif; }
                    .small-box { margin-bottom: 10px; }
                </style>
            </head>
            <body>
                <div class="container mt-4">
                    <h2 class="text-center mb-4">Reporte de Caja Menor - Ferretería</h2>
                    ${contenido}
                </div>
                <div class="text-center mt-4 no-print">
                    <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
                    <button onclick="window.close()" class="btn btn-secondary">Cerrar</button>
                </div>
            </body>
        </html>
    `);
    ventana.document.close();
}

// ========== INICIALIZACIÓN CUANDO EL DOCUMENTO ESTÁ LISTO ==========

$(document).ready(function() {
    console.log('Documento listo - Inicializando caja menor');
    
    // Cargar movimientos iniciales
    cargarMovimientos();
            
    // Event handlers para formularios
    $('#formAbrirCaja').on('submit', function(e) {
        e.preventDefault();
        abrirCaja();
    });
    
    $('#formCerrarCaja').on('submit', function(e) {
        e.preventDefault();
        cerrarCaja();
    });
    
    $('#formReporte').on('submit', function(e) {
        e.preventDefault();
        generarReporte();
    });
    
    // Validación en tiempo real para movimiento
    $('#tipo').on('change', function() {
        const valor = $(this).val();
        if (valor) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });
    
    $('#concepto').on('input', function() {
        const valor = $(this).val().trim();
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

// Limpiar intervalo cuando la página se cierre
$(window).on('beforeunload', function() {
    detenerContador();
});

console.log('Script de caja menor cargado correctamente');
</script>


@stop