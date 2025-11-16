<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class PuntoVentaController extends Controller
{
    public function index()
    {
        return view('venta');
    }

    public function buscarProductos(Request $request)
    {
        $consulta = $request->input('consulta');
        
        $productos = Producto::where('codigo', 'LIKE', "%{$consulta}%")
            ->orWhere('nombre', 'LIKE', "%{$consulta}%")
            ->where('stock', '>', 0)
            ->select('id', 'codigo', 'nombre', 'precio', 'stock')
            ->limit(20)
            ->get();

        return response()->json(['productos' => $productos]);
    }

    public function buscarClientes(Request $request)
    {
        $consulta = $request->input('consulta');
        
        $clientes = Cliente::where('nombre', 'LIKE', "%{$consulta}%")
            ->orWhere('rfc', 'LIKE', "%{$consulta}%")
            ->orWhere('email', 'LIKE', "%{$consulta}%")
            ->orWhere('telefono', 'LIKE', "%{$consulta}%")
            ->select('id', 'nombre', 'rfc', 'email', 'telefono', 'direccion', 'tipo_cliente')
            ->limit(20)
            ->get();

        return response()->json(['clientes' => $clientes]);
    }

    public function procesarVenta(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validar stock antes de procesar
            foreach ($request->items as $item) {
                $producto = Producto::find($item['id']);
                if (!$producto || $producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$item['nombre']}");
                }
            }

            // Crear la venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'subtotal' => $request->subtotal,
                'iva' => $request->iva,
                'total' => $request->total,
                'aplicar_iva' => $request->aplicar_iva,
                'fecha_venta' => now(),
                'estado' => 'completada'
            ]);

            // Crear detalles de venta y actualizar stock
            foreach ($request->items as $item) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'total' => $item['precio'] * $item['cantidad']
                ]);

                // Actualizar stock
                $producto = Producto::find($item['id']);
                $producto->decrement('stock', $item['cantidad']);
            }

            // Registrar pago
            Pago::create([
                'venta_id' => $venta->id,
                'metodo_pago' => $request->metodo_pago,
                'monto' => $request->total,
                'datos_pago' => json_encode($request->datos_pago),
                'fecha_pago' => now()
            ]);

            DB::commit();

            return response()->json([
                'exito' => true,
                'venta_id' => $venta->id,
                'mensaje' => 'Venta procesada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'exito' => false,
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function generarTicket($ventaId)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'pago'])->find($ventaId);
        
        return view('punto-venta.ticket', compact('venta'));
    }

    public function generarFactura($ventaId)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'pago'])->find($ventaId);
        
        return view('punto-venta.factura', compact('venta'));
    }
}