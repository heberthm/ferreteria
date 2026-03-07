@extends('layouts.app')
@section('content')

<style>
    .text-success { color: green; }
    .text-danger { color: red; }
    #preview {
        max-width: 120px;
        max-height: 120px;
        margin-top: 5px;
        display: none;
    }

    /* Asegurar que los botones sean visibles */
    .btn-group {
        display: flex;
        gap: 3px;
    }

    .btn-group .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.8rem;
    }

    /* Opcional: hacer la tabla responsive */
    #tablaClientes {
        width: 100% !important;
    }

    .cliente-imagen-tabla {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 2px;
        background: white;
        transition: transform 0.2s;
    }
    
    .cliente-imagen-tabla:hover {
        transform: scale(2);
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        cursor: pointer;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
</style>

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users"></i> Gestión de Clientes</h5>
        <div>
            <button type="button" id="registrar_cliente" class="btn btn-primary" data-toggle="modal" data-target="#modalClientes">
                <span class="fa fa-plus"></span>  
                Registrar cliente
            </button>
        </div> 
    </div>
  
    <div class="card-body">  
        <div class="table-responsive">
            <table class="table table-hover" id="tablaClientes" style="width:100%; font-size:12.5px;">   
                <thead>  
                    <tr>   
                        <th>ID</th>
                        <th>Cédula/NIT</th>
                        <th>Nombre</th>   
                        <th>Teléfono</th>   
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Acciones</th>   
                    </tr>   
                </thead>   
                <tbody>   
                <!-- Datos se cargarán via AJAX -->   
                </tbody>   
            </table>   
        </div>  
    </div>
</div>

<!-- ================================= 
 Modal para crear clientes 
====================================== -->

<div class="modal fade" id="modalClientes" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_guardar_cliente" action="{{ url('clientes') }}">
                @csrf
                <!-- Campo userId oculto -->
                <input type="hidden" id="userId" name="userId" value="{{ auth()->id() }}">
                
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="cedula" class="col-sm-4 col-form-label">Cédula/NIT *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="cedula" name="cedula" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="nombre" class="col-sm-4 col-form-label">Nombre Completo *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="telefono" class="col-sm-4 col-form-label">Teléfono *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="email" class="col-sm-4 col-form-label">Email</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" id="email" name="email">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="direccion" class="col-sm-4 col-form-label">Dirección</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="direccion" name="direccion" rows="3"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="estado" class="col-sm-4 col-form-label">Estado</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="estado" name="estado">
                                        <option value="activo" selected>Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="BtnGuardar_cliente">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner_guardar"></span>
                        <span id="texto_btn_guardar">Guardar cliente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================= 
 Modal para editar clientes 
====================================== -->

<div class="modal fade" id="modalEditarCliente" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Editar cliente: <span id="nombre_titulo" style="color:red"></span></h5>               

                 <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            
            <form method="POST" id="form_editar_cliente">
                @csrf 
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="id_cliente" name="id_cliente">
                    <!-- Campo userId oculto en edición -->
                    <input type="hidden" id="userId_editar" name="userId" value="{{ auth()->id() }}">
                    
                    <div class="row">
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="cedula_editar" class="col-sm-4 col-form-label">Cédula/NIT *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="cedula_editar" name="cedula" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="nombre_editar" class="col-sm-4 col-form-label">Nombre Completo *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre_editar" name="nombre" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="telefono_editar" class="col-sm-4 col-form-label">Teléfono *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="telefono_editar" name="telefono" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="email_editar" class="col-sm-4 col-form-label">Email</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" id="email_editar" name="email">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="direccion_editar" class="col-sm-4 col-form-label">Dirección</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="direccion_editar" name="direccion" rows="3"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="estado_editar" class="col-sm-4 col-form-label">Estado</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="estado_editar" name="estado">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="BtnEditar_cliente">Actualizar cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================= 
 Modal para ver clientes 
====================================== -->

<div class="modal fade" id="modalVerCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Datos del cliente: <span id="nombre_ver_titulo" style="color:red"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">ID:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_id" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Cédula/NIT:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_cedula" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Nombre:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_nombre" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Teléfono:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_telefono" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Email:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control-plaintext" id="ver_email" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Dirección:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control-plaintext" id="ver_direccion" rows="3" readonly></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Estado:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_estado" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
$(document).ready(function() {

 $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inicializar DataTable
    var tablaClientes = $('#tablaClientes').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('clientes.data') }}",
            type: "GET",
            error: function(xhr, error, thrown) {
                console.log('Error en DataTable:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar datos',
                    text: 'No se pudieron cargar los clientes'
                });
            }
        },
        columns: [
            { data: 'id_cliente', name: 'id_cliente' },
            { data: 'cedula', name: 'cedula' },
            { data: 'nombre', name: 'nombre' },
            { data: 'telefono', name: 'telefono' },
            { data: 'email', name: 'email' },
            { data: 'direccion', name: 'direccion' },
            { data: 'estado', name: 'estado' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        "language": {
            "emptyTable": "No hay clientes registrados.",
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
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]]
    });

    // Resetear formulario al abrir modal de crear
    $('#registrar_cliente').click(function() {
        $('#form_guardar_cliente')[0].reset();
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
        
        // Actualizar el userId oculto
        var userId = "{{ auth()->id() }}";
        $('#userId').val(userId);
        
        // Establecer estado por defecto
        $('#estado').val('activo');
    });

    // Evento que se dispara cuando el modal ha terminado de abrirse
    $('#modalClientes').on('shown.bs.modal', function () {
        $('#cedula').focus();
    });

    // Guardar cliente con spinner
    $('#form_guardar_cliente').submit(function(e) {
        e.preventDefault();
        
        // Mostrar spinner y cambiar texto del botón
        $('#spinner_guardar').removeClass('d-none');
        $('#texto_btn_guardar').text('Procesando...');
        $('#BtnGuardar_cliente').prop('disabled', true);
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: "{{ route('clientes.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    // FORZAR CIERRE DEL MODAL
                    $('#modalClientes').modal('hide');
                    
                    // Eliminar cualquier backdrop que haya quedado
                    if ($('.modal-backdrop').length > 0) {
                        $('.modal-backdrop').remove();
                    }
                    
                    // Quitar la clase modal-open del body
                    $('body').removeClass('modal-open');
                    
                    // Restaurar el estilo del body
                    $('body').css('padding-right', '');
                    
                    // Restaurar botón
                    $('#spinner_guardar').addClass('d-none');
                    $('#texto_btn_guardar').text('Guardar cliente');
                    $('#BtnGuardar_cliente').prop('disabled', false);
                    
                    // Resetear formulario
                    $('#form_guardar_cliente')[0].reset();
                    $('#userId').val("{{ auth()->id() }}");
                    
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Recargar la tabla
                    tablaClientes.ajax.reload();
                }
            },
            error: function(xhr) {
                // Restaurar botón
                $('#spinner_guardar').addClass('d-none');
                $('#texto_btn_guardar').text('Guardar cliente');
                $('#BtnGuardar_cliente').prop('disabled', false);
                
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    
                    $('.invalid-feedback').empty();
                    $('.form-control').removeClass('is-invalid');
                    
                    $.each(errors, function(key, value) {
                        var campo = $('#' + key);
                        if (campo.length === 0) {
                            campo = $('#' + key + '_editar');
                        }
                        
                        campo.addClass('is-invalid');
                        campo.siblings('.invalid-feedback').html(value[0]);
                        errorMessages.push(value[0]);
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        html: errorMessages.join('<br>'),
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    Swal.fire('Error', 'Error al guardar el cliente', 'error');
                }
            }
        });
    });

    // Evento cuando el modal se cierra manualmente
    $('#modalClientes').on('hidden.bs.modal', function () {
        // Limpiar todo
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        
        // Restaurar botón
        $('#spinner_guardar').addClass('d-none');
        $('#texto_btn_guardar').text('Guardar cliente');
        $('#BtnGuardar_cliente').prop('disabled', false);
        
        // Limpiar errores
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
    });

    // Editar cliente
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: "{{ url('clientes') }}/" + id + "/edit",
            type: "GET",
            success: function(data) {
                $('#id_cliente').val(data.id_cliente);
                $('#cedula_editar').val(data.cedula);
                $('#nombre_editar').val(data.nombre);
                $('#telefono_editar').val(data.telefono);
                $('#email_editar').val(data.email);
                $('#direccion_editar').val(data.direccion);
                $('#estado_editar').val(data.estado);
                $('#nombre_titulo').text(data.nombre);
                
                // Actualizar el userId oculto
                var userId = "{{ auth()->id() }}";
                $('#userId_editar').val(userId);
                
                $('#modalEditarCliente').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la información del cliente', 'error');
            }
        });
    });

    // Actualizar cliente
    $('#form_editar_cliente').submit(function(e) {
        e.preventDefault();
        
        var id = $('#id_cliente').val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: "{{ url('clientes') }}/" + id,
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modalEditarCliente').modal('hide');
                    tablaClientes.ajax.reload();
                    Swal.fire('Éxito', response.message, 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '_editar').addClass('is-invalid');
                        $('#' + key + '_editar').siblings('.invalid-feedback').html(value[0]);
                    });
                } else {
                    Swal.fire('Error', 'Error al actualizar el cliente', 'error');
                }
            }
        });
    });

    // Ver cliente
    $(document).on('click', '.btn-ver', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: "{{ url('clientes') }}/" + id,
            type: "GET",
            success: function(data) {
                $('#ver_id').val(data.id_cliente);
                $('#ver_cedula').val(data.cedula);
                $('#ver_nombre').val(data.nombre);
                $('#ver_telefono').val(data.telefono);
                $('#ver_email').val(data.email || 'N/A');
                $('#ver_direccion').val(data.direccion || 'N/A');
                
                var estadoTexto = data.estado === 'activo' ? 'Activo' : 'Inactivo';
                $('#ver_estado').val(estadoTexto);
                
                $('#nombre_ver_titulo').text(data.nombre);
                
                $('#modalVerCliente').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la información del cliente', 'error');
            }
        });
    });

    // Eliminar cliente
    
// Eliminar cliente - VERSIÓN CON FETCH API
$(document).on('click', '.btn-eliminar', function() {
    var id = $(this).data('id');
    var nombre = $(this).data('nombre');
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar el cliente " + nombre + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Obtener token CSRF
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`{{ url('clientes') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    tablaClientes.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', data.message || 'Error al eliminar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al eliminar el cliente', 'error');
            });
        }
    });
});
    // Evento para asegurar que los modales se cierren correctamente
$('.modal').on('hidden.bs.modal', function () {
    // Eliminar cualquier backdrop que haya quedado
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
});
});
</script>
@endpush
@endsection