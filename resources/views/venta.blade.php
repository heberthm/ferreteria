@extends('layouts.app')

@section('title', 'Punto de Venta - Ferretería')

@section('content')

<br>

<div class="container-fluid">

    <div class="row">

        <!-- Panel de productos -->

        <div class="col-md-8">

            <div class="card mb-4">

                <div class="card-header bg-light ">

                    <div class="d-flex justify-content-between align-items-center">

                        <h5 class="mb-0">Productos</h5>

                        <div class="input-group" style="width: 300px;">

                            <input type="text" id="search-product" class="form-control" placeholder="Buscar producto...">

                            <div class="input-group-append">

                                <button class="btn btn-outline-light" type="button">

                                    <i class="fas fa-search"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <!-- Filtros por categoría -->

                    <div class="mb-3">

                        <div class="btn-group" role="group">

                            <button type="button" class="btn btn-outline-secondary active">Todos</button>

                            <button type="button" class="btn btn-outline-secondary">Herramientas</button>

                            <button type="button" class="btn btn-outline-secondary">Materiales</button>

                            <button type="button" class="btn btn-outline-secondary">Electricidad</button>

                            <button type="button" class="btn btn-outline-secondary">Fontanería</button>

                            <button type="button" class="btn btn-outline-secondary">Pinturas</button>

                        </div>
                    </div>

                    <!-- Lista de productos -->

                    <div class="row" id="product-list">
                       
                        <div class="col-md-3 mb-3 product-item" data-category="">
                            <div class="card h-100 product-card" data-id="" data-price="" data-stock="">
                                <div class="card-img-top bg-light text-center py-3">
                                  
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1"></h6>
                                    <p class="card-text text-muted small mb-1">Código: </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold text-success"></span>
                                       
                                            
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de venta -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Venta Actual</h5>
                </div>
                <div class="card-body">
                    <!-- Cliente -->
                    <div class="mb-3">
                        <label for="client-select" class="form-label">Cliente</label>
                        <div class="input-group">
                            <select class="form-control select2" id="client-select">
                                <option value="">Cliente general</option>
                               
                            </select>
                            <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#newClientModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Lista de productos en la venta -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm" id="sale-items">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="45%">Producto</th>
                                    <th width="15%">Precio</th>
                                    <th width="20%">Cantidad</th>
                                    <th width="15%">Total</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los items de venta se agregarán dinámicamente aquí -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Subtotal:</th>
                                    <th id="subtotal">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">IVA (16%):</th>
                                    <th id="iva">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr class="table-active">
                                    <th colspan="4" class="text-right">Total:</th>
                                    <th id="total">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Opciones de pago -->
                    <div class="mb-3">
                        <label class="form-label">Método de pago</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="payment_method" value="cash" checked> Efectivo
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="payment_method" value="card"> Tarjeta
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="payment_method" value="transfer"> Transferencia
                            </label>
                        </div>
                    </div>

                    <!-- Efectivo recibido -->
                    <div class="mb-3" id="cash-received-container">
                        <label for="cash-received" class="form-label">Efectivo recibido</label>
                        <input type="number" class="form-control" id="cash-received" step="0.01" min="0">
                    </div>

                    <!-- Cambio -->
                    <div class="mb-3 d-none" id="change-container">
                        <label for="change" class="form-label">Cambio</label>
                        <input type="text" class="form-control" id="change" readonly>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-danger btn-lg" id="cancel-sale">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </button>
                        <button class="btn btn-success btn-lg" id="complete-sale">
                            <i class="fas fa-check-circle"></i> Finalizar Venta ($<span id="total-amount">0.00</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nuevo cliente -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="new-client-form">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>RFC</label>
                        <input type="text" class="form-control" name="rfc">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" class="form-control" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="save-client">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cantidad de producto -->
<div class="modal fade" id="quantityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cantidad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label id="product-name">Producto</label>
                    <input type="number" class="form-control" id="product-quantity" min="1" value="1">
                    <small class="form-text text-muted">
                        Disponible: <span id="available-stock">0</span>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="add-to-sale">Agregar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Variables para la venta
    let saleItems = [];
    let currentProduct = null;
    const IVA_RATE = 0.16;

    // Inicializar select2 para clientes
    $('#client-select').select2({
        placeholder: "Seleccione un cliente",
        allowClear: true
    });

    // Filtrar productos por categoría
    $('.btn-group button').on('click', function() {
        const category = $(this).text().trim();
        $('.btn-group button').removeClass('active');
        $(this).addClass('active');
        
        if (category === 'Todos') {
            $('.product-item').show();
        } else {
            $('.product-item').hide();
            $('.product-item').filter(function() {
                // Aquí deberías comparar con el nombre de la categoría real
                // Esto es solo un ejemplo
                return $(this).data('category-name') === category;
            }).show();
        }
    });

    // Buscar productos
    $('#search-product').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.product-item').each(function() {
            const productText = $(this).text().toLowerCase();
            if (productText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Seleccionar producto para venta
    $('.product-card').on('click', function() {
        currentProduct = {
            id: $(this).data('id'),
            name: $(this).find('.card-title').text(),
            price: parseFloat($(this).data('price')),
            stock: parseInt($(this).data('stock')),
            image: $(this).find('img').attr('src') || ''
        };

        $('#product-name').text(currentProduct.name);
        $('#product-quantity').val(1).attr('max', currentProduct.stock);
        $('#available-stock').text(currentProduct.stock);
        $('#quantityModal').modal('show');
    });

    // Agregar producto a la venta
    $('#add-to-sale').on('click', function() {
        const quantity = parseInt($('#product-quantity').val());
        
        if (quantity < 1 || quantity > currentProduct.stock) {
            alert('Cantidad no válida');
            return;
        }

        // Verificar si el producto ya está en la venta
        const existingItemIndex = saleItems.findIndex(item => item.id === currentProduct.id);
        
        if (existingItemIndex >= 0) {
            // Actualizar cantidad si ya existe
            saleItems[existingItemIndex].quantity += quantity;
        } else {
            // Agregar nuevo item
            saleItems.push({
                ...currentProduct,
                quantity: quantity
            });
        }

        updateSaleTable();
        $('#quantityModal').modal('hide');
    });

    // Actualizar tabla de venta
    function updateSaleTable() {
        const $tbody = $('#sale-items tbody');
        $tbody.empty();
        
        let subtotal = 0;
        
        saleItems.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            
            $tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.name}</td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary minus-btn" data-index="${index}">-</button>
                            <input type="number" class="form-control text-center quantity-input" 
                                   value="${item.quantity}" min="1" max="${item.stock}" data-index="${index}">
                            <button class="btn btn-outline-secondary plus-btn" data-index="${index}">+</button>
                        </div>
                    </td>
                    <td>$${itemTotal.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
        
        const iva = subtotal * IVA_RATE;
        const total = subtotal + iva;
        
        $('#subtotal').text(`$${subtotal.toFixed(2)}`);
        $('#iva').text(`$${iva.toFixed(2)}`);
        $('#total').text(`$${total.toFixed(2)}`);
        $('#total-amount').text(total.toFixed(2));
        
        // Mostrar/ocultar sección de efectivo según método de pago
        toggleCashSection();
    }

    // Cambiar método de pago
    $('input[name="payment_method"]').on('change', function() {
        toggleCashSection();
    });

    function toggleCashSection() {
        if ($('input[name="payment_method"]:checked').val() === 'cash') {
            $('#cash-received-container').show();
            $('#change-container').addClass('d-none');
        } else {
            $('#cash-received-container').hide();
            $('#change-container').addClass('d-none');
        }
    }

    // Calcular cambio
    $('#cash-received').on('change', function() {
        const total = parseFloat($('#total').text().replace('$', ''));
        const received = parseFloat($(this).val()) || 0;
        
        if (received >= total) {
            const change = received - total;
            $('#change').val(`$${change.toFixed(2)}`);
            $('#change-container').removeClass('d-none');
        } else {
            $('#change-container').addClass('d-none');
        }
    });

    // Eventos para modificar cantidades
    $('#sale-items').on('click', '.minus-btn', function() {
        const index = $(this).data('index');
        if (saleItems[index].quantity > 1) {
            saleItems[index].quantity--;
            updateSaleTable();
        }
    });

    $('#sale-items').on('click', '.plus-btn', function() {
        const index = $(this).data('index');
        if (saleItems[index].quantity < saleItems[index].stock) {
            saleItems[index].quantity++;
            updateSaleTable();
        }
    });

    $('#sale-items').on('change', '.quantity-input', function() {
        const index = $(this).data('index');
        const newQuantity = parseInt($(this).val()) || 1;
        
        if (newQuantity >= 1 && newQuantity <= saleItems[index].stock) {
            saleItems[index].quantity = newQuantity;
            updateSaleTable();
        } else {
            $(this).val(saleItems[index].quantity);
        }
    });

    $('#sale-items').on('click', '.remove-item', function() {
        const index = $(this).data('index');
        saleItems.splice(index, 1);
        updateSaleTable();
    });

    // Guardar nuevo cliente
    $('#save-client').on('click', function() {
        // Aquí iría la lógica para guardar el cliente via AJAX
        // y luego agregarlo al select de clientes
        alert('Funcionalidad para guardar cliente - implementar AJAX');
        $('#newClientModal').modal('hide');
    });

    // Cancelar venta
    $('#cancel-sale').on('click', function() {
        if (saleItems.length > 0 && !confirm('¿Estás seguro de cancelar esta venta?')) {
            return;
        }
        resetSale();
    });

    // Finalizar venta
    $('#complete-sale').on('click', function() {
        if (saleItems.length === 0) {
            alert('No hay productos en la venta');
            return;
        }
        
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        const clientId = $('#client-select').val() || null;
        
        if (paymentMethod === 'cash') {
            const received = parseFloat($('#cash-received').val()) || 0;
            const total = parseFloat($('#total').text().replace('$', ''));
            
            if (received < total) {
                alert('El efectivo recibido es menor que el total');
                return;
            }
        }
        
        // Aquí iría la lógica para enviar la venta al servidor via AJAX
        const saleData = {
            client_id: clientId,
            items: saleItems,
            payment_method: paymentMethod,
            subtotal: parseFloat($('#subtotal').text().replace('$', '')),
            iva: parseFloat($('#iva').text().replace('$', '')),
            total: parseFloat($('#total').text().replace('$', ''))
        };
        
        console.log('Datos de venta a enviar:', saleData);
        alert('Funcionalidad para finalizar venta - implementar AJAX');
        
        // Después de guardar, imprimir ticket y resetear
        printTicket();
        resetSale();
    });

    // Imprimir ticket (ejemplo)
    function printTicket() {
        alert('Funcionalidad para imprimir ticket - implementar según necesidad');
    }

    // Resetear venta
    function resetSale() {
        saleItems = [];
        updateSaleTable();
        $('#client-select').val(null).trigger('change');
        $('input[name="payment_method"][value="cash"]').prop('checked', true).trigger('change');
        $('#cash-received').val('');
        $('#change-container').addClass('d-none');
    }
});
</script>
@endsection

@section('styles')
<style>
    .product-card {
        cursor: pointer;
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    #sale-items tbody tr:hover {
        background-color: #f8f9fa;
    }
    .quantity-input {
        width: 50px;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endsection