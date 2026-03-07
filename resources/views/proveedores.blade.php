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
    #tablaProveedores {
        width: 100% !important;
    }

    .proveedor-imagen-tabla {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 2px;
        background: white;
        transition: transform 0.2s;
    }
    
    .proveedor-imagen-tabla:hover {
        transform: scale(2);
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        cursor: pointer;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
</style>

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fas fa-truck"></i> Gestión de Proveedores</h5>
    <div>
        <button type="button" id="registrar_proveedor" class="btn btn-primary" data-toggle="modal" data-target="#modalProveedores">
            <span class="fa fa-plus"></span>  
            Registrar proveedor
        </button>
    </div> 
</div>
  
    <div class="card-body">  
       <div class="table-responsive">
            <table class="table table-hover" id="tablaProveedores" style="width:100%; font-size:12.5px;">   
                <thead>  
                    <tr>   
                        <th>ID</th>
                        <th>NIT</th>
                        <th>Razón Social</th>   
                        <th>Contacto</th>   
                        <th>Teléfono</th>   
                        <th>Email</th>
                        <th>Dirección</th>
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
 Modal para crear proveedores 
====================================== -->

<div class="modal fade" id="modalProveedores" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-truck"></i> Nuevo proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_guardar_proveedor" action="{{ url('proveedores') }}">
                @csrf
                <!-- Campo userId oculto -->
                <input type="hidden" id="userId" name="userId" value="{{ auth()->id() }}">
                
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="nit" class="col-sm-4 col-form-label">NIT *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nit" name="nit" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="razon_social" class="col-sm-4 col-form-label">Razón Social *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="razon_social" name="razon_social" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="nombre_contacto" class="col-sm-4 col-form-label">Nombre Contacto *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre_contacto" name="nombre_contacto" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="telefono" class="col-sm-4 col-form-label">Teléfono *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
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
                        </div>
                    </div>
                </div>

               <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="BtnGuardar_proveedor">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner_guardar"></span>
                        <span id="texto_btn_guardar">Guardar proveedor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================= 
 Modal para editar proveedores 
====================================== -->

<div class="modal fade" id="modalEditarProveedor" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Editar proveedor: <span id="razon_social_titulo" style="color:red"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_editar_proveedor">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="id_proveedor" name="id_proveedor">
                    <!-- Campo userId oculto en edición -->
                    <input type="hidden" id="userId_editar" name="userId" value="{{ auth()->id() }}">
                    
                    <div class="row">
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="nit_editar" class="col-sm-4 col-form-label">NIT *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nit_editar" name="nit" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="razon_social_editar" class="col-sm-4 col-form-label">Razón Social *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="razon_social_editar" name="razon_social" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="nombre_contacto_editar" class="col-sm-4 col-form-label">Nombre Contacto *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre_contacto_editar" name="nombre_contacto" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="telefono_editar" class="col-sm-4 col-form-label">Teléfono *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="telefono_editar" name="telefono" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
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
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="BtnEditar_proveedor">Actualizar proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================= 
 Modal para ver proveedores 
====================================== -->

<div class="modal fade" id="modalVerProveedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Datos del proveedor: <span id="razon_social_ver_titulo" style="color:red"></span></h5>
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
                            <label class="col-sm-4 col-form-label font-weight-bold">NIT:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_nit" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Razón Social:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_razon_social" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Nombre Contacto:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_nombre_contacto" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Teléfono:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_telefono" readonly>
                            </div>
                        </div>
                        
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
    // Inicializar DataTable - NOTA: Quitamos la columna userId de la vista
    var tablaProveedores = $('#tablaProveedores').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('proveedores.data') }}",
            type: "GET",
            error: function(xhr, error, thrown) {
                console.log('Error en DataTable:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar datos',
                    text: 'No se pudieron cargar los proveedores'
                });
            }
        },
        columns: [
            { data: 'id_proveedor', name: 'id_proveedor' },
            { data: 'nit', name: 'nit' },
            { data: 'razon_social', name: 'razon_social' },
            { data: 'nombre_contacto', name: 'nombre_contacto' },
            { data: 'telefono', name: 'telefono' },
            { data: 'email', name: 'email' },
            { data: 'direccion', name: 'direccion' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        "language": {

            "emptyTable": "No hay productos registrados.",
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
    $('#registrar_proveedor').click(function() {
        $('#form_guardar_proveedor')[0].reset();
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
        
        // Actualizar el userId oculto con el ID del usuario actual (por si acaso)
        var userId = "{{ auth()->id() }}";
        $('#userId').val(userId);
    });

    // Resetear formulario al abrir modal de crear y dar foco al campo NIT
    $('#registrar_proveedor').click(function() {
        $('#form_guardar_proveedor')[0].reset();
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
        
        // Actualizar el userId oculto con el ID del usuario actual
        var userId = "{{ auth()->id() }}";
        $('#userId').val(userId);
        
        // Dar foco al campo NIT cuando se abre el modal
        setTimeout(function() {
            $('#nit').focus();
        }, 500); // Pequeño retraso para asegurar que el modal esté completamente abierto
    });

    // Resetear formulario al abrir modal de crear y dar foco al campo NIT
$('#registrar_proveedor').click(function() {
    $('#form_guardar_proveedor')[0].reset();
    $('.invalid-feedback').empty();
    $('.form-control').removeClass('is-invalid');
    
    // Actualizar el userId oculto con el ID del usuario actual
    var userId = "{{ auth()->id() }}";
    $('#userId').val(userId);
    
    // El foco se manejará cuando el modal esté completamente abierto
});

// Evento que se dispara cuando el modal ha terminado de abrirse
$('#modalProveedores').on('shown.bs.modal', function () {
    // Dar foco al campo NIT cuando el modal esté completamente visible
    $('#nit').focus();
});

// Guardar proveedor con spinner
$('#form_guardar_proveedor').submit(function(e) {
    e.preventDefault();
    
    // Mostrar spinner y cambiar texto del botón
    $('#spinner_guardar').removeClass('d-none');
    $('#texto_btn_guardar').text('Procesando...');
    $('#BtnGuardar_proveedor').prop('disabled', true);
    
    var formData = $(this).serialize();
    
    $.ajax({
        url: "{{ route('proveedores.store') }}",
        type: "POST",
        data: formData,
        success: function(response) {
            if (response.success) {
                // FORZAR CIERRE DEL MODAL - Método directo
                $('#modalProveedores').modal('hide');
                
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
                $('#texto_btn_guardar').text('Guardar proveedor');
                $('#BtnGuardar_proveedor').prop('disabled', false);
                
                // Resetear formulario
                $('#form_guardar_proveedor')[0].reset();
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
                tablaProveedores.ajax.reload();
            }
        },
        error: function(xhr) {
            // Restaurar botón
            $('#spinner_guardar').addClass('d-none');
            $('#texto_btn_guardar').text('Guardar proveedor');
            $('#BtnGuardar_proveedor').prop('disabled', false);
            
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
                Swal.fire('Error', 'Error al guardar el proveedor', 'error');
            }
        }
    });
});

// Evento para asegurar que cuando se abre el modal, todo esté limpio
$('#registrar_proveedor').click(function() {
    // Asegurar que no haya backdrops anteriores
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    // Limpiar formulario
    $('#form_guardar_proveedor')[0].reset();
    $('.invalid-feedback').empty();
    $('.form-control').removeClass('is-invalid');
    
    // Restaurar botón
    $('#spinner_guardar').addClass('d-none');
    $('#texto_btn_guardar').text('Guardar proveedor');
    $('#BtnGuardar_proveedor').prop('disabled', false);
    
    // Actualizar userId
    $('#userId').val("{{ auth()->id() }}");
});

// Evento cuando el modal se cierra manualmente
$('#modalProveedores').on('hidden.bs.modal', function () {
    // Limpiar todo
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
    
    // Restaurar botón
    $('#spinner_guardar').addClass('d-none');
    $('#texto_btn_guardar').text('Guardar proveedor');
    $('#BtnGuardar_proveedor').prop('disabled', false);
    
    // Limpiar errores
    $('.invalid-feedback').empty();
    $('.form-control').removeClass('is-invalid');
});

// Evento cuando el modal se abre
$('#modalProveedores').on('shown.bs.modal', function () {
    // Dar foco al campo NIT
    $('#nit').focus();
});


    // Editar proveedor
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: "{{ url('proveedores') }}/" + id + "/edit",
            type: "GET",
            success: function(data) {
                $('#id_proveedor').val(data.id_proveedor);
                $('#nit_editar').val(data.nit);
                $('#razon_social_editar').val(data.razon_social);
                $('#nombre_contacto_editar').val(data.nombre_contacto);
                $('#telefono_editar').val(data.telefono);
                $('#email_editar').val(data.email);
                $('#direccion_editar').val(data.direccion);
                $('#razon_social_titulo').text(data.razon_social);
                
                // Actualizar el userId oculto con el ID del usuario actual
                var userId = "{{ auth()->id() }}";
                $('#userId_editar').val(userId);
                
                $('#modalEditarProveedor').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la información del proveedor', 'error');
            }
        });
    });

    // Actualizar proveedor
    $('#form_editar_proveedor').submit(function(e) {
        e.preventDefault();
        
        var id = $('#id_proveedor').val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: "{{ url('proveedores') }}/" + id,
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modalEditarProveedor').modal('hide');
                    tablaProveedores.ajax.reload();
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
                    Swal.fire('Error', 'Error al actualizar el proveedor', 'error');
                }
            }
        });
    });

    // Ver proveedor
    $(document).on('click', '.btn-ver', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: "{{ url('proveedores') }}/" + id,
            type: "GET",
            success: function(data) {
                $('#ver_id').val(data.id_proveedor);
                $('#ver_nit').val(data.nit);
                $('#ver_razon_social').val(data.razon_social);
                $('#ver_nombre_contacto').val(data.nombre_contacto);
                $('#ver_telefono').val(data.telefono);
                $('#ver_email').val(data.email || 'N/A');
                $('#ver_direccion').val(data.direccion || 'N/A');
                $('#razon_social_ver_titulo').text(data.razon_social);
                
                $('#modalVerProveedor').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la información del proveedor', 'error');
            }
        });
    });

    // Eliminar proveedor
    $(document).on('click', '.btn-eliminar', function() {
        var id = $(this).data('id');
        var razon_social = $(this).data('razon_social');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas eliminar el proveedor " + razon_social + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('proveedores') }}/" + id,
                    type: "DELETE",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            tablaProveedores.ajax.reload();
                            Swal.fire('Eliminado', response.message, 'success');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al eliminar el proveedor', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection