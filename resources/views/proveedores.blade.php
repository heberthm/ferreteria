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

<!-- Modal Editar Proveedor -->
<div class="modal fade" id="modalEditarProveedor" tabindex="-1" role="dialog" aria-labelledby="modalEditarProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Editar proveedor: <span id="razon_social_titulo" style="color:red"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_editar_proveedor" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="id_proveedor" name="id_proveedor">
                <input type="hidden" id="userId_editar" name="userId">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nit_editar">NIT <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nit_editar" 
                                       name="nit" 
                                       maxlength="20"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="razon_social_editar">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="razon_social_editar" 
                                       name="razon_social" 
                                       maxlength="255"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_contacto_editar">Nombre Contacto <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre_contacto_editar" 
                                       name="nombre_contacto" 
                                       maxlength="255"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono_editar">Teléfono <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="telefono_editar" 
                                       name="telefono" 
                                       maxlength="50"
                                       required>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Máximo 50 caracteres</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_editar">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email_editar" 
                                       name="email" 
                                       maxlength="255">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion_editar">Dirección</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="direccion_editar" 
                                       name="direccion" 
                                       maxlength="255">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="BtnActualizar_proveedor">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner_editar" role="status" aria-hidden="true"></span>
                        <span id="texto_btn_editar"><i class="fas fa-save"></i> Actualizar proveedor</span>
                    </button>
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
    // Inicializar DataTable
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
            "emptyTable": "No hay proveedores registrados.",
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

    // Variable para controlar el estado de la actualización
    var actualizando = false;

    // Resetear formulario al abrir modal de crear
    $('#registrar_proveedor').click(function() {
        // Limpiar backdrops
        limpiarBackdrops();
        
        // Resetear formulario
        $('#form_guardar_proveedor')[0].reset();
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
        
        // Actualizar el userId oculto
        var userId = "{{ auth()->id() }}";
        $('#userId').val(userId);
    });

    // Evento cuando el modal de crear se abre completamente
    $('#modalProveedores').on('shown.bs.modal', function () {
        $('#nit').focus();
    });

    // Evento cuando el modal de crear se cierra
    $('#modalProveedores').on('hidden.bs.modal', function () {
        limpiarBackdrops();
        restaurarBotonGuardar();
    });

    // Guardar proveedor
    $('#form_guardar_proveedor').submit(function(e) {
        e.preventDefault();
        
        // Mostrar spinner
        mostrarSpinnerGuardar();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: "{{ route('proveedores.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Cerrar modal
                    $('#modalProveedores').modal('hide');
                    
                    // Restaurar botón
                    restaurarBotonGuardar();
                    
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
                    
                    // Recargar tabla
                    tablaProveedores.ajax.reload();
                }
            },
            error: function(xhr) {
                restaurarBotonGuardar();
                
                if (xhr.status === 422) {
                    mostrarErroresValidacion(xhr.responseJSON.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar el proveedor'
                    });
                }
            }
        });
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
                
                // Actualizar userId
                var userId = "{{ auth()->id() }}";
                $('#userId_editar').val(userId);
                
                // Limpiar errores anteriores
                $('.invalid-feedback').empty();
                $('.form-control').removeClass('is-invalid');
                
                // Restaurar botón de actualizar
                restaurarBotonActualizar();
                
                $('#modalEditarProveedor').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la información del proveedor'
                });
            }
        });
    });

    // Actualizar proveedor - CORREGIDO
  // Actualizar proveedor - VERSIÓN MEJORADA
$('#form_editar_proveedor').submit(function(e) {
    e.preventDefault();
    
    // Prevenir múltiples envíos
    if (actualizando) {
        return;
    }
    
    actualizando = true;
    
    // Mostrar spinner
    $('#spinner_editar').removeClass('d-none');
    $('#texto_btn_editar').html(' Actualizando...');
    $('#BtnActualizar_proveedor').prop('disabled', true);
    
    var id = $('#id_proveedor').val();
    
    // Validar longitud del teléfono antes de enviar
    var telefono = $('#telefono_editar').val();
    if (telefono.length > 50) {
        $('#telefono_editar').addClass('is-invalid');
        $('#telefono_editar').siblings('.invalid-feedback').html('El teléfono no puede tener más de 50 caracteres');
        
        restaurarBotonActualizar();
        actualizando = false;
        return;
    }
    
    // Usar FormData para enviar todos los datos
    var formData = new FormData(this);
    
    // Agregar método PUT explícitamente
    formData.append('_method', 'PUT');
    
    // Debug: Ver qué se está enviando
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    $.ajax({
        url: "{{ url('proveedores') }}/" + id,
        type: "POST", // Usamos POST con _method=PUT
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Cerrar modal
                $('#modalEditarProveedor').modal('hide');
                
                // Recargar tabla
                tablaProveedores.ajax.reload();
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
            
            restaurarBotonActualizar();
            actualizando = false;
        },
        error: function(xhr) {
            restaurarBotonActualizar();
            actualizando = false;
            
            if (xhr.status === 422) {
                // Errores de validación
                var errors = xhr.responseJSON.errors;
                console.log('Errores de validación:', errors);
                
                // Limpiar errores anteriores
                $('.invalid-feedback').empty();
                $('.form-control').removeClass('is-invalid');
                
                // Mostrar errores específicos
                $.each(errors, function(field, messages) {
                    var campo = $('#' + field + '_editar');
                    if (campo.length) {
                        campo.addClass('is-invalid');
                        campo.siblings('.invalid-feedback').html(messages[0]);
                    }
                });
                
                // Mostrar mensaje de error general si es el teléfono
                if (errors.telefono) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error en el teléfono',
                        text: errors.telefono[0],
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            } else {
                // Error del servidor
                console.error('Error completo:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error al actualizar el proveedor'
                });
            }
        }
    });
});

// Función mejorada para restaurar botón
function restaurarBotonActualizar() {
    $('#spinner_editar').addClass('d-none');
    $('#texto_btn_editar').html('<i class="fas fa-save"></i> Actualizar proveedor');
    $('#BtnActualizar_proveedor').prop('disabled', false);
}

// Validación en tiempo real para el teléfono
$('#telefono_editar').on('input', function() {
    var telefono = $(this).val();
    var longitud = telefono.length;
    
    // Remover clases de error mientras escribe
    $(this).removeClass('is-invalid');
    $(this).siblings('.invalid-feedback').empty();
    
    // Mostrar contador de caracteres
    if (!$('#telefono_counter').length) {
        $(this).after('<small id="telefono_counter" class="form-text text-muted"></small>');
    }
    
    if (longitud > 45) { // Advertencia cuando se acerca al límite
        $('#telefono_counter')
            .text(longitud + '/50 caracteres')
            .removeClass('text-muted text-danger')
            .addClass('text-warning');
    } else if (longitud >= 50) {
        $('#telefono_counter')
            .text(longitud + '/50 caracteres (límite alcanzado)')
            .removeClass('text-muted text-warning')
            .addClass('text-danger');
    } else {
        $('#telefono_counter')
            .text(longitud + '/50 caracteres')
            .removeClass('text-warning text-danger')
            .addClass('text-muted');
    }
});

// Evento específico para el botón cerrar
$(document).on('click', '[data-dismiss="modal"]', function() {
    var modalId = $(this).closest('.modal').attr('id');
    $('#' + modalId).modal('hide');
});

// Limpiar cuando se cierra el modal
$('#modalEditarProveedor').on('hidden.bs.modal', function () {
    limpiarBackdrops();
    restaurarBotonActualizar();
    actualizando = false;
    
    // Limpiar errores
    $('.invalid-feedback').empty();
    $('.form-control').removeClass('is-invalid');
    $('#telefono_counter').remove();
});

    // Evento para cerrar modal con botón X
    $('#modalEditarProveedor').on('click', '.close, .btn-secondary', function() {
        $('#modalEditarProveedor').modal('hide');
    });

    // Evento cuando se cierra el modal de editar
    $('#modalEditarProveedor').on('hidden.bs.modal', function () {
        limpiarBackdrops();
        restaurarBotonActualizar();
        actualizando = false;
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la información del proveedor'
                });
            }
        });
    });

    // Eliminar proveedor
    $(document).on('click', '.btn-eliminar', function() {
        var id = $(this).data('id');
        var razon_social = $(this).data('nombre');
        
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar el proveedor'
                        });
                    }
                });
            }
        });
    });

    // Funciones auxiliares
    function limpiarBackdrops() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    }

    function mostrarSpinnerGuardar() {
        $('#spinner_guardar').removeClass('d-none');
        $('#texto_btn_guardar').text('Guardando...');
        $('#BtnGuardar_proveedor').prop('disabled', true);
    }

    function restaurarBotonGuardar() {
        $('#spinner_guardar').addClass('d-none');
        $('#texto_btn_guardar').text('Guardar proveedor');
        $('#BtnGuardar_proveedor').prop('disabled', false);
    }

    function restaurarBotonActualizar() {
        $('#spinner_editar').addClass('d-none');
        $('#texto_btn_editar').text('Actualizar proveedor');
        $('#BtnActualizar_proveedor').prop('disabled', false);
    }

    function mostrarErroresValidacion(errors) {
        var errorMessages = [];
        
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
        
        $.each(errors, function(key, value) {
            var campo = $('#' + key);
            if (campo.length) {
                campo.addClass('is-invalid');
                campo.siblings('.invalid-feedback').html(value[0]);
            }
            errorMessages.push(value[0]);
        });
        
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            html: errorMessages.join('<br>')
        });
    }

    function mostrarErroresValidacionEditar(errors) {
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');
        
        $.each(errors, function(key, value) {
            var campo = $('#' + key + '_editar');
            if (campo.length) {
                campo.addClass('is-invalid');
                campo.siblings('.invalid-feedback').html(value[0]);
            }
        });
        
        // No mostramos Swal aquí para no interrumpir la edición
    }
});

</script>
@endpush
@endsection