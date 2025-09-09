@extends('layouts.app')
@section('content')

<head>
  <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<br>
<div class="card">
    <div class="card-header d-flex bg-light justify-content-between align-items-right">
        <h5 class="mb-0"><i class="fas fa-umbrella"></i> Gestión de productos</h5>
      <!--  <button class="btn btn-primary float-right" id="BtnCrearProducto" data-toggle="modal" data-target="#modalproductos"><i class="fa fa-plus" aria-hidden="true"></i>  Nuevo producto</button> -->
     
            <div class="pull-right">
                 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalproductos">
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

<!-- Modal para crear productos -->
<div class="modal fade" id="modalproductos"  role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- Añadido modal-lg para más ancho -->
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalproductosTitle"><i class="fas fa-umbrella"></i> Nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" id="form_guardar_productos" enctype="multipart/form-data"  action="{{ url('productos') }}" >
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
                               <label for="Categoria" class="col-sm-4 col-form-label">Categoría</label>
                               <div class="col-sm-8">
                                     <select id="categorias" name="categorias" class="form-control" placeholder="Filtrar eventos" required>

                                            <option value="todos">Mostrar todos</option>

                                            @foreach($categorias as $categ)

                                            <option value="{{$categ->nombre}}">{{$categ->nombre}}</option>

                                            @endforeach

                                     </select>
                                </div>                             
                             </div>

                              <div class="form-group row mb-3">
                                <label for="imagen" class="col-sm-4 col-form-label">Imagen</label>
                              
                                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" >
                                     <small class="form-text text-muted">
                                                    Formatos: JPEG, PNG, JPG, GIF, SVG. Máximo 2MB.
                                                </small>
                                    <div class="invalid-feedback"></div>
                               
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
<!-- Modal de confirmación para eliminar productoo -->


<div class="modal fade" id="ModalEliminarCategoria" tabindex="-1" aria-hidden="true">

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


<!-- ===================================================

 DATATABLE PRODUCTOS

======================================================= --->

@push('js')

<script>

  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    let table = $('#tablaProductos').DataTable({
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


$('#form_guardar_producto').off('submit').on('submit', function (event) {

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
    },5000) //5 seconds
        $('#BtnGuardar_producto').attr('disabled', true);
   

        try {

        $.ajax({
            url: "/productos",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                 $('#modalProductos').hide();       
                $('#form_guardar_categoria')[0].reset();              

              
                $('#BtnGuardar_producto').prop("required", true);
               // $('#selectBuscarCliente').html("");
                   table.ajax.reload();
              
                toastr["success"]("registro creado correctamente.");
         
            }
         });
        } catch(e) {
          toastr["danger"]("Se ha presentado un error.", "Información");
          }
    });
   
});

 


</script>

@endpush
@endsection