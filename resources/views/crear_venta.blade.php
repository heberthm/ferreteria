@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nueva Venta</h1>
    
    <form action="{{ route('crear_venta') }}" method="POST">
        @csrf
        
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="invoice_number" class="form-label">N° Factura</label>
                <input type="text" class="form-control" id="invoice_number" 
                      value="" readonly> 
            </div>
            <div class="col-md-3">
                <label for="sale_date" class="form-label">Fecha</label>
                <input type="datetime-local" class="form-control" id="sale_date" 
                       name="sale_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="col-md-6">
                <label for="customer_id" class="form-label">Cliente</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">Seleccione un cliente</option>
                  
                </select>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="payment_method" class="form-label">Método de Pago</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="cash">Efectivo</option>
                    <option value="credit">Crédito</option>
                    <option value="debit_card">Tarjeta Débito</option>
                    <option value="transfer">Transferencia</option>
                </select>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">Productos</div>
            <div class="card-body">
                <div id="products-container">
                    <!-- Productos se agregarán aquí dinámicamente -->
                </div>
                <button type="button" class="btn btn-primary mt-3" id="add-product">
                    Agregar Producto
                </button>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-success">Registrar Venta</button>
        <a href="{{ route('crear_venta') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const productsContainer = document.getElementById('products-container');
    const addProductBtn = document.getElementById('add-product');
  
    
    let productCounter = 0;
    
    addProductBtn.addEventListener('click', function() {
        const productDiv = document.createElement('div');
        productDiv.className = 'row mb-3 product-row';
        productDiv.dataset.index = productCounter;
        
        productDiv.innerHTML = `
            <div class="col-md-5">
                <select class="form-select product-select" name="products[${productCounter}][id]" required>
                    <option value="">Seleccione un producto</option>
                    ${products.map(p => 
                        `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">
                            ${p.name} - $${p.price} (Stock: ${p.stock})
                        </option>`
                    ).join('')}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control quantity" 
                 
                <small class="text-muted stock-message"></small>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control subtotal" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-product">Eliminar</button>
            </div>
        `;
        
        productsContainer.appendChild(productDiv);
        productCounter++;
        
        // Agregar eventos al nuevo producto
        setupProductEvents(productDiv);
    });
    
    function setupProductEvents(productDiv) {
        const productSelect = productDiv.querySelector('.product-select');
        const quantityInput = productDiv.querySelector('.quantity');
        const subtotalInput = productDiv.querySelector('.subtotal');
        const stockMessage = productDiv.querySelector('.stock-message');
        const removeBtn = productDiv.querySelector('.remove-product');
        
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
            const stock = selectedOption ? parseInt(selectedOption.dataset.stock) : 0;
            
            stockMessage.textContent = `Máximo: ${stock}`;
            quantityInput.max = stock;
            calculateSubtotal();
        });
        
        quantityInput.addEventListener('input', calculateSubtotal);
        
        function calculateSubtotal() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (!selectedOption) return;
            
            const price = parseFloat(selectedOption.dataset.price);
            const quantity = parseInt(quantityInput.value) || 0;
            subtotalInput.value = `$${(price * quantity).toFixed(2)}`;
        }
        
        removeBtn.addEventListener('click', function() {
            productDiv.remove();
        });
    }
    
    // Agregar un producto por defecto al cargar
    addProductBtn.click();
});
</script>
@endsection