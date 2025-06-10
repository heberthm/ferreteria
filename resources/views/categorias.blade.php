@extends('layouts.app')

@section('content')


<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between float-right">
        <h5 class="mb-0"><i class="fas fa-th-list"></i> Gestión de Categorías</h5>
        <button class="btn btn-primary float-right" id="BtnCrearCategoria" data-toggle="modal" data-target="#modalCategoria"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Categoría</button>
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover" id="categoriesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos se cargarán via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para crear/editar categorías -->
<div class="modal fade" id="modalCategoria" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header  bg-light" >

                <h5 class="modal-title" id="modalTitle"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Categoría</h5>

                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                 <span aria-hidden="true">&times;</span>

                 </button>

            </div>

            <form method="POST" id="form_guardar_categoria" action="{{ url('categorias') }}" >
             @csrf  
                <div class="modal-body">

                    <input type="hidden" id="id_categoria">

                    <div class="mb-3">

                        <label for="nombre" class="form-label">Nombre</label>

                        <input type="text" class="form-control" id="nombre" name="nombre" required>

                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">

                        <label for="descripcion" class="form-label">Descripción</label>

                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                      
                        <div class="invalid-feedback"></div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                   <button type="submit" class="btn btn-primary" id="BtnGuardar_categoria" name="BtnGuardar_categoria loader">Guardar</button>
                   <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::check() ? Auth::user()->id : null}}" readonly>

                </div>
          
            </form>
      
        </div>
  
    </div>
</div>

<!-- Modal de confirmación para eliminar -->

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Confirmar Eliminación</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          
            </div>
            <div class="modal-body">

                ¿Estás seguro de que deseas eliminar esta categoría?
          
            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
             
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
          
            </div>
     
        </div>
  
    </div>

</div>


<script>

$(document).ready(function() {
    $('#form_guardar_categoria').on('submit', function(event) {
        // 1. Prevenir el comportamiento por defecto del formulario
        event.preventDefault();
        
        // 2. Configurar el botón (spinner y texto)
        const btn = $('#BtnGuardar_categoria');
        const originalText = btn.html();
        btn.html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Procesando...
        `).prop('disabled', true);

        // 3. Configurar AJAX
        $.ajax({
            url: "/categorias",
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Restaurar botón
                btn.html(originalText).prop('disabled', false);
                
                // Resetear formulario y cerrar modal
                $('#form_guardar_categoria')[0].reset();
                $('#modalCategoria').modal('hide');
                
                // Mostrar notificación
                toastr.success("Registro creado correctamente");
            },
            error: function(xhr) {
                // Restaurar botón
                btn.html(originalText).prop('disabled', false);
                
                // Mostrar error
                toastr.error(xhr.responseJSON.message || "Error al guardar los datos");
            }
        });
    });
});

</script>

@endsection