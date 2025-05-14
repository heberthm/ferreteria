<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function index()
    {
        $sales = Venta::with(['id_cliente', 'user'])->latest()->paginate(10);
        return view('crear_venta');
    }

    public function create()
    {
        $customers = Cliente::all();
        $products = Producto::where('stock', '>', 0)->get();
        $invoiceNumber = Venta::generateInvoiceNumber();
        
        return view('sales.create', compact('clientes', 'productos', 'numero_factura'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:customers,id',
            'fecha_venta' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,credito,tarjeta_credito,transferencia',
            'productos' => 'required|array',
            'productos.*.id_producto' => 'required|exists:producto,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Calcular totales
        $total = 0;
        $productsData = [];
        
        foreach ($request->products as $product) {
            $dbProduct = Producto::findOrFail($product['id']);
            
            if ($dbProduct->stock < $product['cantidad']) {
                return back()->withErrors([
                    'products' => "El producto {$dbProduct->nombre} no tiene suficiente stock"
                ]);
            }
            
            $subtotal = $dbProduct->price * $product['cantidad'];
            $total += $subtotal;
            
            $productsData[] = [
                'id_producto' => $dbProduct->id,
                'cantidad' => $product['cantidad'],
                'precio_unitario' => $dbProduct->price,
                'subtotal' => $subtotal,
            ];
            
            // Reducir stock
            $dbProduct->decrement('stock', $product['cantidad']);
        }
        
        // Calcular impuestos y total general (ejemplo con 16% de IVA)
        $tax = $total * 0.16;
        $grandTotal = $total + $tax;
        
        // Crear la venta
        $sale = Venta::create([
            'numero_factura' => Venta::generateInvoiceNumber(),
            'fecha_venta' => $request->sale_date,
            'total' => $total,
            'iva' => $tax,
            'descuenta' => 0,
            'gran_total' => $grandTotal,
            'metodo_pago' => $request->payment_method,
            'observaciones' => $request->notes,
            'id_cliente' => $request->customer_id,
            'user_id' => auth()->id(),
        ]);
        
        // Agregar detalles de venta
        $sale->details()->createMany($productsData);
        
        return redirect()->route('ventas.show', $sale)
            ->with('success', 'Venta registrada exitosamente');
    }

    public function show(Venta $sale)
    {
        /*
        $sale->load(['cliente', 'user', 'detalle_venta.producto]);
        return view('crear_venta', compact('cliente'));

        */
    }
}