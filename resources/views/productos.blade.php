@extends('layouts.app')

@section('content')

<br>

<div class="card">

    <div class="card-header d-flex bg-light justify-content-between align-items-right">

        <h5 class="mb-0"><i class="fas fa-umbrella"></i> Gestión de productos</h5>

        <button class="btn btn-primary float-right" id="createproductoBtn" data-toggle="modal" data-target="#productoModal">Nuevo producto</button>
  
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
    <div class="modal-dialog modal-lg"> <!-- Añadido modal-lg para más ancho -->
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="productoModalTitle"><i class="fas fa-umbrella"></i> Nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('productos') }}" method="POST" id="form_productos">
                @csrf 
                <div class="modal-body">
                    <input type="hidden" id="id_producto">
                    
                    <div class="row"> <!-- Fila para agrupar campos horizontalmente -->
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="productoName" class="col-sm-4 col-form-label">Nombre</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="productoName" name="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="productoDescription" class="col-sm-4 col-form-label">Descripción</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="productoDescription" name="description" rows="2"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="unidaMedida" class="col-sm-4 col-form-label">Unidad de medida</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="unidaMedida" name="unidaMedida" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="ubicacion" class="col-sm-4 col-form-label">Ubicación</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="Marca" class="col-sm-4 col-form-label">Marca</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="marca" name="marca" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                             <div class="form-group row mb-3">
                                <label for="Peso" class="col-sm-4 col-form-label">Peso</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="peso" name="peso" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="cantidad" class="col-sm-4 col-form-label">Cantidad</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="precioCompra" class="col-sm-4 col-form-label">Precio compra</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="precio_compra" name="precio_compra" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="precioVenta" class="col-sm-4 col-form-label">Precio venta</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="Codigo" class="col-sm-4 col-form-label">Código</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="codigo" name="codigo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                              <div class="form-group row mb-3">
                                <label for="Stock" class="col-sm-4 col-form-label">Stock</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock" name="stock" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock_minimo" class="col-sm-4 col-form-label">Stock mínimo</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                             <div class="form-group row mb-3">
                               <label for="Categoria" class="col-sm-4 col-form-label">Categoría</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="categoria" name="categoria" required>
                                    <option value="">Seleccione una categoría</option>
                                    <option value="Herramientas">Herramientas</option>
                                    <option value="Fijación y sujeción">Fijación y sujeción</option>
                                    <option value="Materiales de construcción">Materiales de construcción</option>
                                    <option value="Electricidad">Electricidad</option>
                                    <option value="Seguridad y protección">Seguridad y protección</option>
                                    <option value="Pinturas y acabados">Pintura y acabados</option>
                                        <option value="Almacenamiento y organización">Almacenamiento y organización</option>

                                </select>
                                <div class="invalid-feedback"></div>
                             </div>
                         </div>
                        
                    </div>
                    
                    <!-- Imagem (ocupa todo el ancho) -->
                       <div class="col-md-12">
                            <div class="form-group row mb-3">
                                <label for="imagen" class="col-sm-2 col-form-label">Imagen</label>
                                <div class="col-sm-10">
                                      <input class="form-control" type="file" id="imagen" name="imagen">
                                  <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
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

/*
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
*/

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
          url: "/productos",
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