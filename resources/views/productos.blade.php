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
        <table class="table table-hover" id="tablaProductos" style="width:100%;font-size:12.5px;">   
            <thead>  
                <tr>   
                    <th>Código</th>
                    <th>Nombre</th>   
                    <th>Descripción</th>   
                    <th>Precio</th>   
                    <th>Stock</th>
                    <th>Stock min</th>
                    <th>Ubicación</th>   
                    <th>Acciones</th>   
                </tr>   
            </thead>   
            <tbody>   
                <!-- Datos se cargarán via AJAX -->   
            </tbody>   
        </table>   
    </div>
</div>

<!-- ================================= 
 Modal para crear productos 
======================================  -->

<div class="modal fade" id="modalProductos"  role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- Añadido modal-lg para más ancho -->
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalproductosTitle"><i class="fas fa-umbrella"></i> Nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_guardar_productos" enctype="multipart/form-data"  action="{{ url('productos') }}" >
            
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
                               <label for="Categoria" class="col-sm-4 col-form-label">Categoría</label>
                               <div class="col-sm-8">
                                     <select id="categoria" name="categoria" class="form-control" placeholder="Filtrar eventos" required>

                                            <option value="todos">Mostrar todas</option>

                                            @foreach($categorias as $categ)

                                            <option value="{{$categ->nombre}}">{{$categ->nombre}}</option>

                                            @endforeach

                                     </select>
                                </div>                             
                             </div>

                              <div class="form-group row mb-3">
                                <label for="imagen" class="col-sm-4 col-form-label">Imagen</label>

                                <input type="file" id="imagen" name="imagen" class="form-control" accept=".webp,.jpeg,.jpg,.png,.gif" 
                                            onchange="manejarSeleccionArchivo(this, document.getElementById('preview'), document.getElementById('mensaje-archivo'))">
                                        <small class="form-text text-muted">
                                            Formatos: JPEG, PNG, JPG, GIF, SVG. Máximo 2MB.
                                            <div id="mensaje-archivo"></div> 
                                        </small>                   
                                    
                               
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
                                    <label for="proveedor" class="col-sm-4 col-form-label">Proveedor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                               </div>

                                 <div class="form-group row mb-3">
                                     <div class="col-sm-8">
                                       <img id="preview">                     
                                         
                                    </div>
                               </div>
 

                         </div>
                    </div>                    
                  
                </div>

                <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                   <button type="submit" class="btn btn-primary" id="BtnGuardar_producto" name="BtnGuardar_producto">Guardar</button>                              
                    <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::check() ? Auth::user()->id : null}}" readonly>
              
                </div>
            </form>
        </div>
    </div>
</div>



<!-- ================================= 
 Modal para editar productos 
======================================  -->

<div class="modal fade" id="modalEditarProducto"  role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- Añadido modal-lg para más ancho -->
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalEditarProducto"><i class="fas fa-umbrella"></i> Editar producto: &nbsp; &nbsp</h5>
                   <h5 ><a class="mx-1 nombre" style="color:red" id="nombre_producto_titulo_2"></a></h5>    
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_editar_productos" enctype="multipart/form-data"  action="{{ url('productos') }}" >
            @csrf
                <div class="modal-body">
                    <input type="hidden" id="id_categoria" name="id_categoria" value="1">
                     <input type="hidden" id="id_proveedor" name="id_proveedor" value="1">
                     <input type="hidden" id="id_producto_producto" name="id_producto" value="">
                    
                    <div class="row"> <!-- Fila para agrupar campos horizontalmente -->
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="Nombre" class="col-sm-4 col-form-label">Nombre</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nombre_producto" name="nombre" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="descripcion" class="col-sm-4 col-form-label">Descripción</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="descripcion_producto" name="descripcion" rows="2"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="unida_medida" class="col-sm-4 col-form-label">Unidad de medida</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="unidad_medida_producto" name="unidad_medida" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="ubicacion" class="col-sm-4 col-form-label">Ubicación</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="ubicacion_producto" name="ubicacion" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="Marca" class="col-sm-4 col-form-label">Marca</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="marca_producto" name="marca" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                          
                            
                             <div class="form-group row mb-3">
                               <label for="Categoria" class="col-sm-4 col-form-label">Categoría</label>
                               <div class="col-sm-8">
                                     <select id="categoria_producto" name="categoria" class="form-control" placeholder="Filtrar eventos" required>

                                            <option value="todos">Mostrar todas</option>

                                            @foreach($categorias as $categ)

                                            <option value="{{$categ->nombre}}">{{$categ->nombre}}</option>

                                            @endforeach

                                     </select>
                                </div>                             
                             </div>

                              <div class="form-group row mb-3">
                                <label for="imagen_editar" class="col-sm-4 col-form-label">Imagen</label>
                                <div class="col-sm-8">
                                    <input type="file" id="imagen_editar" name="imagen" class="form-control" 
                                           accept=".webp,.jpeg,.jpg,.png,.gif" 
                                          onchange="previewImage(this, 'preview_editar')">
                                    <small class="form-text text-muted">
                                        Formatos: JPEG, PNG, JPG, GIF, SVG. Máximo 2MB.
                                        <div id="mensaje-archivo-editar"></div> 
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="cantidad" class="col-sm-4 col-form-label">Cantidad</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="cantidad_producto" name="cantidad" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="Precio_compra" class="col-sm-4 col-form-label">Precio compra</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="precio_compra_producto" name="precio_compra" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="precio_venta" class="col-sm-4 col-form-label">Precio venta</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="precio_venta_producto" name="precio_venta" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="Codigo" class="col-sm-4 col-form-label">Código</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="codigo_producto" name="codigo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                              <div class="form-group row mb-3">
                                <label for="Stock" class="col-sm-4 col-form-label">Stock</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock_producto" name="stock" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock_minimo" class="col-sm-4 col-form-label">Stock mínimo</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="stock_minimo_producto" name="stock_minimo" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                         

                               <div class="form-group row mb-3">
                                    <label for="proveedor" class="col-sm-4 col-form-label">Proveedor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="proveedor_producto" name="proveedor" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                               </div>

                                <div class="form-group row mb-3">
                                <div class="col-sm-8 offset-sm-4">
                                    <img id="preview_editar" style="max-width: 100px; max-height: 100px; margin-top: 5px; display: none;">
                                    <!-- Preview de la imagen actual -->
                                    <img id="preview_imagen_actual" style="max-width: 100px; max-height: 100px; margin-top: 5px; display: none;">
                                </div>
                            </div>
 

                         </div>
                    </div>                    
                  
                </div>

                <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                   <button type="submit" class="btn btn-primary" id="BtnEditar_producto" name="BtnEditar_producto">Guardar</button>                              
                    <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::check() ? Auth::user()->id : null}}" readonly>
              
                </div>
            </form>
        </div>
    </div>
</div>




<!--=====================================

    MODAL VER DATOS DEL PRODUCTO

======================================-->


<div class="modal fade" id="modalVerProducto" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header bg-light">
                  <h5 class="modal-title" id="modalVerProductos"><i class="fas fa-umbrella"></i> Ver datos del producto: &nbsp; &nbsp;</h5>
                      <h5 ><a class="mx-1 nombre" style="color:red" id="nombre_producto_titulo"></a></h5>     

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="GET" id="form_ver_producto" enctype="multipart/form-data"  action="{{ url('productos') }}" >
            @csrf
                <div class="modal-body">
                    <input type="hidden" id="id_categoria" name="id_categoria" >
                     <input type="hidden" id="id_proveedor" name="id_proveedor" >
                                       
                    <div class="row"> <!-- Fila para agrupar campos horizontalmente -->
                        <!-- Columna 1 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="Nombre" class="col-sm-4 col-form-label">Nombre</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control  border-0" autocomplete="off" id="nombre" name="nombre" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="descripcion" class="col-sm-4 col-form-label">Descripción</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" autocomplete="off" id="descripcion" name="descripcion" readonly >
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="unida_medida" class="col-sm-4 col-form-label">Unidad de medida</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control " autocomplete="off" id="unidad_medida" name="unidad_medida" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="ubicacion" class="col-sm-4 col-form-label">Ubicación</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" autocomplete="off" id="ubicacion" name="ubicacion" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="Marca" class="col-sm-4 col-form-label">Marca</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control " autocomplete="off" id="marca" name="marca" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>                          
                            
                             <div class="form-group row mb-3">
                               <label for="Categoria" class="col-sm-4 col-form-label">Categoría</label>
                               <div class="col-sm-8">
                                   <input type="text" class="form-control "  autocomplete="off" id="categoria" name="categoria" readonly required>
                                    <div class="invalid-feedback"></div>                                                                        
                                </div>                             
                             </div>

                               <div class="form-group row mb-3">
                                     <div class="col-sm-8">
                                       <img id="previewVerProducto" style="max-width: 100px; max-height: 100px; margin-top: 5px; display: none;">                                         
                                    </div>
                               </div>

                        </div>
                        
                        <!-- Columna 2 -->
                        <div class="col-md-6">
                            <div class="form-group row mb-3">
                                <label for="cantidad" class="col-sm-4 col-form-label">Cantidad</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="cantidad" name="cantidad" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="Precio_compra" class="col-sm-4 col-form-label">Precio compra</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="precio_compra" name="precio_compra" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="precio_venta" class="col-sm-4 col-form-label">Precio venta</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="precio_venta" name="precio_venta" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="Codigo" class="col-sm-4 col-form-label">Código</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="codigo" name="codigo" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                              <div class="form-group row mb-3">
                                <label for="Stock" class="col-sm-4 col-form-label">Stock</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="stock" name="stock" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="stock_minimo" class="col-sm-4 col-form-label">Stock mínimo</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="stock_minimo" name="stock_minimo" readonly required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>                         

                               <div class="form-group row mb-3">
                                    <label for="proveedor" class="col-sm-4 col-form-label">Proveedor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="proveedor" name="proveedor" readonly required>
                                        <div class="invalid-feedback"></div>
                                             <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::user()->id }}" readonly>

                                    </div>
                               </div>
                         </div>
                    </div>       
                    

                  
                </div>

                <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>                                               
                   <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::check() ? Auth::user()->id : null}}" readonly>                
                </div>
            </form>
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
          data: 'precio_compra',
          name: 'precio_compra'
        },
        {
          data: 'stock',
          name: 'stock'
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
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false
        },
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

//======================================

// Guardar registro de producto

//======================================


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
            url: "/productos",
            method: "POST",
            data: formData,
            processData: false, 
            contentType: false,  
            dataType: "json",
            success: function(data) {
              
                   
                table.ajax.reload();   

                $('#form_guardar_productos')[0].reset();      
                $('#modalProductos').removeClass('show');
                $('#modalProductos').css('display', 'none');
                $('.modal-backdrop').remove();              
            

                 toastr["success"]("registro creado correctamente.");             
                    
         
            }
         });
        } catch(e) {
          toastr["danger"]("Se ha presentado un error.", "Información");
          }
    }); 



// =========================================

/// ELIMINAR REGISTROS DE TERAPIA

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

/// VER REGISTROS DEL PRODUCTO

// =========================================

$(document).on('click', '.verProducto', function(e) {    
    e.preventDefault();
    
    let id_producto = $(this).data('id');
    
    $.ajax({
        url: "{{ url('mostrar_producto') }}/" + id_producto,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Datos del producto recibidos:', data);
            
            // Llenar campos del modal VER
            $('#modalVerProducto input[name="codigo"]').val(data.codigo || '');
            $('#modalVerProducto input[name="nombre"]').val(data.nombre || '');
            $('#modalVerProducto input[name="descripcion"]').val(data.descripcion || '');
            $('#modalVerProducto input[name="marca"]').val(data.marca || '');
            $('#modalVerProducto input[name="categoria"]').val(data.categoria || '');
            $('#modalVerProducto input[name="unidad_medida"]').val(data.unidad_medida || '');
            $('#modalVerProducto input[name="ubicacion"]').val(data.ubicacion || '');
            $('#modalVerProducto input[name="cantidad"]').val(data.cantidad || '');
            $('#modalVerProducto input[name="precio_compra"]').val(data.precio_compra || '');
            $('#modalVerProducto input[name="precio_venta"]').val(data.precio_venta || '');
            $('#modalVerProducto input[name="stock"]').val(data.stock || '');
            $('#modalVerProducto input[name="stock_minimo"]').val(data.stock_minimo || '');
            $('#modalVerProducto input[name="proveedor"]').val(data.proveedor || '');

             // Actualizar solo el nombre en el título
            $('#nombre_producto_titulo').text(data.nombre || '');

            // Manejo de imagen en modal VER
            const preview = $('#previewVerProducto');
            
            if(data.imagen && data.imagen.trim() !== '') {
                let cleanImagePath = data.imagen.replace('storage/', '');
                let imageUrl = "{{ asset('storage') }}/" + cleanImagePath;
                
                preview.attr('src', imageUrl);
                preview.css({
                    'display': 'block',
                    'max-width': '100px',
                    'max-height': '100px',
                    'margin-top': '5px'
                });
                
                preview.on('load', function() {
                    console.log('Imagen cargada correctamente');
                });
                
                preview.on('error', function() {
                    console.error('Error cargando imagen:', imageUrl);
                    preview.attr('src', 'https://via.placeholder.com/120x120/6c757d/ffffff?text=Sin+Imagen');
                    preview.show();
                });
                
            } else {
                console.log('No hay imagen disponible');
                preview.hide();
                preview.attr('src', '');
            }
            
            // CORRECCIÓN: Abrir el modal VER, no el EDITAR
            $('#modalVerProducto').modal('show');  // ← CAMBIADO A modalVerProducto
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', xhr);
            toastr["error"]("Error al cargar los datos del producto.");
        }
    });
});
</script>

<script>


  // =========================================
/// EDITAR REGISTROS DEL PRODUCTO
// =========================================

$(document).on('click', '.editarProducto', function(e) {    
    e.preventDefault();
    
    let id_producto = $(this).data('id');
    console.log('Editando producto ID:', id_producto);
    
    $.ajax({
        url: "{{ url('editar_producto') }}/" + id_producto,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Datos del producto recibidos:', data);
            
            // SETEAR EL ID EN EL FORMULARIO
          //  $('#producto_id').val(data.id);
            
            // Llenar campos del modal
             $('#id_producto_producto').val(data.id_producto || '');
            $('#codigo_producto').val(data.codigo || '');
            $('#nombre_producto').val(data.nombre || '');
            $('#descripcion_producto').val(data.descripcion || '');
            $('#marca_producto').val(data.marca || '');
            $('#categoria_producto').val(data.categoria || '');
            $('#unidad_medida_producto').val(data.unidad_medida || '');
            $('#ubicacion_producto').val(data.ubicacion || '');
            $('#cantidad_producto').val(data.cantidad || '');
            $('#precio_compra_producto').val(data.precio_compra || '');
            $('#precio_venta_producto').val(data.precio_venta || '');
            $('#stock_producto').val(data.stock || '');
            $('#stock_minimo_producto').val(data.stock_minimo || '');
            $('#proveedor_producto').val(data.proveedor || '');

               // Actualizar solo el nombre en el título
            $('#nombre_producto_titulo_2').text(data.nombre || '');

            // MANEJO DE IMAGEN - MOSTRAR IMAGEN ACTUAL
            const previewActual = $('#preview_imagen_actual');
            const previewNueva = $('#preview_editar');
            
            if(data.imagen && data.imagen.trim() !== '') {
                console.log('Imagen actual encontrada:', data.imagen);
                
                let cleanImagePath = data.imagen.replace('storage/', '');
                let imageUrl = "{{ asset('storage') }}/" + cleanImagePath;
                
                console.log('URL de imagen actual:', imageUrl);
                
                // Mostrar imagen actual
                previewActual.attr('src', imageUrl);
                previewActual.css('display', 'block');
                
                // Ocultar preview de nueva imagen
                previewNueva.hide();
                
                // Verificar si la imagen se carga correctamente
                previewActual.on('load', function() {
                    console.log('Imagen actual cargada correctamente');
                });
                
                previewActual.on('error', function() {
                    console.error('Error cargando imagen actual:', imageUrl);
                    previewActual.attr('src', 'https://via.placeholder.com/120x120/6c757d/ffffff?text=Sin+Imagen');
                    previewActual.show();
                });
                
            } else {
                console.log('No hay imagen actual disponible');
                previewActual.hide();
                previewActual.attr('src', '');
                previewNueva.hide();
            }
            
            $('#modalEditarProducto').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', xhr);
            toastr.error("Error al cargar los datos del producto.");
        }
    });
});

//==============================
//GUardar datos editados.
//===============================

$(document).on('submit', '#form_editar_productos', function(event) {
    event.preventDefault();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let id_producto = $('#id_producto_producto').val();
    
    if (!id_producto) {
        toastr.error("Error: ID del producto no encontrado");
        return;
    }

    // ✅ VALIDACIÓN MEJORADA DE IMAGEN
    const imagenInput = document.getElementById('imagen_editar');
    let imagenValida = true;
    let mensajeError = '';

    if (imagenInput.files.length > 0) {
        const imagenFile = imagenInput.files[0];
        
        console.log('Validando imagen:', {
            nombre: imagenFile.name,
            tipo: imagenFile.type,
            tamaño: imagenFile.size,
            extension: imagenFile.name.split('.').pop()
        });

        // Validar tipo MIME
        const tiposPermitidos = [
            'image/webp', 
            'image/jpeg', 
            'image/png', 
            'image/jpg', 
            'image/gif'
        ];
        
        if (!tiposPermitidos.includes(imagenFile.type)) {
            imagenValida = false;
            mensajeError = 'Tipo de archivo no permitido. Formatos aceptados: WEBP, JPEG, PNG, JPG, GIF';
            console.error('Tipo MIME no permitido:', imagenFile.type);
        }
        
        // Validar extensión por si acaso
        const extension = imagenFile.name.toLowerCase().split('.').pop();
        const extensionesPermitidas = ['webp', 'jpeg', 'jpg', 'png', 'gif'];
        if (!extensionesPermitidas.includes(extension)) {
            imagenValida = false;
            mensajeError = 'Extensión no permitida. Use: .webp, .jpeg, .jpg, .png o .gif';
            console.error('Extensión no permitida:', extension);
        }
        
        // Validar tamaño (2MB máximo)
        if (imagenFile.size > 2 * 1024 * 1024) {
            imagenValida = false;
            mensajeError = 'La imagen es demasiado grande. Máximo permitido: 2MB';
            console.error('Tamaño excedido:', imagenFile.size);
        }
        
        // Validar que sea realmente una imagen
        if (!imagenFile.type.startsWith('image/')) {
            imagenValida = false;
            mensajeError = 'El archivo debe ser una imagen válida';
            console.error('No es una imagen:', imagenFile.type);
        }
    }

    if (!imagenValida) {
        toastr.error(mensajeError);
        
        // Limpiar el input de imagen
        imagenInput.value = '';
        
        // Ocultar preview y mostrar imagen actual
        $('#preview_editar').hide();
        $('#preview_imagen_actual').show();
        
        return;
    }

    // Configurar botón submit
    let btn = $('#BtnEditar_producto');
    let existingHTML = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Procesando...').prop('disabled', true);

    var formData = new FormData(this);

    // ✅ DEBUG: Mostrar datos que se envían
    console.log('Enviando FormData:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ':', pair[1]);
    }

    $.ajax({
        url: "/actualizar_producto/" + id_producto,
        method: 'POST',
        data: formData,
        processData: false, 
        contentType: false,  
        dataType: 'json',
        success: function(data) {
            console.log('✅ Respuesta exitosa:', data);
            
            // Recargar tabla
            if (window.table && typeof window.table.ajax !== 'undefined') {
                window.table.ajax.reload(null, false);
            }
            
            $('#form_editar_productos')[0].reset();      
            $('#modalEditarProducto').modal('hide');
            $('.modal-backdrop').remove();              
            
            toastr.success("Producto actualizado correctamente");
            btn.html(existingHTML).prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en AJAX:', xhr);
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = "Errores de validación:<br>";
                
                for (const field in errors) {
                    errorMessage += `- ${errors[field][0]}<br>`;
                    
                    // Resaltar campo con error
                    $(`[name="${field}"]`).addClass('is-invalid');
                    $(`#error-${field}`).remove();
                    $(`[name="${field}"]`).after(`<div class="invalid-feedback" id="error-${field}">${errors[field][0]}</div>`);
                }
                
                toastr.error(errorMessage);
                
                // Si el error es de imagen, limpiar el input
                if (errors.imagen) {
                    $('#imagen_editar').val('');
                    $('#preview_editar').hide();
                    $('#preview_imagen_actual').show();
                }
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

<script>$(document).ready(function() {
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

});

</script>
@endpush
@endsection
