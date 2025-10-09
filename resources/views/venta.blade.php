@extends('layouts.app')

@section('title', 'Punto de Venta - Ferretería')


@section('content')

<br>
<div class="row">
    <!-- Columna de Búsqueda y Productos -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title">Información de la Venta</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" >
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Información del Cliente -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="customer-info bg-light p-3 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 id="customerName">Cliente: <span class="text-muted">Consumidor Final</span></h5>
                                    <small id="customerDetails" class="text-muted">RFC: XAXX010101000 - Sin información adicional</small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#customerModal">
                                        <i class="fas fa-search"></i> Buscar Cliente (F3)
                                    </button>
                                    <button type="button" id="btnRemoveCustomer" class="btn btn-danger btn-sm d-none">
                                        <i class="fas fa-times"></i> Quitar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Búsqueda de Productos -->
                <div class="input-group mb-3">
                    <input type="text" id="searchProduct" class="form-control form-control-lg" 
                           placeholder="Buscar producto (F2)...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="btnSearch">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Lista de Productos -->
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover table-sm" id="productsTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio Unitario</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <!-- Los productos se cargarán aquí via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna de Carrito y Total -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title">Venta Actual</h3>
            </div>
            <div class="card-body">
                <!-- Lista de Productos en el Carrito -->
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm" id="cartTable">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cant</th>
                                <th>P. Unit</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Items del carrito -->
                        </tbody>
                    </table>
                </div>

                <!-- Totales -->
                <div class="row mt-3">
                    <div class="col-6">
                        <strong>Subtotal:</strong>
                    </div>
                    <div class="col-6 text-right">
                        <span id="subtotal">$0.00</span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="ivaToggle">
                                <input type="checkbox" id="ivaToggle"> IVA (16%)
                            </label>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        <span id="ivaAmount">$0.00</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <strong>Total:</strong>
                    </div>
                    <div class="col-6 text-right">
                        <span id="totalAmount" class="h4">$0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h3 class="card-title">Método de Pago</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-primary active">
                            <input type="radio" name="paymentMethod" value="efectivo" checked> Efectivo
                        </label>
                        <label class="btn btn-outline-primary">
                            <input type="radio" name="paymentMethod" value="tarjeta"> Tarjeta
                        </label>
                    </div>
                </div>

                <!-- Efectivo -->
                <div id="cashPayment" class="payment-method">
                    <div class="form-group">
                        <label for="cashReceived">Efectivo Recibido:</label>
                        <input type="number" id="cashReceived" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="cashChange">Cambio:</label>
                        <input type="text" id="cashChange" class="form-control" readonly>
                    </div>
                </div>

                <!-- Tarjeta -->
                <div id="cardPayment" class="payment-method d-none">
                    <div class="form-group">
                        <label for="cardNumber">Número de Tarjeta:</label>
                        <input type="text" id="cardNumber" class="form-control" placeholder="**** **** **** ****">
                    </div>
                    <div class="form-group">
                        <label for="cardAuth">Autorización:</label>
                        <input type="text" id="cardAuth" class="form-control">
                    </div>
                </div>

                <button type="button" id="btnProcessSale" class="btn btn-success btn-lg btn-block">
                    <i class="fas fa-cash-register"></i> Procesar Venta (F9)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Búsqueda de Clientes -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Buscar Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" id="searchCustomer" class="form-control" placeholder="Buscar por nombre, RFC, email o teléfono...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="btnSearchCustomer">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Seleccionar</th>
                                <th>Nombre</th>
                                <th>RFC</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody id="customersList">
                            <!-- Los clientes se cargarán aquí via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSelectCustomer" disabled>Seleccionar Cliente</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .product-item:hover, .customer-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .payment-method {
        transition: all 0.3s ease;
    }
    #cartTable tbody tr {
        font-size: 0.9em;
    }
    .shortcut-key {
        background-color: #6c757d;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.8em;
    }
    .customer-info {
        border-left: 4px solid #007bff;
    }
    .customer-selected {
        background-color: #d4edda !important;
        border-left-color: #28a745 !important;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let cart = [];
    let subtotal = 0;
    let iva = 0;
    let total = 0;
    const ivaRate = 0.16;
    let selectedCustomer = null;

    // Atajos de teclado
    $(document).keydown(function(e) {
        // F2 - Buscar producto
        if (e.keyCode === 113) {
            e.preventDefault();
            $('#searchProduct').focus();
            return false;
        }

        // F3 - Buscar cliente
        if (e.keyCode === 114) {
            e.preventDefault();
            $('#customerModal').modal('show');
            $('#searchCustomer').focus();
            return false;
        }

        // F9 - Procesar venta
        if (e.keyCode === 120) {
            e.preventDefault();
            processSale();
            return false;
        }

        // ESC - Limpiar búsqueda
        if (e.keyCode === 27) {
            if ($('#customerModal').is(':visible')) {
                $('#searchCustomer').val('').focus();
            } else {
                $('#searchProduct').val('').focus();
            }
            return false;
        }
    });

    // Búsqueda de productos
    $('#searchProduct').on('input', function() {
        searchProducts($(this).val());
    });

    $('#btnSearch').click(function() {
        searchProducts($('#searchProduct').val());
    });

    // Búsqueda de clientes
    $('#searchCustomer').on('input', function() {
        if ($(this).val().length >= 2) {
            searchCustomers($(this).val());
        }
    });

    $('#btnSearchCustomer').click(function() {
        searchCustomers($('#searchCustomer').val());
    });

    // Selección de cliente en el modal
    $(document).on('click', '.customer-item', function() {
        $('.customer-item').removeClass('table-active');
        $(this).addClass('table-active');
        $('#btnSelectCustomer').prop('disabled', false);
    });

    // Doble click para seleccionar cliente
    $(document).on('dblclick', '.customer-item', function() {
        selectCustomerFromModal();
    });

    // Botón seleccionar cliente
    $('#btnSelectCustomer').click(function() {
        selectCustomerFromModal();
    });

    // Quitar cliente seleccionado
    $('#btnRemoveCustomer').click(function() {
        removeCustomer();
    });

    // Toggle IVA
    $('#ivaToggle').change(function() {
        calculateTotals();
    });

    // Cambio método de pago
    $('input[name="paymentMethod"]').change(function() {
        $('.payment-method').addClass('d-none');
        $('#' + $(this).val() + 'Payment').removeClass('d-none');
    });

    // Calcular cambio en efectivo
    $('#cashReceived').on('input', function() {
        calculateChange();
    });

    // Procesar venta
    $('#btnProcessSale').click(processSale);

    function searchProducts(query) {
        if (query.length < 2) {
            $('#productsList').empty();
            return;
        }

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                query: query
            },
            success: function(response) {
                displayProducts(response.products);
            },
            error: function(xhr) {
                console.error('Error en la búsqueda:', xhr);
            }
        });
    }

    function searchCustomers(query) {
        if (!query) {
            $('#customersList').html('<tr><td colspan="6" class="text-center">Ingrese un término de búsqueda</td></tr>');
            return;
        }

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                query: query
            },
            success: function(response) {
                displayCustomers(response.customers);
            },
            error: function(xhr) {
                console.error('Error en la búsqueda de clientes:', xhr);
            }
        });
    }

    function displayCustomers(customers) {
        const tbody = $('#customersList');
        tbody.empty();

        if (customers.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center">No se encontraron clientes</td></tr>');
            return;
        }

        customers.forEach(customer => {
            const row = `
                <tr class="customer-item" data-customer='${JSON.stringify(customer)}'>
                    <td>
                        <input type="radio" name="selectedCustomer" value="${customer.id}">
                    </td>
                    <td>${customer.name}</td>
                    <td>${customer.rfc || 'N/A'}</td>
                    <td>${customer.email || 'N/A'}</td>
                    <td>${customer.phone || 'N/A'}</td>
                    <td>
                        <span class="badge ${customer.type === 'business' ? 'badge-primary' : 'badge-secondary'}">
                            ${customer.type === 'business' ? 'Empresa' : 'Individual'}
                        </span>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function selectCustomerFromModal() {
        const selectedRow = $('.customer-item.table-active');
        if (selectedRow.length === 0) {
            alert('Seleccione un cliente de la lista');
            return;
        }

        const customerData = selectedRow.data('customer');
        setSelectedCustomer(customerData);
        $('#customerModal').modal('hide');
    }

    function setSelectedCustomer(customer) {
        selectedCustomer = customer;
        
        // Actualizar interfaz
        $('#customerName').html(`Cliente: <strong>${customer.name}</strong>`);
        
        let details = [];
        if (customer.rfc) details.push(`RFC: ${customer.rfc}`);
        if (customer.email) details.push(`Email: ${customer.email}`);
        if (customer.phone) details.push(`Tel: ${customer.phone}`);
        if (customer.address) details.push(`Dir: ${customer.address}`);
        
        $('#customerDetails').text(details.join(' - '));
        $('.customer-info').addClass('customer-selected');
        $('#btnRemoveCustomer').removeClass('d-none');
    }

    function removeCustomer() {
        selectedCustomer = null;
        $('#customerName').html('Cliente: <span class="text-muted">Consumidor Final</span>');
        $('#customerDetails').text('RFC: XAXX010101000 - Sin información adicional');
        $('.customer-info').removeClass('customer-selected');
        $('#btnRemoveCustomer').addClass('d-none');
    }

    function displayProducts(products) {
        const tbody = $('#productsList');
        tbody.empty();

        if (products.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center">No se encontraron productos</td></tr>');
            return;
        }

        products.forEach(product => {
            const row = `
                <tr class="product-item" data-product='${JSON.stringify(product)}'>
                    <td>${product.code}</td>
                    <td>${product.name}</td>
                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.stock}</td>
                    <td>
                        <button class="btn btn-sm btn-success btn-add-to-cart">
                            <i class="fas fa-plus"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Evento para agregar al carrito
        $('.btn-add-to-cart').click(function() {
            const productData = $(this).closest('tr').data('product');
            addToCart(productData);
        });

        // Doble click en fila para agregar
        $('.product-item').dblclick(function() {
            const productData = $(this).data('product');
            addToCart(productData);
        });
    }

    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        
        if (existingItem) {
            if (existingItem.quantity < product.stock) {
                existingItem.quantity += 1;
            } else {
                alert('No hay suficiente stock disponible');
                return;
            }
        } else {
            if (product.stock > 0) {
                cart.push({
                    id: product.id,
                    code: product.code,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: 1,
                    stock: product.stock
                });
            } else {
                alert('Producto sin stock disponible');
                return;
            }
        }

        updateCartDisplay();
        $('#searchProduct').val('').focus();
    }

    function updateCartDisplay() {
        const tbody = $('#cartItems');
        tbody.empty();

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            const row = `
                <tr>
                    <td>${item.name}<br><small class="text-muted">${item.code}</small></td>
                    <td>
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary btn-quantity-minus" data-index="${index}">-</button>
                            <input type="number" class="form-control form-control-sm quantity-input" 
                                   value="${item.quantity}" min="1" max="${item.stock}" data-index="${index}">
                            <button class="btn btn-outline-secondary btn-quantity-plus" data-index="${index}">+</button>
                        </div>
                    </td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td>$${itemTotal.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger btn-remove-item" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        // Eventos para modificar cantidades
        $('.btn-quantity-minus').click(function() {
            const index = $(this).data('index');
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1;
                updateCartDisplay();
            }
        });

        $('.btn-quantity-plus').click(function() {
            const index = $(this).data('index');
            if (cart[index].quantity < cart[index].stock) {
                cart[index].quantity += 1;
                updateCartDisplay();
            }
        });

        $('.quantity-input').on('change', function() {
            const index = $(this).data('index');
            const newQuantity = parseInt($(this).val());
            
            if (newQuantity >= 1 && newQuantity <= cart[index].stock) {
                cart[index].quantity = newQuantity;
                updateCartDisplay();
            } else {
                $(this).val(cart[index].quantity);
            }
        });

        $('.btn-remove-item').click(function() {
            const index = $(this).data('index');
            cart.splice(index, 1);
            updateCartDisplay();
        });

        calculateTotals();
    }

    function calculateTotals() {
        subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        if ($('#ivaToggle').is(':checked')) {
            iva = subtotal * ivaRate;
        } else {
            iva = 0;
        }

        total = subtotal + iva;

        $('#subtotal').text('$' + subtotal.toFixed(2));
        $('#ivaAmount').text('$' + iva.toFixed(2));
        $('#totalAmount').text('$' + total.toFixed(2));

        calculateChange();
    }

    function calculateChange() {
        const received = parseFloat($('#cashReceived').val()) || 0;
        const change = received - total;
        $('#cashChange').val(change >= 0 ? '$' + change.toFixed(2) : '-');
    }

    function processSale() {
        if (cart.length === 0) {
            alert('No hay productos en el carrito');
            return;
        }

        const paymentMethod = $('input[name="paymentMethod"]:checked').val();
        const paymentData = {};

        if (paymentMethod === 'efectivo') {
            const received = parseFloat($('#cashReceived').val()) || 0;
            if (received < total) {
                alert('El efectivo recibido es menor al total');
                return;
            }
            paymentData.cash_received = received;
            paymentData.change = received - total;
        } else {
            paymentData.card_number = $('#cardNumber').val();
            paymentData.authorization = $('#cardAuth').val();
            
            if (!paymentData.card_number || !paymentData.authorization) {
                alert('Complete los datos de la tarjeta');
                return;
            }
        }

        const saleData = {
            _token: '{{ csrf_token() }}',
            items: cart,
            subtotal: subtotal,
            iva: iva,
            total: total,
            apply_iva: $('#ivaToggle').is(':checked'),
            payment_method: paymentMethod,
            payment_data: paymentData,
            customer_id: selectedCustomer ? selectedCustomer.id : null
        };

        $.ajax({
            url: '',
            method: 'POST',
            data: saleData,
            success: function(response) {
                if (response.success) {
                    alert('Venta procesada exitosamente');
                    resetPOS();
                } else {
                    alert('Error al procesar la venta: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error al procesar la venta');
                console.error('Error:', xhr);
            }
        });
    }

    function resetPOS() {
        cart = [];
        selectedCustomer = null;
        $('#cartItems').empty();
        $('#searchProduct').val('').focus();
        $('#cashReceived').val('');
        $('#cardNumber').val('');
        $('#cardAuth').val('');
        $('#ivaToggle').prop('checked', false);
        removeCustomer();
        calculateTotals();
    }

    // Enfocar campo de búsqueda al cargar
    $('#searchProduct').focus();
});
</script>

<script>
$(document).ready(function() {
    // Cerrar modal con la X
    $(document).on('click', '#modalVerProducto .close', function() {
        $('#customerModal').modal('hide');
    });
    
    // Cerrar modal con el botón Cancelar
    $(document).on('click', '#modalVerProducto .btn-secondary', function() {
        $('#customerModal').modal('hide');
    });
    
    // Forzar la inicialización del modal
    $('#customerModal').modal({
        backdrop: true,
        keyboard: true,
        show: false
    });
});
</script>

@stop