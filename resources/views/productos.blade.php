@extends('layouts.app')

@section('content')

<br>


<div class="card">

    <div class="card-header d-flex bg-light justify-content-between align-items-right">

        <h5 class="mb-0"><i class="fas fa-umbrella"></i> Gestión de productos</h5>

        <button class="btn btn-primary float-right" id="createproductoBtn" data-toggle="modal" data-target="#modalproductos"><i class="fa fa-plus" aria-hidden="true"></i>  Nuevo producto</button>
  
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
<div class="modal fade" id="modalproductos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Añadido modal-lg para más ancho -->
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalproductosTitle"><i class="fas fa-umbrella"></i> Nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_guardar_productos" action="{{ url('productos') }}" >
            @csrf
                <div class="modal-body">
                    <input type="hidden" id="id_categoria" name="id_categoria" value="1">
                     <input type="hidden" id="id_proveedor" name="id_proveedor" value="1">
                    
                    <div class="row"> <!-- Fila para agrupar campos horizontalmente -->
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="Nombre" class="col-sm-4 col-form-label">Nombre</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="descripcion" class="col-sm-4 col-form-label">Descripción</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="unida_medida" class="col-sm-4 col-form-label">Unidad de medida</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" required>
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
                                <label for="imagen" class="col-sm-4 col-form-label">Imagen</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" id="imagen" name="imagen" >
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
                                <label for="Precio_compra" class="col-sm-4 col-form-label">Precio compra</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="precio_compra" name="precio_compra" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="precio_venta" class="col-sm-4 col-form-label">Precio venta</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="Codigo" class="col-sm-4 col-form-label">Código</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="codigo" name="codigo" required>
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

                               <div class="form-group row mb-3">
                                    <label for="proveedor" class="col-sm-4 col-form-label">Proveedor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                               </div>
                         </div>
                    </div>                    
                  
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="crear_producto" name="crear_producto" class="btn btn-primary loader">

                         <span id="btnText">Guardar</span>
              <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>

                    </button>
                    <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::check() ? Auth::user()->id : null}}" readonly>
              
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



<script>


$(document).ready(function() {
    $('#form_guardar_productos').on('submit', function(event) {
        // 1. Prevenir el comportamiento por defecto del formulario
        event.preventDefault();
        
    const btnSubmit = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    
    // Deshabilitar botón y mostrar spinner
    btnSubmit.disabled = true;
    btnText.textContent = 'Procesando...';
    btnSpinner.classList.remove('d-none');

        // 3. Configurar AJAX
        $.ajax({
            url: "/productos",
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Restaurar botón
                btn.html(originalText).prop('disabled', false);
                
                // Resetear formulario y cerrar modal
                $('#form_guardar_productos')[0].reset();
                $('#modalproductos').modal('hide');
                
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