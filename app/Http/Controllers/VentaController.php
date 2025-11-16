<?php

namespace App\Http\Controllers;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Inventario;
use Illuminate\Http\Reques;

class VentaController extends Controller
{
    public function index()
    {
        $venta = Venta::with(['id_cliente', 'user'])->latest()->paginate(10);
        return view('venta');
    }

  public  function generateInvoiceNumber() {

      // Generar número de factura automático
        $ultimaVenta = Venta::latest()->first();
        $numeroFactura = $ultimaVenta ? 'FAC-' . str_pad((intval(substr($ultimaVenta->numero_factura, 4)) + 1), 6, '0', STR_PAD_LEFT) : 'FAC-000001';

  }

    public function create()
    {
        $customers = Cliente::all();
        $products = Producto::where('stock', '>', 0)->get();
        $invoiceNumber = Venta::generateInvoiceNumber();
        
        return view('venta');
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            'id_cliente' => 'required|exists:customers,id',
            'fecha_venta' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,credito,tarjeta_credito,transferencia',
            'productos' => 'required|array',
            'productos.*.id_producto' => 'required|exists:producto,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

 /*

  try {
        DB::beginTransaction();

*/
            

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

           
               // Verificar stock
                $inventario = $producto->inventario;
                if ($inventario->cantidad < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}");
                }
        }
        
        // Calcular impuestos y total general (ejemplo con 16% de IVA)
        $tax = $total * 0.16;
        $grandTotal = $total + $tax;
        
        // Crear la venta
        $venta = Venta::create([
            'numero_factura' => Venta::generateInvoiceNumber(),
            'fecha_venta' => $request->sale_date,
            'total' => $total,
            'iva' => $tax,
            'descuento' => 0,
            'gran_total' => $grandTotal,
            'metodo_pago' => $request->payment_method,
            'observaciones' => $request->notes,
            'id_cliente' => $request->customer_id,
            'user_id' => auth()->id(),
        ]);
        
        // Agregar detalles de venta
        $venta->details()->createMany($productsData);
        
        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta registrada exitosamente');
   

     DB::commit();

        return redirect()->route('venta', $venta)
                ->with('success', 'Venta realizada exitosamente.');
  
    }

    public function show(Venta $venta)
    {
        /*
        $venta->load(['cliente', 'user', 'detalle_venta.producto]);
        return view('venta', compact('cliente'));

        */
    }
}