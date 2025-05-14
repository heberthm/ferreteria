@extends('layouts.app')

@section('title', 'Gestión de Caja Diaria')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-center">Gestión de Caja Diaria</h2>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-default text-white">
                    <h5 class="mb-0">Estado de Caja</h5>
                </div>
                <div class="card-body">
                    <div id="caja-status" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                    </div>

                    <div id="apertura-caja-form" class="mt-4" style="display: none;">
                        <form id="form-apertura">
                            @csrf
                            <div class="mb-3">
                                <label for="monto_inicial" class="form-label">Monto Inicial</label>
                                <input type="number" step="0.01" class="form-control" id="monto_inicial" name="monto_inicial" required>
                            </div>
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones (Opcional)</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-lock-open me-2"></i>Abrir Caja
                            </button>
                        </form>
                    </div>

                    <div id="cierre-caja-section" class="mt-4" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Ventas del Día</h6>
                                        <p class="display-6 text-primary" id="total-ventas">$0.00</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Efectivo en Caja</h6>
                                        <p class="display-6 text-success" id="total-caja">$0.00</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="form-cierre">
                            @csrf
                            <div class="mb-3">
                                <label for="monto_cierre" class="form-label">Monto en Efectivo</label>
                                <input type="number" step="0.01" class="form-control" id="monto_cierre" name="monto_cierre" required>
                            </div>
                            <div class="mb-3">
                                <label for="observaciones_cierre" class="form-label">Observaciones (Opcional)</label>
                                <textarea class="form-control" id="observaciones_cierre" name="observaciones_cierre" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-lock me-2"></i>Cerrar Caja
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Movimientos -->
    <div class="row mt-4 justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Historial de Caja</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="historial-caja" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora Apertura</th>
                                    <th>Hora Cierre</th>
                                    <th>Monto Inicial</th>
                                    <th>Monto Final</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles -->
<div class="modal fade" id="detallesCajaModal" tabindex="-1" aria-labelledby="detallesCajaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detallesCajaModalLabel">Detalles de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detalles-caja-content">
                <!-- Contenido cargado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

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