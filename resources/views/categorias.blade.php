
@extends('layouts.app')
@section('content')

<br>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between float-right">
        <h5 class="mb-0"><i class="fas fa-th-list"></i> Gestión de Categorías</h5>
        <button class="btn btn-primary float-right" id="BtnCrearCategoria" data-toggle="modal" data-target="#modalCategoria"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Categoría</button>
    </div>
    <div class="card-body">
        <table class="table table-hover" id="TablaCategorias" style="width:100%;font-size:12.5px;">
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

<!-- Modal para crear categorías -->
<div class="modal fade" id="modalCategoria" role="dialog" tabindex="-1">

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
                   <button type="submit" class="btn btn-primary" id="BtnGuardar_categoria" name="BtnGuardar_categoria">Guardar</button>
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


<!-- ===================================================

 DATATABLE CATEGORIAS

======================================================= --->

@push('js')

<script>

  $(document).ready(function() {
        $("#modalCategoria input:first").focus();
   });
</script>

<script type="text/javascript">


  $(document).ready(function() {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    let table = $('#TablaCategorias').DataTable({
      processing: true,
      serverSide: true,
      paging: true,
      info: true,
      filter: true,
      responsive: true,
      type: "GET",
      ajax: 'categorias',
      columns: [
        {
          data: 'id_categoria',
          name: 'id_categoria'
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
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false
        },
      ],

      order: [
        [0, 'asc']
      ],


      "language": {


        "emptyTable": "No hay categorías registrados.",
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



 
$('#form_guardar_categoria').off('submit').on('submit', function (event) {

  event.preventDefault();

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
/* Configurar botón submit con spinner */
let btn = $('#BtnGuardar_categoria') 
    let existingHTML =btn.html() //store exiting button HTML
    //Add loading message and spinner
    $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)
    setTimeout(function() {
      $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
    },5000) //5 seconds
        $('#BtnGuardar_categoria').attr('disabled', true);

      

        try {

        $.ajax({
            url: "/categorias",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
              
                $('#modalCategoria').modal('hide');       
                $('#form_guardar_categoria')[0].reset();              

                  table.ajax.reload();
                $('#BtnGuardar_categoria').prop("required", true);
               // $('#selectBuscarCliente').html("");
               
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