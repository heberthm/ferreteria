@extends('layouts.app')

@section('content')
<style>
    .btn-group .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.8rem;
    }
    
    #TablaInventario,
    #TablaInventario thead th,
    #TablaInventario tbody td {
        font-size: 12.5px;
    }
    
    #TablaInventario .badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    .modal-header.bg-light {
        background-color: #f8f9fa;
    }
        
    .producto-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #007bff;
    }

    /* Tarjetas estadísticas */
    .stat-card {
        border-radius: 10px;
        padding: 20px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 2.5rem;
        opacity: 0.3;
    }

    .stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 5px 0;
    }

    .stat-card .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin: 0;
    }

    .stat-card .stat-sub {
        font-size: 0.75rem;
        opacity: 0.75;
        margin-top: 5px;
    }

    .bg-entradas  { background: linear-gradient(135deg, #28a745, #20c997); }
    .bg-salidas   { background: linear-gradient(135deg, #dc3545, #fd7e14); }
    .bg-ajustes   { background: linear-gradient(135deg, #6f42c1, #007bff); }
    .bg-valor     { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .bg-total     { background: linear-gradient(135deg, #17a2b8, #007bff); }
    .bg-productos { background: linear-gradient(135deg, #6c757d, #495057); }

    /* Top productos */
    .top-producto-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 6px;
        background: #f8f9fa;
        border-left: 3px solid #007bff;
        font-size: 12.5px;
    }

    .top-producto-item .producto-rank {
        font-weight: 700;
        color: #007bff;
        width: 20px;
    }

    .top-producto-item .producto-nombre {
        flex: 1;
        padding: 0 10px;
    }

    .top-producto-item .producto-cantidad {
        font-weight: 600;
        color: #28a745;
    }
</style>

<br>

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-chart-line"></i> Kardex de Inventario
        </h5>
        <div>
            <button type="button" class="btn btn-success btn-sm" id="BtnExportarKardex">
                <i class="fa fa-download"></i> Exportar a excel
            </button>
        </div>
    </div>

    <div class="card-body">

        <!-- ===== ESTADÍSTICAS PRINCIPALES ===== -->
        <div class="row mb-3">
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="stat-card bg-total">
                    <i class="fas fa-list stat-icon"></i>
                    <p class="stat-label">Total Movimientos</p>
                    <div class="stat-value" id="total_movimientos">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <p class="stat-sub">registros en kardex</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="stat-card bg-entradas">
                    <i class="fas fa-arrow-down stat-icon"></i>
                    <p class="stat-label">Total Entradas</p>
                    <div class="stat-value" id="total_entradas">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <p class="stat-sub">unidades compradas</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="stat-card bg-salidas">
                    <i class="fas fa-arrow-up stat-icon"></i>
                    <p class="stat-label">Total Salidas</p>
                    <div class="stat-value" id="total_salidas">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <p class="stat-sub">unidades vendidas</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="stat-card bg-ajustes">
                    <i class="fas fa-sliders-h stat-icon"></i>
                    <p class="stat-label">Ajustes</p>
                    <div class="stat-value" id="total_ajustes">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <p class="stat-sub">correcciones de stock</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="stat-card bg-valor">
                    <i class="fas fa-dollar-sign stat-icon"></i>
                    <p class="stat-label">Valor Inventario</p>
                    <div class="stat-value" id="valor_inventario" style="font-size:1.3rem;">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <p class="stat-sub">stock × precio compra</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <div class="stat-card bg-productos">
                    <i class="fas fa-undo stat-icon"></i>
                    <p class="stat-label">Devoluciones</p>
                    <div class="stat-value" id="total_devoluciones">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <p class="stat-sub">unidades devueltas</p>
                </div>
            </div>
        </div>

        <!-- ===== TOP PRODUCTOS + TABLA ===== -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-star text-warning"></i> 
                            Top 5 Productos más movidos
                        </h6>
                    </div>
                    <div class="card-body py-2" id="top_productos_container">
                        <div class="text-center py-3">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <p class="text-muted mt-2 mb-0" style="font-size:12px;">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar text-info"></i> 
                            Distribución por tipo de movimiento
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <canvas id="graficoTipos" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA KARDEX ===== -->
        <div class="table-responsive">
            <table class="table table-hover" id="TablaInventario" style="width:100%;font-size:12.5px;">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Stock Anterior</th>
                        <th>Stock Nuevo</th>
                        <th>Precio Compra</th>
                        <th>Factura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>


<!-- ===== MODAL DETALLE ===== -->
<div class="modal fade" id="modalDetalleMovimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Detalle del Movimiento
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="producto-info">
                            <h6><i class="fas fa-box"></i> Información del Producto</h6>
                            <hr>
                            <p><strong>Código:</strong> <span id="detalle_producto_codigo"></span></p>
                            <p><strong>Producto:</strong> <span id="detalle_producto_nombre"></span></p>
                            <p><strong>Categoría:</strong> <span id="detalle_producto_categoria"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="producto-info">
                            <h6><i class="fas fa-chart-line"></i> Información del Movimiento</h6>
                            <hr>
                            <p><strong>Tipo:</strong> <span id="detalle_tipo_movimiento"></span></p>
                            <p><strong>Cantidad:</strong> <span id="detalle_cantidad"></span></p>
                            <p><strong>Stock Anterior:</strong> <span id="detalle_stock_anterior"></span></p>
                            <p><strong>Stock Nuevo:</strong> <span id="detalle_stock_nuevo"></span></p>
                            <p><strong>Precio Compra:</strong> <span id="detalle_precio_compra"></span></p>
                            <p><strong>Costo Promedio:</strong> <span id="detalle_costo_promedio"></span></p>
                            <p><strong>Proveedor:</strong> <span id="detalle_proveedor"></span></p>
                            <p><strong>Factura:</strong> <span id="detalle_numero_factura"></span></p>
                            <p><strong>Fecha:</strong> <span id="detalle_fecha_movimiento"></span></p>
                            <p><strong>Registrado por:</strong> <span id="detalle_usuario"></span></p>
                        </div>
                    </div>
                </div>
                <div class="producto-info">
                    <h6><i class="fas fa-sticky-note"></i> Observaciones</h6>
                    <hr>
                    <p id="detalle_notas" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


@push('js')
{{-- Chart.js para el gráfico --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
$(document).ready(function() {

    // =========================================
    // FIX MODALES - evitar desplazamiento
    // =========================================
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('body').css('padding-right', '0');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });

    $(document).on('show.bs.modal', '.modal', function() {
        $('body').css('padding-right', '0');
    });

    // =========================================
    // DATATABLE
    // =========================================
    let table = $('#TablaInventario').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("inventario.data") }}',
        columns: [
            { data: 'id_inventario',        name: 'id_inventario' },
            { data: 'fecha_movimiento',      name: 'fecha_movimiento' },
            { data: 'producto_nombre',       name: 'producto_nombre' },
            { data: 'tipo_movimiento_badge', name: 'tipo_movimiento', orderable: false },
            { data: 'cantidad',              name: 'cantidad' },
            { data: 'stock_anterior',        name: 'stock_anterior' },
            { data: 'stock_nuevo',           name: 'stock_nuevo' },
            { data: 'precio_compra',         name: 'precio_compra' },
            { data: 'numero_factura',        name: 'numero_factura' },
            { data: 'acciones',              name: 'acciones', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable:   "No hay movimientos registrados",
            info:         "Mostrando _START_ a _END_ de _TOTAL_ movimientos",
            infoEmpty:    "Mostrando 0 a 0 de 0 movimientos",
            infoFiltered: "(Filtrado de _MAX_ total movimientos)",
            lengthMenu:   "Mostrar _MENU_ movimientos",
            loadingRecords: "Cargando...",
            processing:   "Procesando...",
            search:       "Buscar:",
            zeroRecords:  "Sin resultados encontrados",
            paginate: {
                first: "Primero", last: "Último",
                next: "Siguiente", previous: "Anterior"
            }
        }
    });

    // =========================================
    // CARGAR RESUMEN ESTADÍSTICO
    // =========================================
    let graficoTipos = null;

    function cargarResumen() {
        $.ajax({
            url: '{{ route("inventario.resumen") }}',
            method: 'GET',
            success: function(data) {
                // Tarjetas
                $('#total_movimientos').text(Number(data.total_movimientos || 0).toLocaleString());
                $('#total_entradas').text(Number(data.total_entradas || 0).toLocaleString());
                $('#total_salidas').text(Number(data.total_salidas || 0).toLocaleString());
                $('#total_ajustes').text(Number(data.total_ajustes || 0).toLocaleString());
                $('#total_devoluciones').text(Number(data.total_devoluciones || 0).toLocaleString());
                $('#valor_inventario').text('$' + Number(data.valor_total_inventario || 0).toLocaleString('es-CO'));

                // Top productos
                let topHtml = '';
                if (data.productos_mas_movidos && data.productos_mas_movidos.length > 0) {
                    data.productos_mas_movidos.forEach(function(item, index) {
                        let nombre = item.producto ? item.producto.nombre : 'Producto eliminado';
                        topHtml += `
                            <div class="top-producto-item">
                                <span class="producto-rank">#${index + 1}</span>
                                <span class="producto-nombre">${nombre}</span>
                                <span class="producto-cantidad">${Number(item.total_movimientos).toLocaleString()} uds</span>
                            </div>`;
                    });
                } else {
                    topHtml = '<p class="text-muted text-center mb-0" style="font-size:12px;">Sin datos disponibles</p>';
                }
                $('#top_productos_container').html(topHtml);

                // Gráfico de barras
                let ctx = document.getElementById('graficoTipos').getContext('2d');

                if (graficoTipos) {
                    graficoTipos.destroy();
                }

                graficoTipos = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Entradas', 'Salidas', 'Ajustes', 'Devoluciones'],
                        datasets: [{
                            label: 'Unidades',
                            data: [
                                data.total_entradas    || 0,
                                data.total_salidas     || 0,
                                data.total_ajustes     || 0,
                                data.total_devoluciones|| 0
                            ],
                            backgroundColor: [
                                'rgba(40,167,69,0.8)',
                                'rgba(220,53,69,0.8)',
                                'rgba(111,66,193,0.8)',
                                'rgba(108,117,125,0.8)'
                            ],
                            borderColor: [
                                '#28a745','#dc3545','#6f42c1','#6c757d'
                            ],
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { size: 11 } }
                            },
                            x: {
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            },
            error: function() {
                $('#total_movimientos, #total_entradas, #total_salidas, #total_ajustes, #total_devoluciones')
                    .text('—');
                $('#valor_inventario').text('$—');
                $('#top_productos_container').html(
                    '<p class="text-danger text-center mb-0" style="font-size:12px;">Error al cargar estadísticas</p>'
                );
            }
        });
    }

    cargarResumen();

    // =========================================
    // VER DETALLE DE MOVIMIENTO
    // =========================================
    $(document).on('click', '.verMovimiento', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        if (!id) {
            toastr.error('ID de movimiento no encontrado');
            return;
        }

        $.ajax({
            url: '/inventario/detalle/' + id,
            method: 'GET',
            success: function(data) {
                $('#detalle_producto_codigo').text(data.producto_codigo   || 'N/A');
                $('#detalle_producto_nombre').text(data.producto_nombre   || 'N/A');
                $('#detalle_producto_categoria').text(data.producto_categoria || 'N/A');
                $('#detalle_tipo_movimiento').html(data.tipo_movimiento   || 'N/A');
                $('#detalle_cantidad').text(data.cantidad                 || '0');
                $('#detalle_stock_anterior').text(data.stock_anterior     || '0');
                $('#detalle_stock_nuevo').text(data.stock_nuevo           || '0');
                $('#detalle_precio_compra').text(data.precio_compra       || 'N/A');
                $('#detalle_costo_promedio').text(data.costo_promedio     || 'N/A');
                $('#detalle_proveedor').text(data.proveedor               || 'N/A');
                $('#detalle_numero_factura').text(data.numero_factura     || 'N/A');
                $('#detalle_fecha_movimiento').text(data.fecha_movimiento || 'N/A');
                $('#detalle_usuario').text(data.usuario_nombre            || 'N/A');
                $('#detalle_notas').text(data.notas                       || 'Sin observaciones');

                $('#modalDetalleMovimiento').modal('show');
            },
            error: function() {
                toastr.error('Error al cargar los detalles del movimiento');
            }
        });
    });

    
   $('#BtnExportarKardex').on('click', function() {
    // Cambiar texto del botón mientras descarga
    let btn = $(this);
    let htmlOriginal = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm mr-1"></span> Generando...').prop('disabled', true);
    
    // Descargar archivo
    window.location.href = '{{ route("inventario.exportar") }}';
    
    // Restaurar botón después de 3 segundos
    setTimeout(function() {
        btn.html(htmlOriginal).prop('disabled', false);
    }, 3000);
});

});
</script>
@endpush

@endsection