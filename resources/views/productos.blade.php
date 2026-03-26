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
        #tablaProductos {
            width: 100% !important;
        }

    .producto-imagen-tabla {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 2px;
        background: white;
        transition: transform 0.2s;
    }
    
    .producto-imagen-tabla:hover {
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
    <div class="card-header bg-light d-flex justify-content-between align-items-right">
        <h5 class="mb-0"><i class="fas fa-umbrella"></i> Gestión de productos</h5>
      <!--  <button class="btn btn-primary float-right" id="BtnCrearProducto" data-toggle="modal" data-target="#modalProductos"><i class="fa fa-plus" aria-hidden="true"></i>  Nuevo producto</button> -->
     
            <div class="pull-right">
                 <button type="button" id="registrar_producto" class="btn btn-primary" data-toggle="modal" data-target="#modalProductos">
                        <span class="fa fa-plus" ></span>  
                        Registrar producto
                 </button>  &nbsp;
            </div> 
     </div>
  
    <div class="card-body">  
    <div class="table-responsive">
        <table class="table table-hover" id="tablaProductos" style="width:100%;font-size:12.5px;">   
            <thead>  
               <tr>   
                    <th>Código</th>
                    <th>Nombre</th>   
                    <th>Descripción</th>   
                    <th>Precio Venta</th>   
                    <th>Stock</th>
                    <th>Stock min</th>
                    <th>Ubicación</th>   
                    <th>Frecuente</th>  
                    <th>Imagen</th>      
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
 Modal para crear productos 
======================================  -->

<div class="modal fade" id="modalProductos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-umbrella"></i> Nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_guardar_productos" enctype="multipart/form-data" action="{{ url('productos') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="codigo" class="col-sm-4 col-form-label">Código *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="codigo" name="codigo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="nombre" class="col-sm-4 col-form-label">Nombre *</label>
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
                                <label for="marca" class="col-sm-4 col-form-label">Marca</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="marca" name="marca">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="id_categoria" class="col-sm-4 col-form-label">Categoría *</label>
                                <div class="col-sm-8">
                                    <select id="id_categoria" name="id_categoria" class="form-control" required>
                                        <option value="">Seleccione categoría</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="id_proveedor" class="col-sm-4 col-form-label">Proveedor</label>
                                <div class="col-sm-8">
                                    <select id="id_proveedor" name="id_proveedor" class="form-control">
                                        <option value="">Seleccione proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id_proveedor }}">{{ $proveedor->razon_socal}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="imagen" class="col-sm-4 col-form-label">Imagen</label>
                                <div class="col-sm-8">
                                    <input type="file" id="imagen" name="imagen" class="form-control" 
                                           accept=".webp,.jpeg,.jpg,.png,.gif"
                                           onchange="manejarSeleccionArchivo(this, document.getElementById('preview'), document.getElementById('mensaje-archivo'))">
                                    <small class="form-text text-muted">
                                        Formatos: JPEG, PNG, JPG, GIF, WEBP. Máximo 2MB.
                                    </small>
                                    <div id="mensaje-archivo" class="small"></div>
                                    <img id="preview" style="max-width: 100px; max-height: 100px; margin-top: 5px; display: none;">
                                </div>
                            </div>
                            


                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="precio_venta" class="col-sm-4 col-form-label">Precio venta *</label>
                                <div class="col-sm-8">
                                    <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock" class="col-sm-4 col-form-label">Stock actual *</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock" name="stock" value="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock_minimo" class="col-sm-4 col-form-label">Stock mínimo *</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="unidad_medida" class="col-sm-4 col-form-label">Unidad medida *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" placeholder="Ej: UNIDAD, KG, LITRO" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="ubicacion" class="col-sm-4 col-form-label">Ubicación</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ej: ESTANTE A1">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="frecuente" class="col-sm-4 col-form-label">Producto frecuente</label>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="frecuente" name="frecuente" value="1">
                                        <label class="form-check-label" for="frecuente">Marcar como producto de uso frecuente</label>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <!-- Campo oculto para activo (siempre true por defecto) -->
                            <input type="hidden" name="activo" value="1">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="BtnGuardar_producto">Guardar producto</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================= 
 Modal para editar productos 
======================================  -->

<!-- Modal para editar productos -->
<div class="modal fade" id="modalEditarProducto" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Editar producto: <span id="nombre_producto_titulo_2" style="color:red"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_editar_productos" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="id_producto_producto" name="id_producto">
                    
                    <div class="row">
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="codigo_producto" class="col-sm-4 col-form-label">Código *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="codigo_producto" name="codigo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="nombre_producto" class="col-sm-4 col-form-label">Nombre *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre_producto" name="nombre" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="descripcion_producto" class="col-sm-4 col-form-label">Descripción</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="descripcion_producto" name="descripcion" rows="2"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="marca_producto" class="col-sm-4 col-form-label">Marca</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="marca_producto" name="marca">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="id_categoria_producto" class="col-sm-4 col-form-label">Categoría *</label>
                                <div class="col-sm-8">
                                    <select id="id_categoria_producto" name="id_categoria" class="form-control" required>
                                        <option value="">Seleccione categoría</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="id_proveedor_producto" class="col-sm-4 col-form-label">Proveedor</label>
                                <div class="col-sm-8">
                                    <select id="id_proveedor_producto" name="id_proveedor" class="form-control">
                                        <option value="">Seleccione proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id_proveedor }}">{{ $proveedor->razon_social }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="imagen_editar" class="col-sm-4 col-form-label">Imagen</label>
                                <div class="col-sm-8">
                                    <input type="file" id="imagen_editar" name="imagen" class="form-control" 
                                           accept=".webp,.jpeg,.jpg,.png,.gif"
                                           onchange="previewImage(this, 'preview_editar')">
                                    <small class="form-text text-muted">
                                        Formatos: JPEG, PNG, JPG, GIF, WEBP. Máximo 2MB.
                                    </small>
                                    <div id="mensaje-archivo-editar" class="small"></div>
                                    
                                    <!-- Preview de imagen actual y nueva -->
                                    <div class="mt-2">
                                        <img id="preview_imagen_actual" style="max-width: 100px; max-height: 100px; display: none;">
                                        <img id="preview_editar" style="max-width: 100px; max-height: 100px; display: none;">
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="activo" value="1">
                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="precio_venta_producto" class="col-sm-4 col-form-label">Precio venta *</label>
                                <div class="col-sm-8">
                                    <input type="number" step="0.01" class="form-control" id="precio_venta_producto" name="precio_venta" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock_producto" class="col-sm-4 col-form-label">Stock actual *</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock_producto" name="stock" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock_minimo_producto" class="col-sm-4 col-form-label">Stock mínimo *</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock_minimo_producto" name="stock_minimo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="unidad_medida_producto" class="col-sm-4 col-form-label">Unidad medida *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="unidad_medida_producto" name="unidad_medida" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="ubicacion_producto" class="col-sm-4 col-form-label">Ubicación</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="ubicacion_producto" name="ubicacion">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="frecuente_producto" class="col-sm-4 col-form-label">Producto frecuente</label>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="frecuente_producto" name="frecuente" value="1">
                                        <label class="form-check-label" for="frecuente_producto">Marcar como producto de uso frecuente</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="BtnEditar_producto">Actualizar producto</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================= 
 Modal para ver productos 
======================================  -->

<!-- Modal para ver productos -->
<div class="modal fade" id="modalVerProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Datos del producto: <span id="nombre_producto_titulo" style="color:red"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-md-6">
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Código:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_codigo" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Nombre:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_nombre" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Descripción:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control-plaintext" id="ver_descripcion" rows="2" readonly></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Marca:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_marca" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Categoría:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_categoria_nombre" readonly>
                                <small class="text-muted" id="ver_categoria_id"></small>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Proveedor:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_proveedor_nombre" readonly>
                                <small class="text-muted" id="ver_proveedor_id"></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna 2 -->
                    <div class="col-md-6">
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Precio venta:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_precio_venta" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Stock actual:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_stock" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Stock mínimo:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_stock_minimo" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Unidad medida:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_unidad_medida" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Ubicación:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_ubicacion" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Frecuente:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control-plaintext" id="ver_frecuente" readonly>
                            </div>
                        </div>
                                                
                        <div class="form-group row mb-3">
                            <label class="col-sm-4 col-form-label font-weight-bold">Imagen:</label>
                            <div class="col-sm-8">
                                <img id="previewVerProducto" style="max-width: 100px; max-height: 100px; display: none;">
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



<!-- ===================================================

 DATATABLE PRODUCTOS

======================================================= --->

@push('js')

<script>
    

function previewImage(input, previewId) {
    console.log('previewImage ejecutada');
    
    const preview = document.getElementById(previewId);
    const mensaje = document.getElementById('mensaje-archivo-editar');
    
    if (!preview) {
        console.error('No se encontró:', previewId);
        return;
    }
    
    if (!input.files || !input.files[0]) {
        preview.style.display = 'none';
        return;
    }
    
    const file = input.files[0];
    
    // Validaciones simples
    const tiposPermitidos = ['image/webp', 'image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!tiposPermitidos.includes(file.type)) {
        if (mensaje) {
            mensaje.textContent = 'Tipo de archivo no permitido';
            mensaje.className = 'text-danger';
        }
        input.value = '';
        preview.style.display = 'none';
        return;
    }
    
    if (file.size > 2 * 1024 * 1024) {
        if (mensaje) {
            mensaje.textContent = 'Archivo demasiado grande (máx. 2MB)';
            mensaje.className = 'text-danger';
        }
        input.value = '';
        preview.style.display = 'none';
        return;
    }
    
    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        
        // Ocultar imagen actual en edición
        if (previewId === 'preview_editar') {
            const previewActual = document.getElementById('preview_imagen_actual');
            if (previewActual) previewActual.style.display = 'none';
        }
        
        if (mensaje) {
            mensaje.textContent = 'Imagen válida';
            mensaje.className = 'text-success';
        }
    };
    reader.readAsDataURL(file);
}


$(document).ready(function() {

    
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
  
  // Imagen por defecto (definida una sola vez)
    const defaultProductImage = "{{ asset('storage/images/default-product.png') }}";
    
    window.table = $('#tablaProductos').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        filter: true,
        responsive: true,
        type: "GET",
        ajax: 'productos',
        columns: [
            {
                data: 'codigo',
                name: 'codigo'
            },
            {
                data: 'nombre',
                name: 'nombre'
            },
            {
                data: 'descripcion',
                name: 'descripcion'
            },
            {
                data: 'precio_venta',
                name: 'precio_venta',
                render: function(data) {
                    return '$' + parseFloat(data || 0).toFixed(2);
                }
            },
            {
                data: 'stock_actual',
                name: 'stock_actual',
                render: function(data, type, row) {
                    let badgeClass = data <= row.stock_minimo ? 'badge-danger' : 'badge-success';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            {
                data: 'stock_minimo',
                name: 'stock_minimo'
            },
            {
                data: 'ubicacion',
                name: 'ubicacion'
            },
            {
                data: 'frecuente',
                name: 'frecuente',
                render: function(data) {
                    if (data == 1 || data == true) {
                        return '<span class="badge badge-warning"><i class="fa fa-star"></i> Frecuente</span>';
                    } else {
                        return '<span class="badge badge-secondary">Normal</span>';
                    }
                }
            },
            {
                data: 'imagen',
                name: 'imagen',
                render: function(data) {
                    if (data) {
                        // Limpiar la ruta
                        let imagePath = data.replace('storage/', '');
                        let imageUrl = "{{ asset('storage') }}/" + imagePath;
                        
                        return '<img src="' + imageUrl + '" alt="Producto" class="producto-imagen-tabla" onerror="this.src=\'' + defaultProductImage + '\'">';
                    } else {
                        return '<img src="' + defaultProductImage + '" alt="Sin imagen" class="producto-imagen-tabla">';
                    }
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                width: '80px'  
            }
        ],

      order: [
        [0, 'desc']
      ],


       columnDefs: [{
                "orderable": false,
                "render": $.fn.dataTable.render.number('.'),
                "targets": [3],
                "className": "dt-body-right",
            }],

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

    });


  // =============================================
    // 1. LIMPIAR TODOS LOS EVENTOS ANTERIORES
    // =============================================
    $(document).off('click', '#btnRegistrarProducto');
    $(document).off('submit', '#formAgregarProducto');
    $(document).off('hidden.bs.modal', '#modalAgregarProducto');
    $(document).off('shown.bs.modal', '#modalAgregarProducto');
    $(document).off('change', '#imagen');
    
    // =============================================
    // 2. FUNCIÓN PARA ABRIR MODAL (SIMPLE Y DIRECTA)
    // =============================================
    function abrirModalProducto() {
        console.log('📤 Abriendo modal de producto...');
        
        // 1. Resetear formulario COMPLETAMENTE
        $('#formAgregarProducto')[0].reset();
        
        // 2. Limpiar vista previa de imagen
        $('#previewImagen').attr('src', '').hide();
        $('.custom-file-label').text('Seleccionar imagen...');
        
        // 3. Limpiar errores
        $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');
        
        // 4. Mostrar modal
        $('#modalAgregarProducto').modal('show');
        
        // 5. Forzar focus después de 300ms
        setTimeout(function() {
            $('#nombre').focus().select();
            console.log('🔍 Foco puesto en campo Nombre');
        }, 300);
    }
    
    // =============================================
    // 3. BOTÓN PARA ABRIR MODAL (UN SOLO CLIC)
    // =============================================
    $(document).on('click', '#btnRegistrarProducto', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation(); // IMPORTANTE: Detiene otros eventos
        console.log('🖱️ Clic en Registrar Producto');
        
        abrirModalProducto();
        return false;
    });
    


// =============================================
    // 4. AL MOSTRAR EL MODAL (ENFOCAR AUTOMÁTICAMENTE)
    // =============================================
    $('#modalAgregarProducto').on('shown.bs.modal', function() {
        console.log('👁️ Modal mostrado, enfocando campo...');
        
        // Pequeño retraso para asegurar que el modal esté completamente visible
        setTimeout(function() {
            $('#nombre').focus().select();
            
            // Forzar visualmente el foco (estilo)
            $('#nombre').addClass('focused-field');
            
            console.log('✅ Campo Nombre enfocado');
        }, 100);
    });
 
$('#form_guardar_productos').off('submit').on('submit', function (event) {

   event.preventDefault();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
/* Configurar botón submit con spinner */
let btn = $('#BtnGuardar_producto') 
    let existingHTML =btn.html() //store exiting button HTML
    //Add loading message and spinner
    $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)
    setTimeout(function() {
      $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
    },5000) 
        $('#BtnGuardar_producto').attr('disabled', true);

      var form = document.getElementById('form_guardar_productos');
      var formData = new FormData(form);         

        try {
        $.ajax({
            url: "/productos-guardar",
            method: "POST",
            data: formData,
            processData: false, 
            contentType: false,  
            dataType: "json",
          success: function(data) {
           
            table.ajax.reload();   

            $('#form_guardar_productos')[0].reset();      
            
            // Cerrar modal manualmente y limpiar estado Bootstrap
            $('#modalProductos').modal('hide');
            $('#modalProductos').removeClass('show');
            $('#modalProductos').css('display', 'none');
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '0');
            $('.modal-backdrop').remove();
            
            // Resetear preview de imagen
            $('#preview').hide().attr('src', '');
            $('#mensaje-archivo').text('').removeClass('text-success text-danger');

            toastr["success"]("Registro creado correctamente.");             
        }
         });
        } catch(e) {
          toastr["danger"]("Se ha presentado un error.", "Información");
          }
    }); 


// ======================================
// CARGAR PROVEEDOR VIA AJAX
// =======================================

$(document).ready(function() {
    // Cargar proveedores vía AJAX
    $.ajax({
        url: "{{ route('proveedores.lista') }}", // Necesitarás crear esta ruta
        type: "GET",
        success: function(data) {
            let select = $('#id_proveedor');
            select.empty().append('<option value="">Seleccione proveedor</option>');
            
            $.each(data, function(index, proveedor) {
                select.append('<option value="' + proveedor.id_proveedor + '">' + 
                             proveedor.razon_social + '</option>');
            });
        },
        error: function() {
            $('#id_proveedor').empty().append('<option value="">Error al cargar proveedores</option>');
        }
    });
});



// =========================================

/// ELIMINAR REGISTROS DE PRODUCTOS

// =========================================   


  $(document).on('click', '.eliminarProducto', function (event) {
    event.preventDefault();
    
    // Obtener datos del producto desde los data attributes
    let id_producto = $(this).data('id');
    let productName = $(this).data('nombre');
    
    // Verificar que tenemos los datos necesarios
    if (!id_producto) {
        console.error('No se encontró el ID del producto');
        return;
    }
    
    // Usar la función de confirmación pasando ambos parámetros
    confirmarEliminacion(id_producto, nombre_producto);
});

// ✅ FUNCIÓN SEGURA PARA CONFIRMAR ELIMINACIÓN
function confirmarEliminacion(id_producto, productName) {
    // Verificar que SweetAlert2 esté cargado
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está disponible. Usando confirm nativo.');
        if (confirm(`¿Estás seguro de eliminar el producto "${productName}"?`)) {
            eliminarProducto(id_producto);
        }
        return;
    }

    // Usar SweetAlert2 con el nombre del producto
    Swal.fire({
        title: '¿Estás seguro?',
        html: `Esta acción no se puede deshacer.`,
      //  html: `Estás a punto de eliminar el producto: <strong>${productName}</strong><br><br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        backdrop: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            popup: 'sweetalert-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarProducto(id_producto);
        }
    });
}

// ✅ FUNCIÓN PARA ELIMINAR EL PRODUCTO
function eliminarProducto(id_producto) {
    console.log('Eliminando producto ID:', id_producto);
    
    // Mostrar loading
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Eliminando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    $.ajax({
      url: "/eliminar_producto/" + id_producto,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content') // Obtener token CSRF
        },
        dataType: 'json',
        success: function(response) {
            // Cerrar loading
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            if (response.success) {
                // Mostrar éxito
                Swal.fire({
                    title: '¡Eliminado!',
                    text: response.message || 'Producto eliminado correctamente',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Recargar DataTable o actualizar la vista
                if (window.table && typeof window.table.ajax !== 'undefined') {
                    window.table.ajax.reload(null, false);
                } else {
                    // Si no usas DataTable, recargar la página después de un tiempo
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
                
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'Error al eliminar el producto',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        },
        error: function(xhr, status, error) {
            // Cerrar loading
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            console.error('Error al eliminar:', xhr);
            
            let errorMessage = 'Error al eliminar el producto';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Producto no encontrado';
            } else if (xhr.status === 500) {
                errorMessage = 'Error interno del servidor';
            }
            
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    });
}

});


</script>

<script>
// =========================================
/// VER REGISTROS DEL PRODUCTO - CORREGIDO
// =========================================
// =========================================
/// VER REGISTROS DEL PRODUCTO - CORREGIDO
// =========================================
$(document).on('click', '.verProducto', function(e) {    
    e.preventDefault();
    
    let id_producto = $(this).data('id');
    console.log('Ver producto ID:', id_producto);
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: "{{ url('mostrar_producto') }}/" + id_producto,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Datos recibidos:', data);
            
            Swal.close();
            
            // Llenar campos del modal VER
            $('#ver_codigo').val(data.codigo || '');
            $('#ver_nombre').val(data.nombre || '');
            $('#nombre_producto_titulo').text(data.nombre || '');
            $('#ver_descripcion').val(data.descripcion || '');
            $('#ver_marca').val(data.marca || '');
            
            // Categoría: mostrar nombre y ID
            let categoriaNombre = data.categoria ? data.categoria.nombre : (data.categoria_nombre || 'Sin categoría');
            let categoriaId = data.id_categoria || (data.categoria ? data.categoria.id_categoria : '');
            
            $('#ver_categoria_nombre').val(categoriaNombre);
            if (categoriaId) {
                $('#ver_categoria_id').text('ID Categoría: ' + categoriaId);
            } else {
                $('#ver_categoria_id').text('');
            }
            
            // Proveedor: mostrar nombre y ID
            let proveedorNombre = data.proveedor ? data.proveedor.razon_social : (data.proveedor_nombre || 'Sin proveedor');
            let proveedorId = data.id_proveedor || (data.proveedor ? data.proveedor.id_proveedor : '');
            
            $('#ver_proveedor_nombre').val(proveedorNombre);
            if (proveedorId) {
                $('#ver_proveedor_id').text('ID Proveedor: ' + proveedorId);
            } else {
                $('#ver_proveedor_id').text('');
            }
            
            $('#ver_precio_venta').val('$' + parseFloat(data.precio_venta || 0).toFixed(2));
            $('#ver_stock').val(data.stock_actual || '0');
            $('#ver_stock_minimo').val(data.stock_minimo || '0');
            $('#ver_unidad_medida').val(data.unidad_medida || '');
            $('#ver_ubicacion').val(data.ubicacion || '');
            $('#ver_frecuente').val(data.frecuente ? 'Sí' : 'No');
            
            // Manejo de imagen
            const preview = $('#previewVerProducto');
            
            if(data.imagen && data.imagen.trim() !== '') {
                let imageUrl = data.imagen;
                if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
                    imageUrl = "{{ asset('') }}" + imageUrl;
                }
                preview.attr('src', imageUrl);
                preview.show();
            } else {
                preview.attr('src', "{{ asset('storage/images/default-product.png') }}");
                preview.show();
            }
            
            // Abrir modal
            $('#modalVerProducto').modal('show');
        },
        error: function(xhr) {
            Swal.close();
            console.error('Error:', xhr);
            
            let errorMessage = 'Error al cargar los datos del producto.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    });
});
</script>




<script>

  // =========================================
/// EDITAR REGISTROS DEL PRODUCTO
// =========================================

// =========================================
/// EDITAR REGISTROS DEL PRODUCTO - CORREGIDO
// =========================================

$(document).on('click', '.editarProducto', function(e) {    
    e.preventDefault();
    
    let id_producto = $(this).data('id');
    console.log('Editando producto ID:', id_producto);
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: "{{ url('editar_producto') }}/" + id_producto,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Datos del producto recibidos:', data);
            
            Swal.close();
            
            // SETEAR EL ID EN EL FORMULARIO
            $('#id_producto_producto').val(data.id_producto);
            
            // Llenar campos del modal
            $('#codigo_producto').val(data.codigo || '');
            $('#nombre_producto').val(data.nombre || '');
            $('#nombre_producto_titulo_2').text(data.nombre || '');
            $('#descripcion_producto').val(data.descripcion || '');
            $('#marca_producto').val(data.marca || '');
            $('#precio_venta_producto').val(data.precio_venta || '');
            $('#stock_producto').val(data.stock_actual || '');
            $('#stock_minimo_producto').val(data.stock_minimo || '');
            $('#unidad_medida_producto').val(data.unidad_medida || '');
            $('#ubicacion_producto').val(data.ubicacion || '');
            
            // Cargar categoría y proveedor con sus IDs
            if (data.id_categoria) {
                $('#id_categoria_producto').val(data.id_categoria);
            }
            
            if (data.id_proveedor) {
                $('#id_proveedor_producto').val(data.id_proveedor);
            }
            
            // Checkbox frecuente
            if (data.frecuente == 1) {
                $('#frecuente_producto').prop('checked', true);
            } else {
                $('#frecuente_producto').prop('checked', false);
            }

            // MANEJO DE IMAGEN
            const previewActual = $('#preview_imagen_actual');
            const previewNueva = $('#preview_editar');
            
            if(data.imagen && data.imagen.trim() !== '') {
                console.log('Imagen actual encontrada:', data.imagen);
                
                let imageUrl = data.imagen;
                if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
                    imageUrl = "{{ asset('') }}" + imageUrl;
                }
                
                console.log('URL de imagen actual:', imageUrl);
                
                // Mostrar imagen actual
                previewActual.attr('src', imageUrl);
                previewActual.css('display', 'block');
                previewNueva.hide();
                
                // Verificar si la imagen se carga correctamente
                previewActual.on('load', function() {
                    console.log('Imagen actual cargada correctamente');
                });
                
                previewActual.on('error', function() {
                    console.error('Error cargando imagen actual:', imageUrl);
                    previewActual.attr('src', "{{ asset('storage/images/default-product.png') }}");
                    previewActual.show();
                });
                
            } else {
                console.log('No hay imagen actual disponible');
                previewActual.hide();
                previewNueva.hide();
            }
            
            // Limpiar mensajes de error
            $('.invalid-feedback').empty();
            $('.is-invalid').removeClass('is-invalid');
            
            // Abrir modal
            $('#modalEditarProducto').modal('show');
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error en AJAX:', xhr);
            
            let errorMessage = 'Error al cargar los datos del producto.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    });
});

// =========================================
/// ACTUALIZAR PRODUCTO - USANDO POST
// =========================================
$(document).on('submit', '#form_editar_productos', function(event) {
    event.preventDefault();

    let id_producto = $('#id_producto_producto').val();
    
    if (!id_producto) {
        toastr.error("Error: ID del producto no encontrado");
        return;
    }

    // Configurar botón
    let btn = $('#BtnEditar_producto');
    let existingHTML = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Procesando...').prop('disabled', true);

    var formData = new FormData(this);
    // 👇 ELIMINA ESTA LÍNEA: formData.append('_method', 'PUT');
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: "/actualizar_producto/" + id_producto,
        method: 'POST', // 👈 AHORA ES SOLO POST
        data: formData,
        processData: false, 
        contentType: false,  
        dataType: 'json',
        success: function(data) {
            console.log('✅ Producto actualizado:', data);
            
            if (window.table) {
                window.table.ajax.reload(null, false);
            }
            
            $('#form_editar_productos')[0].reset();      
            $('#modalEditarProducto').modal('hide');
            
            toastr.success("Producto actualizado correctamente");
            btn.html(existingHTML).prop('disabled', false);
        },
        error: function(xhr) {
            console.error('❌ Error:', xhr);
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = "Errores:<br>";
                for (const field in errors) {
                    errorMessage += `- ${errors[field][0]}<br>`;
                }
                toastr.error(errorMessage);
            } else {
                toastr.error("Error al actualizar el producto");
            }
            
            btn.html(existingHTML).prop('disabled', false);
        }
    });
});
    
</script>


<script>

//===========================================================

// Función para validar archivos de imagenes antes de enviar

//============================================================

function validarArchivo(inputFile) {
    const archivo = inputFile.files[0];
    
    if (!archivo) {
        return { valido: false, mensaje: 'Por favor selecciona un archivo' };
    }

    // Tipos de archivo permitidos
    const tiposPermitidos = ['image/webp', 'image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    
    // Validar tipo de archivo
    if (!tiposPermitidos.includes(archivo.type)) {
        return { 
            valido: false, 
            mensaje: 'Tipo de archivo no permitido. Solo se permiten: WEBP, JPEG, PNG, JPG, GIF' 
        };
    }

    // Validar tamaño máximo (2MB = 2048KB)
    const tamañoMaximo = 2048 * 1024; // 2MB en bytes
    if (archivo.size > tamañoMaximo) {
        return { 
            valido: false, 
            mensaje: 'El archivo es demasiado grande. Máximo permitido: 2MB' 
        };
    }

    return { valido: true, mensaje: 'Archivo válido' };
}

//==========================================================

// Función para mostrar preview y validación en tiempo real

//==========================================================

function manejarSeleccionArchivo(inputFile, previewElement, mensajeElement) {
    const validacion = validarArchivo(inputFile);
    
    mensajeElement.textContent = validacion.mensaje;
    mensajeElement.className = validacion.valido ? 'text-success' : 'text-danger';
    
    if (validacion.valido) {
        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        };
        reader.readAsDataURL(inputFile.files[0]);
    } else {
        previewElement.style.display = 'none';
        inputFile.value = ''; // Limpiar input
    }
    
    return validacion.valido;
}

// Ejemplo de uso con AJAX
function subirArchivo() {
    const inputFile = document.getElementById('archivo');
    const mensajeElement = document.getElementById('mensaje-archivo');
    
    // Validar antes de enviar
    const validacion = validarArchivo(inputFile);
    
    if (!validacion.valido) {
        mensajeElement.textContent = validacion.mensaje;
        mensajeElement.className = 'text-danger';
        return;
    }

    // Crear FormData
    const formData = new FormData();
    formData.append('archivo', inputFile.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    // Enviar con AJAX
    fetch('/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mensajeElement.textContent = 'Archivo subido correctamente';
            mensajeElement.className = 'text-success';
        } else {
            mensajeElement.textContent = data.message || 'Error al subir archivo';
            mensajeElement.className = 'text-danger';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mensajeElement.textContent = 'Error al subir archivo';
        mensajeElement.className = 'text-danger';
    });
}

</script>

<script>

$(document).ready(function() {

const defaultProductImage = "{{ asset('storage/images/default-product.png') }}";

    // ===== MODAL VER PRODUCTO =====
    $(document).on('click', '#modalVerProducto .close', function() {
        $('#modalVerProducto').modal('hide');
    });
    
    $(document).on('click', '#modalVerProducto .btn-secondary', function() {
        $('#modalVerProducto').modal('hide');
    });
    
    $('#modalVerProducto').modal({
        backdrop: true,
        keyboard: true,
        show: false
    });

    // ===== MODAL EDITAR PRODUCTO =====
    $(document).on('click', '#modalEditarProducto .close', function() {
        $('#modalEditarProducto').modal('hide');
    });
    
    $(document).on('click', '#modalEditarProducto .btn-secondary', function() {
        $('#modalEditarProducto').modal('hide');
    });
    
    $('#modalEditarProducto').modal({
        backdrop: true,
        keyboard: true,
        show: false
    });

    // =============================================
// FIX: Evitar acumulación de padding-right al cerrar modales
// =============================================
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('body').css('padding-right', '0');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });

    $(document).on('show.bs.modal', '.modal', function() {
        $('body').css('padding-right', '0');
    });

});

</script>
@endpush
@endsection
