@extends('layouts.app')

@section('title', 'Gestión de Caja Diaria')

@section('content')

<div class="container-fluid py-4">

    <div class="row">

        <div class="col-12">

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        <i class="fas fa-cash-register me-2"></i>  Gestión de Caja Diaria

                    </h5>

                    <div>

                        <span class="badge bg-warning text-dark fs-9">

                          Fecha  {{ now()->format('d/m/Y') }}

                        </span>

                    </div>

                </div>

                <div class="card-body">

                    <!-- Resumen de Caja -->

                    <div class="row mb-4">

                        <div class="col-md-3">

                            <div class="card border-start border-primary border-4">

                                <div class="card-body">

                                    <h6 class="text-muted">Monto Inicial</h6>

                                    <h4 class="text-primary">S/. {{ number_format($caja->monto_inicial ?? 0, 2) }}</h4>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card border-start border-success border-4">

                                <div class="card-body">

                                    <h6 class="text-muted">Ventas</h6>

                                    <h4 class="text-success">S/. {{ number_format($totalVentas ?? 0, 2) }}</h4>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card border-start border-info border-4">

                                <div class="card-body">

                                    <h6 class="text-muted">Ingresos Varios</h6>

                                    <h4 class="text-info">S/. {{ number_format($totalIngresos ?? 0, 2) }}</h4>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card border-start border-warning border-4">

                                <div class="card-body">

                                    <h6 class="text-muted">Egresos</h6>

                                    <h4 class="text-warning">S/. {{ number_format($totalEgresos ?? 0, 2) }}</h4>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-4">

                            <!-- Estado de Caja -->

                            <div class="card h-100">

                                <div class="card-header bg-light">

                                    <h6 class="mb-0"><i class="fas fa-tasks"></i> Estado de Caja</h6>

                                </div>

                                <div class="card-body text-center">

                                </div>

                            </div>

                        </div>

                        <div class="col-md-8">

                            <!-- Movimientos de Caja -->

                            <div class="card h-100">

                                <div class="card-header bg-light d-flex justify-content-between align-items-center">

                                    <h6 class="mb-0"><i class="fas fa-stream"></i> Movimientos de Caja</h6>

                                    <div>

                                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#cierreCajaModal">

                                            <i class="fas fa-plus me-1"></i> Nuevo Movimiento

                                        </button>

                                    </div>

                                </div>

                                <div class="card-body p-0">

                                    <div class="table-responsive">

                                        <table class="table table-hover mb-0">

                                            <thead class="table-light">

                                                <tr>

                                                    <th>Hora</th>

                                                    <th>Tipo</th>

                                                    <th>Descripción</th>

                                                    <th class="text-end">Monto</th>

                                                    <th>Acciones</th>

                                                </tr>

                                            </thead>

                                            <tbody>
                                               
                                                        
                                                  
                                                        <td>

                                                            <button class="btn btn-sm btn-outline-primary" 

                                                                    data-bs-toggle="tooltip" title="Ver detalles">

                                                                <i class="fas fa-eye"></i>

                                                            </button>

                                                        </td>

                                                    </tr>
                                                
                                                    <tr>

                                                        <td colspan="5" class="text-center py-4">

                                                            No hay movimientos registrados hoy

                                                        </td>

                                                    </tr>



                                            </tbody>

                                        </table>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cierre de Caja -->

<div class="modal fade" id="cierreCajaModal" tabindex="-1" aria-labelledby="cierreCajaModalLabel" aria-hidden="true">
   
    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" action="{{ route('caja_abrir') }}">
                @csrf

                <div class="modal-header "><i class="fas fa-key"></i> Cerrar caja

                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                 <span aria-hidden="true">&times;</span>

                 </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label for="monto_cierre" class="form-label">Monto en Caja (S/.)</label>

                        <input type="number" step="0.01" min="0" class="form-control" 
                               id="monto_cierre" name="monto_cierre" required>

                    </div>

                    <div class="mb-3">

                        <label for="observaciones_cierre" class="form-label">Observaciones</label>

                        <textarea class="form-control" id="observaciones_cierre" 
                                  name="observaciones_cierre" rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    
                    <button type="submit" class="btn btn-primary">Confirmar Cierre</button>
                
                </div>
           
            </form>
        
        </div>
    
    </div>

</div>

<!-- Modal Nuevo Movimiento -->

<div class="modal fade" id="nuevoMovimientoModal" tabindex="-1" aria-labelledby="nuevoMovimientoModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" action="{{ route('caja_abrir') }}">
                @csrf

                <div class="modal-header bg-success text-white">

                    <h5 class="modal-title" id="nuevoMovimientoModalLabel">Nuevo Movimiento</h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">Tipo de Movimiento</label>

                        <div class="form-check">

                            <input class="form-check-input" type="radio" name="tipo_movimiento" 

                                   id="ingreso" value="ingreso" checked>

                            <label class="form-check-label" for="ingreso">

                                Ingreso

                            </label>

                        </div>

                        <div class="form-check">

                            <input class="form-check-input" type="radio" name="tipo_movimiento" 

                                   id="egreso" value="egreso">

                            <label class="form-check-label" for="egreso">

                                Egreso

                            </label>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label for="monto_movimiento" class="form-label">Monto (S/.)</label>

                        <input type="number" step="0.01" min="0.01" class="form-control" 

                               id="monto_movimiento" name="monto_movimiento" required>

                    </div>

                    <div class="mb-3">

                        <label for="descripcion_movimiento" class="form-label">Descripción</label>

                        <textarea class="form-control" id="descripcion_movimiento" 

                                  name="descripcion_movimiento" rows="3" required></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                    <button type="submit" class="btn btn-success">Registrar Movimiento</button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection


@push('styles')
<style>
    .card {
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .table th {
        border-top: none;
    }
    .border-4 {
        border-width: 4px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Calcular y mostrar total al cerrar caja
        const cierreModal = document.getElementById('cierreCajaModal');
        if (cierreModal) {
            cierreModal.addEventListener('show.bs.modal', function () {
                const montoInicial = parseFloat({{ $caja->monto_inicial ?? 0 }});
                const totalVentas = parseFloat({{ $totalVentas ?? 0 }});
                const totalIngresos = parseFloat({{ $totalIngresos ?? 0 }});
                const totalEgresos = parseFloat({{ $totalEgresos ?? 0 }});
                
                const totalCalculado = montoInicial + totalVentas + totalIngresos - totalEgresos;
                document.getElementById('monto_cierre').value = totalCalculado.toFixed(2);
            });
        }
    });
</script>
@endpush

@section('scripts')
<script>
$(document).ready(function() {
    // Cargar estado de caja al inicio
    cargarEstadoCaja();
    cargarHistorialCaja();

    // Manejar apertura de caja
    $('#form-apertura').submit(function(e) {
        e.preventDefault();
        abrirCaja();
    });

    // Manejar cierre de caja
    $('#form-cierre').submit(function(e) {
        e.preventDefault();
        cerrarCaja();
    });

    // Configurar DataTable para el historial
    $('#historial-caja').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        order: [[0, 'desc']]
    });
});

function cargarEstadoCaja() {
    $.ajax({
        url: '{{ route("caja_estado") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.caja_abierta) {
                mostrarSeccionCierre(response);
            } else {
                mostrarSeccionApertura();
            }
        },
        error: function(xhr) {
            $('#caja-status').html('<div class="alert alert-danger">Error al cargar el estado de caja</div>');
        }
    });
}

function mostrarSeccionApertura() {
    $('#caja-status').html(`
        <div class="alert alert-warning">
            <h4 class="alert-heading"><i class="fas fa-lock me-2"></i>Caja Cerrada</h4>
            <p>No hay una caja abierta actualmente. Para comenzar las operaciones, por favor abra la caja.</p>
        </div>
    `);
    $('#apertura-caja-form').show();
    $('#cierre-caja-section').hide();
}

function mostrarSeccionCierre(data) {
    $('#caja-status').html(`
        <div class="alert alert-success">
            <h4 class="alert-heading"><i class="fas fa-lock-open me-2"></i>Caja Abierta</h4>
            <p>La caja fue abierta el ${data.fecha_apertura} a las ${data.hora_apertura} con un monto inicial de $${data.monto_inicial}.</p>
        </div>
    `);
    $('#apertura-caja-form').hide();
    $('#cierre-caja-section').show();
    $('#total-ventas').text('$' + data.total_ventas);
    $('#total-caja').text('$' + data.total_caja);
}

function abrirCaja() {
    const btn = $('#form-apertura button[type="submit"]');
    const btnText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');

    $.ajax({
        url: '{{ route("caja_abrir") }}',
        type: 'POST',
        data: $('#form-apertura').serialize(),
        dataType: 'json',
        success: function(response) {
            toastr.success('Caja abierta correctamente');
            $('#form-apertura')[0].reset();
            cargarEstadoCaja();
            cargarHistorialCaja();
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors) {
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error('Error al abrir la caja');
            }
        },
        complete: function() {
            btn.prop('disabled', false).html(btnText);
        }
    });
}

function cerrarCaja() {
    const btn = $('#form-cierre button[type="submit"]');
    const btnText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');

    $.ajax({
        url: '{{ route("caja_cerrar") }}',
        type: 'POST',
        data: $('#form-cierre').serialize(),
        dataType: 'json',
        success: function(response) {
            toastr.success('Caja cerrada correctamente');
            $('#form-cierre')[0].reset();
            cargarEstadoCaja();
            cargarHistorialCaja();
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors) {
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error('Error al cerrar la caja');
            }
        },
        complete: function() {
            btn.prop('disabled', false).html(btnText);
        }
    });
}

function cargarHistorialCaja() {
    $.ajax({
        url: '{{ route("caja_historial") }}',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let tbody = $('#historial-caja tbody');
            tbody.empty();
            
            response.data.forEach(function(item) {
                let estado = item.estado === 'abierta' ? 
                    '<span class="badge bg-success">Abierta</span>' : 
                    '<span class="badge bg-secondary">Cerrada</span>';
                
                let horaCierre = item.hora_cierre ? item.hora_cierre : 'N/A';
                let montoFinal = item.monto_final ? '$' + item.monto_final : 'N/A';
                
                tbody.append(`
                    <tr class="cursor-pointer" onclick="mostrarDetallesCaja(${item.id})">
                        <td>${item.fecha_apertura}</td>
                        <td>${item.hora_apertura}</td>
                        <td>${horaCierre}</td>
                        <td>$${item.monto_inicial}</td>
                        <td>${montoFinal}</td>
                        <td>${estado}</td>
                        <td>${item.usuario.name}</td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            toastr.error('Error al cargar el historial de caja');
        }
    });
}

function mostrarDetallesCaja(cajaId) {
    $.ajax({
        url: '{{ url("caja") }}/' + cajaId + '/detalles',
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#detalles-caja-content').html(response);
            $('#detallesCajaModal').modal('show');
        },
        error: function(xhr) {
            toastr.error('Error al cargar los detalles de la caja');
        }
    });
}
</script>
@endsection

@section('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .card-header h5 {
        font-weight: 600;
    }
    .table-responsive {
        min-height: 300px;
    }
</style>
@endsection