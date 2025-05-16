@extends('layouts.app')

@section('content')

<br>

<div class="card">

    <div class="card-header d-flex bg-light justify-content-between align-items-right">

        <h5 class="mb-0"><i class="fas fa-umbrella"></i> Gestión de Productos</h5>

        <button class="btn btn-primary float-right" id="createProductBtn" data-toggle="modal" data-target="#productModal">Nuevo Producto</button>
  
    </div>
  
    <div class="card-body">
  
        <table class="table table-striped table-hover" id="productsTable">
   
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

<!-- Modal para crear/editar productos -->

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-light">

                <h5 class="modal-title" id="productModalTitle"><i class="fas fa-umbrella"></i> Nuevo Producto</h5>

                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span>

                 </button>

            </div>

            <form id="productForm">

                <div class="modal-body">

                    <input type="hidden" id="productId">

                    <div class="mb-3">

                        <label for="productName" class="form-label">Nombre</label>

                        <input type="text" class="form-control" id="productName" name="name" required>

                        <div class="invalid-feedback"></div>

                    </div>

                    <div class="mb-3">

                        <label for="productDescription" class="form-label">Descripción</label>

                        <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>

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

                        <label for="productCategory" class="form-label">Categoría</label>

                        <select class="form-select" id="productCategory" name="category_id" required>

                            <option value="">Seleccione una categoría</option>

                            <!-- Las opciones se cargarán via AJAX -->
                        </select>

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

<!-- Modal de confirmación para eliminar producto -->

<div class="modal fade" id="confirmProductDeleteModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Confirmar Eliminación</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                ¿Estás seguro de que deseas eliminar este producto?

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-danger" id="confirmProductDeleteBtn">Eliminar</button>

            </div>

        </div>

    </div>

</div>

@section('scripts')

<script>

$(document).ready(function() {

// Variables

let editingProductId = null;

let deleteProductId = null;

    // Cargar productos

    function loadProducts() {
    
        $.get(' ', function(data) {
        
            let rows = '';
            data.forEach(product => {
                rows += `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.name}</td>
                        <td>${product.description || ''}</td>
                        <td>${product.precio_compra}</td>
                         <td>${product.precio_venta}</td>
                        <td>${product.stock}</td>
                        <td>${product.category ? product.category.name : 'Sin categoría'}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-product" data-id="${product.id}">Editar</button>
                            <button class="btn btn-sm btn-danger delete-product" data-id="${product.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            $('#productsTable tbody').html(rows);
        });
    }

    // Cargar categorías para el select
    function loadCategories() {
        $.get(' ', function(data) {
            let options = '<option value="">Seleccione una categoría</option>';
            data.forEach(category => {
                options += `<option value="${category.id}">${category.name}</option>`;
            });
            $('#productCategory').html(options);
        });
    }

    // Mostrar modal para crear/editar producto
    $('#createProductBtn').click(function() {
        editingProductId = null;
        $('#productModalTitle').text('Nuevo Producto');
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('.invalid-feedback').text('').parent().removeClass('has-error');
        loadCategories();
        $('#productModal').modal('show');
    });

    // Editar producto
    $(document).on('click', '.edit-product', function() {
        editingProductId = $(this).data('id');
        $('#productModalTitle').text('Editar Producto');
        $('.invalid-feedback').text('').parent().removeClass('has-error');
        
        $.get(`/products/${editingProductId}`, function(data) {
            $('#productId').val(data.id);
            $('#productName').val(data.name);
            $('#productDescription').val(data.description);
            $('#productPrice').val(data.price);
            $('#productStock').val(data.stock);
            
            // Cargar categorías y seleccionar la correcta
            loadCategories();
            setTimeout(() => {
                $('#productCategory').val(data.category_id);
            }, 200);
            
            $('#productModal').modal('show');
        });
    });

    // Enviar formulario de producto
    $('#productForm').submit(function(e) {
        e.preventDefault();
        
        let url = editingProductId ? `/products/${editingProductId}` : '/products';
        let method = editingProductId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#productModal').modal('hide');
                loadProducts();
                toastr.success('Producto guardado correctamente');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    let input = $(`#product${field.charAt(0).toUpperCase() + field.slice(1)}`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors[field][0]);
                }
            }
        });
    });

    // Mostrar modal de confirmación para eliminar producto
    $(document).on('click', '.delete-product', function() {
        deleteProductId = $(this).data('id');
        $('#confirmProductDeleteModal').modal('show');
    });

    // Confirmar eliminación de producto
    $('#confirmProductDeleteBtn').click(function() {
        $.ajax({
            url: `/products/${deleteProductId}`,
            method: 'DELETE',
            success: function(response) {
                $('#confirmProductDeleteModal').modal('hide');
                loadProducts();
                toastr.success('Producto eliminado correctamente');
            }
        });
    });

    // Cargar productos al inicio
    loadProducts();
});
</script>
@endsection
@endsection