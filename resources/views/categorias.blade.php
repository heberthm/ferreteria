@extends('layouts.app')

@section('content')

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between float-right">
        <h5 class="mb-0"><i class="fas fa-th-list"></i> Gestión de Categorías</h5>
        <button class="btn btn-primary float-right" id="createCategoryBtn" data-toggle="modal" data-target="#categoryModal">Nueva Categoría</button>
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
<div class="modal fade" id="categoryModal" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header  bg-light" >

                <h5 class="modal-title" id="modalTitle"><i class="fas fa-th-list"></i> Nueva Categoría</h5>

                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                 <span aria-hidden="true">&times;</span>

                 </button>

            </div>

            <form id="categoryForm">

                <div class="modal-body">

                    <input type="hidden" id="categoryId">

                    <div class="mb-3">

                        <label for="name" class="form-label">Nombre</label>

                        <input type="text" class="form-control" id="name" name="name" required>

                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">

                        <label for="description" class="form-label">Descripción</label>

                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                      
                        <div class="invalid-feedback"></div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                 
                    <button type="submit" class="btn btn-primary">Guardar</button>
             
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

@section('scripts')

<script>

$(document).ready(function() {
    // CSRF Token
  
  $.ajaxSetup({
    
    headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }

    });

    // Variables

    let editingCategoryId = null;

    let deleteCategoryId = null;


    // Cargar categorías

    function loadCategories() {

        $.get('', function(data) {

            let rows = '';

            data.forEach(category => {

                rows += `
                    <tr>
                        <td>${category.id}</td>
                        <td>${category.name}</td>
                        <td>${category.description || ''}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-category" data-id="${category.id}">Editar</button>
                            <button class="btn btn-sm btn-danger delete-category" data-id="${category.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            $('#categoriesTable tbody').html(rows);
        });
    }

    // Mostrar modal para crear/editar
    $('#createCategoryBtn').click(function() {
        editingCategoryId = null;
        $('#modalTitle').text('Nueva Categoría');
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        $('.invalid-feedback').text('').parent().removeClass('has-error');
        $('#categoryModal').modal('show');
    });

    // Editar categoría
    $(document).on('click', '.edit-category', function() {
        editingCategoryId = $(this).data('id');
        $('#modalTitle').text('Editar Categoría');
        $('.invalid-feedback').text('').parent().removeClass('has-error');
        
        $.get(`/categories/${editingCategoryId}`, function(data) {
            $('#categoryId').val(data.id);
            $('#name').val(data.name);
            $('#description').val(data.description);
            $('#categoryModal').modal('show');
        });
    });

    // Enviar formulario
    $('#categoryForm').submit(function(e) {
        e.preventDefault();
        
        let url = editingCategoryId ? `/categories/${editingCategoryId}` : '/categories';
        let method = editingCategoryId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#categoryModal').modal('hide');
                loadCategories();
                toastr.success('Categoría guardada correctamente');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    let input = $(`#${field}`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors[field][0]);
                }
            }
        });
    });

    // Mostrar modal de confirmación para eliminar
    $(document).on('click', '.delete-category', function() {
        deleteCategoryId = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
    });

    // Confirmar eliminación
    $('#confirmDeleteBtn').click(function() {
        $.ajax({
            url: `/categories/${deleteCategoryId}`,
            method: 'DELETE',
            success: function(response) {
                $('#confirmDeleteModal').modal('hide');
                loadCategories();
                toastr.success('Categoría eliminada correctamente');
            }
        });
    });

    // Cargar categorías al inicio
    loadCategories();
});
</script>
@endsection
@endsection