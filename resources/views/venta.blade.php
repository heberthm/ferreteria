

@extends('layouts.app')

@section('content')

<br>

<div class="pos-container">
    <div class="row">
        <!-- Panel izquierdo - Productos -->
        <div class="col-md-7">
            <div class="card pos-products-panel">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-0">Productos</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Buscar por nombre o código..." id="product-search">
                                <div class="input-group-append">
                                    <button class="btn btn-light" type="button" id="search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Categorías -->
                    <div class="categories-scroll mb-3">
                        <div class="d-flex flex-wrap">
                            <button class="btn btn-outline-secondary category-btn active" data-category="all">Todos</button>
                            {{--
                            @foreach($categories as $category)
                                <button class="btn btn-outline-secondary category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                            @endforeach
                            --}}
                        </div>
                    </div>
                    
                    <!-- Lista de productos -->
                    <div class="products-grid" id="products-container">

                    {{--
                        @foreach($products as $product)
                            <div class="product-card" data-product-id="{{ $product->id }}" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}" data-code="{{ strtolower($product->code) }}">
                                <div class="product-image">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="img-fluid product-img">
                                    @else
                                        <div class="no-image"><i class="fas fa-box-open"></i></div>
                                    @endif
                                </div>
                                <div class="product-info">
                                    <h6 class="product-name">{{ $product->name }}</h6>
                                    <small class="text-muted product-code">Código: {{ $product->code }}</small>
                                    <div class="product-price">${{ number_format($product->price, 2) }}</div>
                                    <div class="product-stock">Disponible: {{ $product->stock }}</div>
                                </div>
                            </div>
                        @endforeach

                        --}}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho - Carrito y total -->
        <div class="col-md-5">
            <div class="card pos-cart-panel">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Venta actual</h4>
                </div>
                <div class="card-body">
                    <!-- Información del cliente -->
                    <div class="customer-section mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Cliente</h6>
                            
                            <button class="btn btn-sm btn-outline-primary" id="seleccionar_cliente" data-toggle="modal" data-target="#modalCliente">
                                <i class="fas fa-user-plus"></i> Seleccionar
                            </button>

                        </div>
                        <div id="customer-info" class="mt-2 p-2 bg-light rounded">
                            <div class="text-center text-muted">
                                <i class="fas fa-user fa-2x mb-1"></i>
                                <p class="mb-0">Cliente general</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de productos en el carrito -->
                    <div class="cart-items">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th width="80">Cant.</th>
                                        <th width="100">P. Unit.</th>
                                        <th width="100">Total</th>
                                        <th width="40"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-list">
                                    <!-- Los items del carrito se agregarán aquí dinámicamente -->
                                    <tr class="empty-cart">
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                            <p>No hay productos agregados</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Resumen de la venta -->
                    <div class="sale-summary mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuento:</span>
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <input type="number" class="form-control" id="discount-input" value="0" min="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                         {{--   <span>Impuesto ({{ $taxRate }}%):</span> --}}
                            <span id="taxes">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold total">
                            <span>Total:</span>
                            <span id="total">$0.00</span>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="pos-actions mt-4">
                        <button class="btn btn-danger btn-block mb-2" id="cancel-sale" disabled>
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button class="btn btn-success btn-block" id="complete-sale" disabled>
                            <i class="fas fa-check"></i> Completar venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Seleccionar cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar cliente..." id="customer-search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Identificación</th>
                                <th>Teléfono</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        {{--
                            @foreach($customers as $customer)
                                <tr class="customer-row" data-customer-id="{{ $customer->id }}">
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->identification }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td class="text-right">
                                        <button class="btn btn-sm btn-primary select-customer-btn">Seleccionar</button>
                                    </td>
                                </tr>
                            @endforeach
                            --}}

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cantidad de producto -->
<div class="modal fade" id="quantityModal" tabindex="-1" role="dialog" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quantityModalLabel">Cantidad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="product-quantity">Ingrese la cantidad:</label>
                    <input type="number" class="form-control" id="product-quantity" min="1" value="1">
                </div>
                <div class="product-info-modal text-center mb-3">
                    <img id="modal-product-image" src="" class="img-fluid mb-2" style="max-height: 80px; display: none;">
                    <h6 id="modal-product-name"></h6>
                    <div class="product-price-modal" id="modal-product-price"></div>
                    <div class="product-stock-modal text-muted small" id="modal-product-stock"></div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-quantity">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pos-container {
        height: calc(100vh - 120px);
    }
    
    .pos-products-panel, .pos-cart-panel {
        height: 100%;
    }
    
    .categories-scroll {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 5px;
    }
    
    .categories-scroll .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 15px;
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        padding: 5px;
    }
    
    .product-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .product-image {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        border-radius: 3px;
        overflow: hidden;
    }
    
    .product-image img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    
    .no-image {
        font-size: 2rem;
        color: #6c757d;
    }
    
    .product-info {
        text-align: center;
    }
    
    .product-name {
        font-weight: 600;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .product-code {
        display: block;
        margin-bottom: 5px;
        font-size: 0.8rem;
    }
    
    .product-price {
        font-weight: bold;
        color: #28a745;
    }
    
    .product-stock {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .cart-items {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    
    .sale-summary {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
    
    .total {
        font-size: 1.2rem;
        border-top: 1px solid #dee2e6;
        padding-top: 10px;
        margin-top: 10px;
    }
    
    #discount-input {
        text-align: right;
    }
    
    .product-info-modal {
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Variables para el carrito
        let cart = [];
        let currentCustomer = null;
        let selectedProduct = null;
     {{--   const taxRate = {{ $taxRate }};  --}}
        
        // Filtrar productos por categoría
        $('.category-btn').click(function() {
            $('.category-btn').removeClass('active');
            $(this).addClass('active');
            
            const categoryId = $(this).data('category');
            
            if(categoryId === 'all') {
                $('.product-card').show();
            } else {
                $('.product-card').hide();
                $(`.product-card[data-category="${categoryId}"]`).show();
            }
        });
        
        // Buscar productos por nombre o código
        $('#product-search, #search-btn').on('input click', function() {
            const searchTerm = $('#product-search').val().toLowerCase();
            
            if(searchTerm === '') {
                $('.product-card').show();
                return;
            }
            
            $('.product-card').each(function() {
                const productName = $(this).data('name');
                const productCode = $(this).data('code');
                
                if(productName.includes(searchTerm) || productCode.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Seleccionar producto
        $('.product-card').click(function() {
            const productId = $(this).data('product-id');
            const productElement = $(this);
            
            // Obtener datos del producto
            selectedProduct = {
                id: productId,
                name: productElement.find('.product-name').text(),
                code: productElement.find('.product-code').text().replace('Código: ', ''),
                price: parseFloat(productElement.find('.product-price').text().replace('$', '')),
                stock: parseInt(productElement.find('.product-stock').text().replace('Disponible: ', '')),
                image: productElement.find('.product-img').attr('src') || ''
            };
            
            // Configurar modal
            $('#modal-product-name').text(selectedProduct.name);
            $('#modal-product-price').text('Precio: $' + selectedProduct.price.toFixed(2));
            $('#modal-product-stock').text('Disponible: ' + selectedProduct.stock);
            
            if(selectedProduct.image) {
                $('#modal-product-image').attr('src', selectedProduct.image).show();
            } else {
                $('#modal-product-image').hide();
            }
            
            $('#product-quantity').val(1);
            $('#product-quantity').attr('max', selectedProduct.stock);
            $('#quantityModal').modal('show');
        });
        
        // Confirmar cantidad
        $('#confirm-quantity').click(function() {
            const quantity = parseInt($('#product-quantity').val());
            
            if(quantity > 0 && quantity <= selectedProduct.stock) {
                addToCart(selectedProduct, quantity);
                $('#quantityModal').modal('hide');
            } else {
                alert('La cantidad debe ser mayor a 0 y no puede exceder el stock disponible.');
            }
        });
        
        // Seleccionar cliente
        $('#select-customer').click(function() {
            $('#customerModal').modal('show');
        });
        
        // Seleccionar cliente desde el modal
        $('.select-customer-btn').click(function() {
            const row = $(this).closest('.customer-row');
            const customerId = row.data('customer-id');
            const customerName = row.find('td:first').text();
            
            currentCustomer = {
                id: customerId,
                name: customerName
            };
            
            $('#customer-info').html(`
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${customerName}</strong>
                    </div>
                    <button class="btn btn-sm btn-outline-danger remove-customer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
            
            $('#customerModal').modal('hide');
            updateCartButtons();
        });
        
        // Quitar cliente
        $(document).on('click', '.remove-customer', function() {
            currentCustomer = null;
            $('#customer-info').html(`
                <div class="text-center text-muted">
                    <i class="fas fa-user fa-2x mb-1"></i>
                    <p class="mb-0">Cliente general</p>
                </div>
            `);
            updateCartButtons();
        });
        
        // Función para agregar producto al carrito
        function addToCart(product, quantity) {
            // Verificar si el producto ya está en el carrito
            const existingItemIndex = cart.findIndex(item => item.id === product.id);
            
            if(existingItemIndex >= 0) {
                // Actualizar cantidad si ya existe
                cart[existingItemIndex].quantity += quantity;
            } else {
                // Agregar nuevo item al carrito
                cart.push({
                    id: product.id,
                    name: product.name,
                    code: product.code,
                    price: product.price,
                    quantity: quantity,
                    image: product.image
                });
            }
            
            updateCart();
        }
        
        // Función para actualizar el carrito
        function updateCart() {
            if(cart.length === 0) {
                $('#cart-items-list').html(`
                    <tr class="empty-cart">
                        <td colspan="5" class="text-center text-muted py-3">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <p>No hay productos agregados</p>
                        </td>
                    </tr>
                `);
                
                $('#subtotal').text('$0.00');
                $('#taxes').text('$0.00');
                $('#total').text('$0.00');
            } else {
                let html = '';
                let subtotal = 0;
                
                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    ${item.image ? 
                                        `<img src="${item.image}" class="img-thumbnail mr-2" style="width: 40px; height: 40px;">` : 
                                        `<div class="img-thumbnail mr-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-box-open text-muted"></i>
                                        </div>`
                                    }
                                    <div>
                                        <div>${item.name}</div>
                                        <small class="text-muted">${item.code}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control item-quantity" value="${item.quantity}" min="1" 
                                        data-product-id="${item.id}" max="${item.maxStock || 1000}">
                                </div>
                            </td>
                            <td>$${item.price.toFixed(2)}</td>
                            <td>$${(itemTotal).toFixed(2)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger remove-item" data-product-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                $('#cart-items-list').html(html);
                
                // Calcular totales
                const discountPercentage = parseInt($('#discount-input').val()) || 0;
                const discountAmount = subtotal * (discountPercentage / 100);
                const taxes = (subtotal - discountAmount) * (taxRate / 100);
                const total = subtotal - discountAmount + taxes;
                
                $('#subtotal').text('$' + subtotal.toFixed(2));
                $('#taxes').text('$' + taxes.toFixed(2));
                $('#total').text('$' + total.toFixed(2));
            }
            
            updateCartButtons();
        }
        
        // Función para actualizar estado de botones
        function updateCartButtons() {
            const hasItems = cart.length > 0;
            
            $('#cancel-sale').prop('disabled', !hasItems);
            $('#complete-sale').prop('disabled', !hasItems);
        }
        
        // Cambiar cantidad de un item
        $(document).on('change', '.item-quantity', function() {
            const productId = $(this).data('product-id');
            const quantity = parseInt($(this).val());
            
            if(quantity > 0) {
                const itemIndex = cart.findIndex(item => item.id === productId);
                if(itemIndex >= 0) {
                    cart[itemIndex].quantity = quantity;
                    updateCart();
                }
            } else {
                $(this).val(1);
            }
        });
        
        // Eliminar item del carrito
        $(document).on('click', '.remove-item', function() {
            const productId = $(this).data('product-id');
            cart = cart.filter(item => item.id !== productId);
            updateCart();
        });
        
        // Aplicar descuento
        $('#discount-input').on('change', function() {
            updateCart();
        });
        
        // Cancelar venta
        $('#cancel-sale').click(function() {
            if(confirm('¿Está seguro de cancelar esta venta?')) {
                cart = [];
                currentCustomer = null;
                updateCart();
                
                $('#customer-info').html(`
                    <div class="text-center text-muted">
                        <i class="fas fa-user fa-2x mb-1"></i>
                        <p class="mb-0">Cliente general</p>
                    </div>
                `);
                
                $('#discount-input').val(0);
            }
        });
        
        // Completar venta
        $('#complete-sale').click(function() {
            if(cart.length === 0) return;
            
            // Preparar datos para enviar
            const saleData = {
                customer_id: currentCustomer ? currentCustomer.id : null,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    price: item.price
                })),
                discount_percentage: parseInt($('#discount-input').val()) || 0,
                tax_rate: taxRate
            };
            
            // Aquí iría la petición AJAX para guardar la venta
            console.log('Datos de la venta:', saleData);
            
            // Simular envío
            $.ajax({
                url: '{{ route("crear_venta") }}',
                method: 'POST',
                data: saleData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Venta completada con éxito! N° ' + response.sale_number);
                    
                    // Limpiar después de la venta
                    cart = [];
                    currentCustomer = null;
                    updateCart();
                    
                    $('#customer-info').html(`
                        <div class="text-center text-muted">
                            <i class="fas fa-user fa-2x mb-1"></i>
                            <p class="mb-0">Cliente general</p>
                        </div>
                    `);
                    
                    $('#discount-input').val(0);
                },
                error: function(xhr) {
                    alert('Error al procesar la venta: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endpush