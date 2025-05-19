@extends('layouts.app')

@section('content')

<br>

<div class="card">

    <div class="card-header d-flex bg-light justify-content-between align-items-right">

        <h5 class="mb-0"><i class="fas fa-umbrella"></i> Gestión de productos</h5>

        <button class="btn btn-primary float-right" id="createproductoBtn" data-toggle="modal" data-target="#productoModal">Nuevo productoo</button>
  
    </div>
  
    <div class="card-body">
  
        <table class="table table-striped table-hover" id="productosTable">
   
            <thead>
  
                <tr>
   
                    <th>ID</th>
   
                    <th>Nombre</th>
   
                    <th>Descripción</th>
   
                    <th>Precio</th>
   
                    <th>Stock</th>
   
                    <th>Categoría</th>
   
                    <th>Acciones</th>
   
                </tr>
   
            </thead>
   
            <tbody>
   
                <!-- Datos se cargarán via AJAX -->
   
            </tbody>
   
        </table>
   
    </div>

</div>

<!-- Modal para crear productos -->

<div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
 
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-light">

                <h5 class="modal-title" id="productoModalTitle"><i class="fas fa-umbrella"></i> Nuevo productoo</h5>

                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span>

                 </button>

            </div>

             <form method="POST" id="form_productos" action="{{ url('productos') }}">
              
                <div class="modal-body">

                    <input type="hidden" id="productoId">

                    <div class="mb-3">

                        <label for="productoName" class="form-label">Nombre</label>

                        <input type="text" class="form-control" id="productoName" name="name" required>

                        <div class="invalid-feedback"></div>

                    </div>

                    <div class="mb-3">

                        <label for="productoDescription" class="form-label">Descripción</label>

                        <textarea class="form-control" id="productoDescription" name="description" rows="3"></textarea>

                        <div class="invalid-feedback"></div>

                    </div>

                      <div class="mb-3">

                        <label for="unidaMedida" class="form-label">Unidad de medida</label>

                        <input type="text"  class="form-control" id="unidaMedida" name="unidaMedida" required>

                       <div class="invalid-feedback"></div>

                     </div>  


                    <div class="mb-3">

                        <label for="precioCompra" class="form-label">Precio de compra</label>

                        <input type="number"  class="form-control" id="precioCompra" name="precioCompra" required>

                        <div class="invalid-feedback"></div>
                    </div>

                     <div class="mb-3">
                         <label for="precioVenta" class="form-label">Precio de venta</label>

                        <input type="number"  class="form-control" id="precioVenta" name="precioVenta" required>

                        <div class="invalid-feedback"></div>

                    </div>

                    <div class="mb-3">

                        <label for="Stock" class="form-label">Stock</label>

                        <input type="number" class="form-control" id="stock" name="stock" required>

                        <div class="invalid-feedback"></div>

                         <label for="stock_minimo" class="form-label">Stock minimo</label>

                        <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required>

                        <div class="invalid-feedback"></div>

                    </div>

                    <div class="mb-3">

                        <label for="productoCategory" class="form-label">Categoría</label>

                        <select class="form-select" id="productoCategory" name="category_id" required>

                            <option value="">Seleccione una categoría</option>

                            <!-- Las opciones se cargarán via AJAX -->

                           <option value="Carlos Martinez Rojas">Carlos Martinez Rojas</option>
                            <option value="Luisa Hernández Solis">Luisa Hernandez Solis</option>
                            <option value="Adelaida Forero Cardona">Adelaida Forero Cardona</option>
                            <option value="Rosario Jaramillo Torres">Rosario jaramillo Torres</option>
                         

                            
                        </select>

                        <div class="invalid-feedback"></div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>


                    <button type="submit" id="crear_producto" name="crear_producto" class="btn btn-primary">Guardar</button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- Modal de confirmación para eliminar productoo -->

<div class="modal fade" id="confirmproductoDeleteModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Confirmar Eliminación</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                ¿Estás seguro de que deseas eliminar este productoo?

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-danger" id="confirmproductoDeleteBtn">Eliminar</button>

            </div>

        </div>

    </div>

</div>

@section('scripts')

<script>

$(document).ready(function() {

// Variables

let editingproductoId = null;

let deleteproductoId = null;

    // Cargar productos

    function loadproductos() {
    
        $.get(' ', function(data) {
        
            let rows = '';
            data.forEach(producto => {
                rows += `
                    <tr>
                        <td>${producto.id}</td>
                        <td>${producto.nombre}</td>
                        <td>${producto.description || ''}</td>
                        <td>${producto.precio_compra}</td>
                         <td>${producto.precio_venta}</td>
                        <td>${producto.stock}</td>
                        <td>${producto.category ? producto.category.name : 'Sin categoría'}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-producto" data-id="${producto.id}">Editar</button>
                            <button class="btn btn-sm btn-danger delete-producto" data-id="${producto.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            $('#productosTable tbody').html(rows);
        });
    }

    // Cargar categorías para el select
    function loadCategories() {
        $.get(' ', function(data) {
            let options = '<option value="">Seleccione una categoría</option>';
            data.forEach(category => {
                options += `<option value="${category.id}">${category.name}</option>`;
            });
            $('#productoCategory').html(options);
        });
    }

/*
    // Mostrar modal para crear/editar productoo
    $('#createproductoBtn').click(function() {
        editingproductoId = null;
        $('#productoModalTitle').text('Nuevo productoo');
        $('#productoForm')[0].reset();
        $('#productoId').val('');
        $('.invalid-feedback').text('').parent().removeClass('has-error');
        loadCategories();
        $('#productoModal').modal('show');
    });

    // Editar productoo
    $(document).on('click', '.edit-producto', function() {
        editingproductoId = $(this).data('id');
        $('#productoModalTitle').text('Editar productoo');
        $('.invalid-feedback').text('').parent().removeClass('has-error');
        
        $.get(`/productos/${editingproductoId}`, function(data) {
            $('#productoId').val(data.id);
            $('#productoName').val(data.name);
            $('#productoDescription').val(data.description);
            $('#productoPrice').val(data.price);
            $('#productoStock').val(data.stock);
            
            // Cargar categorías y seleccionar la correcta
            loadCategories();
            setTimeout(() => {
                $('#productoCategory').val(data.category_id);
            }, 200);
            
            $('#productoModal').modal('show');
        });
    });

*/


    // =========================================

    /// GUARDAR REGISTROS DE DATOS DE PROFESIONALES

    // =========================================

    $('#form_productos').off('submit').on('submit', function(event) {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      /* Configurar botón submit con spinner */
      let btn = $('#crear_producto')
      let existingHTML = btn.html() //store exiting button HTML
      //Add loading message and spinner
      $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)
      setTimeout(function() {
        $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
      }, 5000) //5 seconds
      $('#crear_producto').attr('disabled', true);

      event.preventDefault();

      try {

        $.ajax({
          url: "crear_producto",
          method: "POST",
          data: $(this).serialize(),
          dataType: "json",
          success: function(data) {
            table.ajax.reload();
            $('#crear_producto').prop("required", true);
            // $('#selectBuscarCliente').html("");

            $('#form_productos')[0].reset();
            $('#form_productos').modal('hide');

            //   table.ajax.reload();
            //   location.reload(true);
            toastr["success"]("registro creado correctamente.");

          }
        });
      } catch (e) {
        toastr["danger"]("Se ha presentado un error.", "Información");
      }
    });


/*
    // Enviar formulario de productoo
    $('#productoForm').submit(function(e) {
        e.preventDefault();
        
        let url = editingproductoId ? `/productos/${editingproductoId}` : '/productos';
        let method = editingproductoId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#productoModal').modal('hide');
                loadproductos();
                toastr.success('productoo guardado correctamente');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    let input = $(`#producto${field.charAt(0).toUpperCase() + field.slice(1)}`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors[field][0]);
                }
            }
        });
    });

*/

    // Mostrar modal de confirmación para eliminar productoo
    $(document).on('click', '.delete-producto', function() {
        deleteproductoId = $(this).data('id');
        $('#confirmproductoDeleteModal').modal('show');
    });

    // Confirmar eliminación de productoo
    $('#confirmproductoDeleteBtn').click(function() {
        $.ajax({
            url: `/productos/${deleteproductoId}`,
            method: 'DELETE',
            success: function(response) {
                $('#confirmproductoDeleteModal').modal('hide');
                loadproductos();
                toastr.success('productoo eliminado correctamente');
            }
        });
    });

    // Cargar productos al inicio
    loadproductos();
});
</script>
@endsection
@endsection