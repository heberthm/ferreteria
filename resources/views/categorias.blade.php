@extends('layouts.app')
@section('content')


<br>

 <style>

  
        .btn-group .btn {
            padding: 0.25rem 0.4rem;
            font-size: 0.8rem;
        }

    
/* Mantener el color de los textos en negritas */
.list-group-item strong {
    color: #495057;
    font-weight: 600;
}

 .my-accordion {
                        border: 1px solid #dee2e6;
                        border-radius: 6px;
                        overflow: hidden;
                    }
                    .my-accordion-item {
                        border-bottom: 1px solid #dee2e6;
                    }
                    .my-accordion-item:last-child {
                        border-bottom: none;
                    }
                    .my-accordion-btn {
                        width: 100%;
                        background: #f8f9fa;
                        border: none;
                        padding: 12px 16px;
                        text-align: left;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 600;
                        color: #343a40;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        transition: background 0.2s;
                    }
                    .my-accordion-btn:hover {
                        background: #e9ecef;
                    }
                    .my-accordion-btn.active {
                        background: #e9ecef;
                        color: #007bff;
                    }
                    .my-accordion-btn .arrow {
                        font-style: normal;
                        font-size: 12px;
                        transition: transform 0.3s;
                        display: inline-block;
                    }
                    .my-accordion-btn.active .arrow {
                        transform: rotate(180deg);
                    }
                    .my-accordion-body {
                        display: none;
                        padding: 12px 16px;
                        background: #fff;
                        font-size: 13px;
                    }
                    .my-accordion-body.open {
                        display: block;
                    }
                    .my-accordion-body ul {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    .my-accordion-body ul li {
                        padding: 7px 0;
                        border-bottom: 1px solid #f0f0f0;
                        color: #495057;
                        line-height: 1.5;
                    }
                    .my-accordion-body ul li:last-child {
                        border-bottom: none;
                    }

                    /* Estilos para botones de acción */
                    .btn-group .btn-sm {
                        padding: 0.22rem 0.5rem;
                        font-size: 0.75rem;
                        border-radius: 0.2rem;
                        margin: 0 2px;
                    }

                    .btn-group .btn-info {
                        color: #fff;
                        background-color: #17a2b8;
                        border-color: #17a2b8;
                    }

                    .btn-group .btn-warning {
                        color: #212529;
                        background-color: #ffc107;
                        border-color: #ffc107;
                    }

                    .btn-group .btn-danger {
                        color: #fff;
                        background-color: #dc3545;
                        border-color: #dc3545;
                    }


    </style>

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between float-right">
        <h5 class="mb-0"><i class="fas fa-th-list"></i> Gestión de Categorías</h5>

       
           <button type="button" class="btn btn-default float-right" data-bs-toggle="modal" data-bs-target="#categoriasModal">
                <i class="fa fa-eye"></i> Ver ejemplos de categorías
            </button>
        

           <button class="btn btn-primary float-right" id="BtnCrearCategoria" data-toggle="modal" data-target="#modalCategoria"><i class="fa fa-plus" aria-hidden="true"></i> Crear nueva categoría</button>

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


<!-- ===================================

    MODAL CREAR CATEGORIA

======================================  -->


<div class="modal fade" id="modalCategoria" name="modalCategoria" role="dialog" tabindex="-1">

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



<!-- ===================================

    MODAL VER CATEGORIA

======================================  -->

<div class="modal fade" id="modalVerCategoria" tabindex="-1" role="dialog" aria-labelledby="modalVerCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-light" >
                <h5 class="modal-title" id="modalVerCategoriaLabel"><i class="fas fa-eye"></i> Ver Categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalVer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombreVer" name="nombre" readonly>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" class="form-control" id="descripcionVer" name="descripcion" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCerrarModalVer">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- ===================================

    MODAL EDITAR CATEGORIA

======================================  -->


    <div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header  bg-light" >
                <h5 class="modal-title" id="modalTitle"><i class="fa fa-edit" aria-hidden="true"></i> Editar Categoría</h5>                
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
                 </button>
            </div>

            <form method="POST" id="form_editar_categoria" action="{{ url('categorias') }}" >
             @csrf  

                <div class="modal-body">
                     <input type="hidden" id="id_categoria" name="id_categoria" value="1">
                     <input type="hidden" id="id_proveedor" name="id_proveedor" value="1">
                      <input type="hidden" name="id_categoria" id="id_categoria">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcion_categoria" name="descripcion" >                      
                        <div class="invalid-feedback"></div>
                    </div>                    
                </div>

                <div class="modal-footer">                
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                     <button type="submit" class="btn btn-primary" id="BtnEditar_categoria" name="BtnEditar_categoria">Guardar</button>   
                    <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::check() ? Auth::user()->id : null}}" readonly>
                </div>          
            </form>      
        </div>
     </div> 
</div>




<!-- ======================================

MODAL EJEMPLOS DE CATEGORIAS

============================================ -->
<!-- Modal de Categorías - Estilo panelsStayOpen -->
<div class="modal fade" id="categoriasModal" tabindex="-1" aria-labelledby="categoriasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title" id="categoriasModalLabel">
                    <i class="bi bi-tags-fill me-2"></i> Categorías de Productos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">               
                <div class="my-accordion" id="myCustomAccordion">
                    <!-- Ferretería General -->
                    <div class="my-accordion-item">
                        <button class="my-accordion-btn active">
                            <span><i class="bi bi-tools me-2"></i> Ferretería General</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body open">
                            <ul>
                                <li><strong>Herramientas manuales:</strong> Martillos, destornilladores, alicates, llaves (fijas, de expansión), sierras, cinceles, limas, niveles.</li>
                                <li><strong>Herramientas eléctricas:</strong> Taladros, pulidoras, sierras circulares, esmeriles, lijadoras, martillos percutores, pistolas de calor.</li>
                                <li><strong>Medición y marcado:</strong> Cintas métricas, flexómetros, escuadras, calibres, tiralíneas.</li>
                                <li><strong>Elementos de fijación:</strong> Clavos, tornillos, tuercas, arandelas, chazos, remaches.</li>
                                <li><strong>Adhesivos y selladores:</strong> Siliconas, colbón, pegamentos de contacto, masillas, cintas adhesivas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Plomería y Tubería -->
                    <div class="my-accordion-item">
                        <button class="my-accordion-btn active">
                            <span><i class="bi bi-droplet-half me-2"></i> Plomería y Tubería</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>Tuberías:</strong> PVC (agua fría, sanitaria), CPVC (agua caliente), cobre, polipropileno (PPR).</li>
                                <li><strong>Conexiones y accesorios:</strong> Codos, T-es, uniones, reducciones, adaptadores, tapones, niples, válvulas.</li>
                                <li><strong>Grifería y sanitarios:</strong> Grifos para lavamanos y cocina, duchas, sifones, mangueras de abasto, herrajes para sanitarios.</li>
                                <li><strong>Bombas y tanques:</strong> Bombas de agua, motobombas, tanques de almacenamiento.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Electricidad -->
                    <div class="my-accordion-item">
                       <button class="my-accordion-btn active">
                            <span><i class="bi bi-lightning-charge me-2"></i> Electricidad</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>Cables y conductores:</strong> Cables eléctricos (unifilares, multifilares), alambres, cordones.</li>
                                <li><strong>Interruptores y tomas:</strong> Sencillos, dobles, conmutables, tomas polarizadas, con polo a tierra, cajas de paso.</li>
                                <li><strong>Iluminación:</strong> Bombillos LED, fluorescentes, halógenos, luminarias, reflectores.</li>
                                <li><strong>Protección eléctrica:</strong> Breakers, fusibles, cajas de breakers, cintas aislantes.</li>
                                <li><strong>Conductos y canaletas:</strong> Tubería conduit, canaletas plásticas, abrazaderas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Pinturas y Acabados -->
                    <div class="my-accordion-item">
                        <button class="my-accordion-btn active">
                            <span><i class="bi bi-brush me-2"></i> Pinturas y Acabados</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>Pinturas:</strong> Vinilos (interiores y exteriores), esmaltes (brillantes, mate), anticorrosivos, selladores, bases.</li>
                                <li><strong>Disolventes y aditivos:</strong> Thinner, aguarrás, varsol, removedores de pintura.</li>
                                <li><strong>Accesorios para pintar:</strong> Brochas, rodillos, bandejas, espátulas, lijas.</li>
                                <li><strong>Impermeabilizantes:</strong> Para techos y paredes, selladores de grietas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Materiales de Construcción -->
                    <div class="my-accordion-item">
                       <button class="my-accordion-btn active">
                            <span><i class="bi bi-house-gear me-2"></i> Materiales de Construcción</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>Agregados:</strong> Cemento, arena, gravilla, triturado, estuco.</li>
                                <li><strong>Ladrillos y bloques:</strong> Ladrillo común, bloque de cemento.</li>
                                <li><strong>Hierro y acero:</strong> Varillas de refuerzo, mallas electrosoldadas, perfiles, láminas.</li>
                                <li><strong>Mallas y cercas:</strong> Malla eslabonada, alambre de púas, concertinas.</li>
                                <li><strong>Productos químicos:</strong> Aditivos para morteros, pegantes para baldosas, selladores de juntas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Seguridad Industrial -->
                    <div class="my-accordion-item">
                       <button class="my-accordion-btn active">
                            <span><i class="bi bi-shield-check me-2"></i> Seguridad Industrial</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>EPP:</strong> Guantes, gafas de seguridad, cascos, botas con puntera de acero, tapabocas, arneses.</li>
                                <li><strong>Señalización:</strong> Cintas de precaución, conos, señales de seguridad.</li>
                                <li><strong>Extintores:</strong> Polvo químico, CO2, agua.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Jardinería y Agro -->
                    <div class="my-accordion-item">
                       <button class="my-accordion-btn active">
                            <span><i class="bi bi-flower1 me-2"></i> Jardinería y Agro</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>Herramientas de jardinería:</strong> Palas, picos, rastrillos, machetes, mangueras.</li>
                                <li><strong>Equipos de fumigación:</strong> Fumigadoras de espalda, atomizadores.</li>
                                <li><strong>Materiales:</strong> Alambre de amarre, bolsas de basura, lonas plásticas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Misceláneos -->
                    <div class="my-accordion-item">
                       <button class="my-accordion-btn active">
                            <span><i class="bi bi-grid me-2"></i> Misceláneos</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="my-accordion-body">
                            <ul>
                                <li><strong>Cerraduras y candados:</strong> Candados de seguridad, cerraduras para puertas.</li>
                                <li><strong>Carretillas y escaleras:</strong> Carretillas de obra, escaleras de tijera, de extensión.</li>
                                <li><strong>Lubricantes y aceites:</strong> WD-40, aceites para motores, grasas.</li>
                                <li><strong>Artículos para el hogar:</strong> Ganchos para cuadros, topes para puertas, bisagras.</li>
                            </ul>
                        </div>
                    </div>

                </div><!-- fin my-accordion -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
          data: 'acciones',
          name: 'acciones',
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
    },5000) 
        $('#BtnGuardar_categoria').attr('disabled', true);    

        try {

        $.ajax({
            url: "/categorias",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
              
                  table.ajax.reload();
              //  $('#modalCategoria').modal('hide');

                $('#modalVerCategoria').removeClass('show');
                $('#modalVerCategoria').css('display', 'none');
                $('.modal-backdrop').remove();

                $('#form_guardar_categoria')[0].reset();         
                  
              
             //   location.reload(true);
                toastr["success"]("registro creado correctamente.");
         
            }
         });
        } catch(e) {
          toastr["danger"]("Se ha presentado un error.", "Información");
          }
    });

// =========================================
/// VER REGISTROS DEL PRODUCTO
// =========================================

$(document).on('click', '.verCategoria', function(e) {    
    e.preventDefault();
    
    let id_categoria = $(this).data('id');
    
    $.ajax({
        url: "{{ url('mostrar_categoria') }}/" + id_categoria,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Datos del producto recibidos:', data);
            
            // Llenar campos del modal VER
            $('#modalVerCategoria input[name="nombre"]').val(data.nombre || '');
            $('#modalVerCategoria input[name="descripcion"]').val(data.descripcion || '');
            $('#modalVerCategoria').modal('show');    
        }                
    });                  
});

// =========================================
/// ACORDEON EJEMPLOS DE CATEGORIAS
// =========================================

$(document).on('click', '.my-accordion-btn', function() {
    var $btn     = $(this);
    var $body    = $btn.next('.my-accordion-body');
    var isOpen   = $body.is(':visible');

    // Cerrar todos
    $('#myCustomAccordion .my-accordion-body').hide();
    $('#myCustomAccordion .my-accordion-btn').removeClass('active');

    // Abrir solo si estaba cerrado
    if (!isOpen) {
        $body.show();
        $btn.addClass('active');

        // Scroll hacia el botón dentro del modal
        setTimeout(function() {
            var $modalBody = $('#categoriasModal .modal-body');
            var btnTop     = $btn.offset().top;
            var modalTop   = $modalBody.offset().top;
            var scrollPos  = $modalBody.scrollTop() + (btnTop - modalTop) - 8;
            $modalBody.animate({ scrollTop: scrollPos }, 200);
        }, 50);
    }
});

// Abrir primero al mostrar el modal
$(document).on('shown.bs.modal', '#categoriasModal', function() {
    $('#myCustomAccordion .my-accordion-body').hide();
    $('#myCustomAccordion .my-accordion-btn').removeClass('active');
    $('#myCustomAccordion .my-accordion-body').first().show();
    $('#myCustomAccordion .my-accordion-btn').first().addClass('active');
});


// =========================================
/// CERRAR MODALES - CÓDIGO CORREGIDO
// =========================================

// Cerrar modal VER al hacer clic en la X o botón cerrar
$(document).on('click', '#modalVerCategoria .close, #modalVerCategoria [data-dismiss="modal"]', function() {
    $('#modalVerCategoria').modal('hide');
});

// Cerrar modal EDITAR al hacer clic en la X o botón cerrar
$(document).on('click', '#modalEditarCategoria .close, #modalEditarCategoria [data-dismiss="modal"]', function() {
    $('#modalEditarCategoria').modal('hide');
});

// Cerrar modales al hacer clic fuera
$(document).on('click', '#modalVerCategoria, #modalEditarCategoria', function(e) {
    if (e.target === this) {
        $(this).modal('hide');
    }
});

// Cerrar modales con tecla ESC
$(document).on('keydown', function(e) {
    if (e.keyCode === 27) { // ESC key
        $('#modalVerCategoria').modal('hide');
        $('#modalEditarCategoria').modal('hide');
    }
});
// =========================================
/// EDITAR REGISTROS DE CATEGORIA
// =========================================

$(document).on('click', '.editarCategoria', function(e) {    
    e.preventDefault();
    
    let id_categoria = $(this).data('id');
   
    $.ajax({
        url: "{{ url('editar_categoria') }}/" + id_categoria,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Datos recibidos para editar:', data);
            
            // Llenar campos del modal incluyendo el ID
            $('#id_categoria').val(data.id_categoria || data.id); // Asegúrate de usar el campo correcto
            $('#nombre_categoria').val(data.nombre || '');
            $('#descripcion_categoria').val(data.descripcion || '');
                                  
            $('#modalEditarCategoria').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', xhr);
            toastr.error("Error al cargar los datos de la Categoria.");
        }
    });
});

// ==============================
// GUARDAR DATOS EDITADOS - CORREGIDO
// ==============================

$(document).on('submit', '#form_editar_categoria', function(event) {
    event.preventDefault();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // OBTENER EL ID CORRECTAMENTE
    let id_categoria = $('#id_categoria').val();
    
    if (!id_categoria) {
        toastr.error("Error: ID de la categoría no encontrado");
        return;
    }

    console.log('ID de categoría a actualizar:', id_categoria);

    // Configurar botón submit
    let btn = $('#BtnEditar_categoria');
    let existingHTML = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm mr-2"></span>Procesando...').prop('disabled', true);

    var formData = new FormData(this);   

    $.ajax({
        url: "/actualizar_categoria/" + id_categoria,
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

              table.ajax.reload();
            
            $('#form_editar_categoria')[0].reset();      
            $('#modalEditarCategoria').modal('hide');
            $('.modal-backdrop').remove();              
            
            toastr.success(data.message || "Categoría actualizada correctamente");
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
            } else if (xhr.status === 404) {
                toastr.error(xhr.responseJSON.message || "Categoría no encontrada");
            } else {
                toastr.error("Error al actualizar la categoría");
            }
            
            btn.html(existingHTML).prop('disabled', false);
        }
    });
});

// Eliminar categoría
$(document).on('click', '.eliminarCategoria', function() {
    var id = $(this).data('id');
    var fila = $(this).closest('tr');
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("categorias") }}/' + id,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            response.message,
                            'success'
                        );
                        $('#TablaCategorias').DataTable().ajax.reload();
                    } else {
                        Swal.fire(
                            'Error',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    let mensaje = 'Error al eliminar la categoría';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    Swal.fire(
                        'Error',
                        mensaje,
                        'error'
                    );
                }
            });
        }
    });
});

});
 
</script>
@endpush

@endsection