<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenCompraController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // Vista principal
    // ──────────────────────────────────────────────────────────────
    public function index()
    {
        $numero_orden = 'OC-' . date('Ymd') . '-00001';
        return view('ordenes-compra', compact('numero_orden'));
    }

    // ──────────────────────────────────────────────────────────────
    // DataTable server-side
    // ──────────────────────────────────────────────────────────────
    public function getData(Request $request)
    {
        try {
            $query = OrdenCompra::with(['usuario'])
                ->select('ordenes_compra.*');

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_orden', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_orden', '<=', $request->fecha_hasta);
            }
            if ($request->filled('proveedor')) {
                $query->where('proveedor_nombre', 'like', '%' . $request->proveedor . '%');
            }

            return DataTables::of($query)
                ->addColumn('fecha_orden_fmt', function($r) {
                    return $r->fecha_orden ? date('d/m/Y', strtotime($r->fecha_orden)) : '—';
                })
                ->addColumn('fecha_entrega_fmt', function($r) {
                    return $r->fecha_entrega_esperada ? date('d/m/Y', strtotime($r->fecha_entrega_esperada)) : '—';
                })
                ->addColumn('proveedor_display', function($r) {
                    return $r->proveedor_nombre ?? '—';
                })
                ->addColumn('responsable', function($r) {
                    return $r->usuario ? $r->usuario->name : 'N/A';
                })
                ->addColumn('total_fmt', function($r) {
                    return '$' . number_format($r->total, 0, ',', '.');
                })
                ->addColumn('total_raw', function($r) {
                    return (float) $r->total;
                })
                ->addColumn('estado_badge', function($r) {
                    $badges = [
                        'borrador' => 'secondary',
                        'enviada' => 'info',
                        'confirmada' => 'primary',
                        'recibida_parcial' => 'warning',
                        'recibida' => 'success',
                        'cancelada' => 'danger'
                    ];
                    $estados = [
                        'borrador' => 'Borrador',
                        'enviada' => 'Enviada',
                        'confirmada' => 'Confirmada',
                        'recibida_parcial' => 'Recibida Parcial',
                        'recibida' => 'Recibida',
                        'cancelada' => 'Cancelada'
                    ];
                    $color = $badges[$r->estado] ?? 'secondary';
                    $texto = $estados[$r->estado] ?? $r->estado;
                    return '<span class="badge badge-' . $color . '">' . $texto . '</span>';
                })
                ->addColumn('acciones', function($r) {
                    return '
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-info btn-ver" data-id="' . $r->id_orden . '" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-editar" data-id="' . $r->id_orden . '" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-success btn-pdf" data-id="' . $r->id_orden . '" title="PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <button class="btn btn-danger btn-eliminar"
                                data-id="' . $r->id_orden . '"
                                data-numero="' . $r->numero_orden . '" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['estado_badge', 'acciones'])
                ->make(true);
                
        } catch (\Exception $e) {
            \Log::error('Error en getData: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Siguiente número (AJAX)
    // ──────────────────────────────────────────────────────────────
    public function numeroSiguiente()
    {
        try {
            return response()->json([
                'success' => true,
                'numero' => OrdenCompra::siguienteNumero()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al generar número de orden: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'numero' => 'OC-' . date('Ymd') . '-00001'
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Mostrar una orden (AJAX)
    // ──────────────────────────────────────────────────────────────
    public function show($id)
    {
        $orden = OrdenCompra::with(['detalles.producto', 'usuario'])
            ->findOrFail($id);

        return response()->json($orden);
    }

    // ──────────────────────────────────────────────────────────────
    // Guardar nueva orden
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        try {
            \Log::info('Datos recibidos para guardar orden:', $request->all());
            
            // Validación más flexible
            $validator = \Validator::make($request->all(), [
                'numero_orden' => 'required|unique:ordenes_compra,numero_orden',
                'fecha_orden' => 'required|date',
                'proveedor_nombre' => 'required|string|max:150',
                'productos' => 'required|array|min:1',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Procesar productos
            $productos = $request->input('productos', []);
            $productosProcesados = [];
            
            // Convertir a array si es necesario
            if (is_string($productos)) {
                $productos = json_decode($productos, true);
            }
            
            foreach ($productos as $key => $item) {
                if (is_array($item)) {
                    $productosProcesados[] = [
                        'id_producto' => $item['id_producto'] ?? null,
                        'nombre_producto' => $item['nombre_producto'] ?? 'Producto',
                        'cantidad' => (float) ($item['cantidad'] ?? 1),
                        'precio_unitario' => (float) ($item['precio_unitario'] ?? 0),
                        'descuento' => (float) ($item['descuento'] ?? 0),
                        'codigo_producto' => $item['codigo_producto'] ?? null,
                        'unidad_medida' => $item['unidad_medida'] ?? 'und'
                    ];
                }
            }
            
            // Calcular totales
            $totales = $this->calcularTotalesConProductos($productosProcesados, $request->input('tipo_iva', 0));
            
            // Crear la orden
            $orden = OrdenCompra::create([
                'numero_orden' => $request->numero_orden,
                'fecha_orden' => $request->fecha_orden,
                'fecha_entrega_esperada' => $request->fecha_entrega_esperada ?: null,
                'id_proveedor' => $request->id_proveedor ?: null,
                'proveedor_nombre' => $request->proveedor_nombre,
                'proveedor_nit' => $request->proveedor_nit,
                'proveedor_telefono' => $request->proveedor_telefono,
                'proveedor_email' => $request->proveedor_email,
                'proveedor_direccion' => $request->proveedor_direccion,
                'nombre_contacto' => $request->proveedor_contacto,
                'metodo_pago' => $request->metodo_pago,
                'dias_credito' => $request->dias_credito ?? 0,
                'subtotal' => $totales['subtotal'],
                'descuento_total' => $totales['descuento'],
                'impuesto_porcentaje' => (float) ($request->tipo_iva ?? 0),
                'impuesto_valor' => $totales['iva'],
                'total' => $totales['total'],
                'estado' => 'borrador',
                'observaciones' => $request->observaciones,
                'terminos_condiciones' => $request->terminos_condiciones,
                'userId' => auth()->id(),
            ]);
            
            \Log::info('Orden creada con ID: ' . $orden->id_orden);
            
            // Guardar detalles
            foreach ($productosProcesados as $item) {
                $totalLinea = ($item['cantidad'] * $item['precio_unitario']) - $item['descuento'];
                
                OrdenCompraDetalle::create([
                    'id_orden' => $orden->id_orden,
                    'id_producto' => $item['id_producto'],
                    'codigo_producto' => $item['codigo_producto'],
                    'nombre_producto' => $item['nombre_producto'],
                    'unidad_medida' => $item['unidad_medida'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento' => $item['descuento'],
                    'total_linea' => $totalLinea,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Orden de compra creada correctamente.',
                'numero_siguiente' => OrdenCompra::siguienteNumero()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al guardar orden: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Actualizar orden
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $orden = OrdenCompra::findOrFail($id);

        $request->validate([
            'proveedor_nombre'          => 'required|string|max:150',
            'productos'                 => 'required|array|min:1',
            'productos.*.id_producto'   => 'required',
            'productos.*.cantidad'      => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            ['sub' => $sub, 'desc' => $desc, 'iva' => $iva, 'total' => $total]
                = $this->calcularTotales($request);

            $orden->update([
                'fecha_entrega_esperada' => $request->fecha_entrega_esperada ?: null,
                'id_proveedor'           => $request->id_proveedor ?: null,
                'proveedor_nombre'       => $request->proveedor_nombre,
                'nombre_contacto'       => $request->proveedor_contacto,
                'proveedor_nit'          => $request->proveedor_nit,
                'proveedor_telefono'     => $request->proveedor_telefono,
                'proveedor_email'        => $request->proveedor_email,
                'proveedor_direccion'    => $request->proveedor_direccion,
                'metodo_pago'            => $request->metodo_pago,
                'dias_credito'           => $request->dias_credito ?? 0,
                'subtotal'               => $sub,
                'descuento_total'        => $desc,
                'impuesto_porcentaje'    => $request->tipo_iva ?? 0,
                'impuesto_valor'         => $iva,
                'total'                  => $total,
                'observaciones'          => $request->observaciones,
                'terminos_condiciones'   => $request->terminos_condiciones,
            ]);

            $orden->detalles()->delete();
            $this->guardarDetalle($orden, $request->productos);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Orden actualizada correctamente.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Cambiar estado
    // ──────────────────────────────────────────────────────────────
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate(['estado' => 'required|in:borrador,enviada,confirmada,recibida_parcial,recibida,cancelada']);

        $orden = OrdenCompra::findOrFail($id);
        $extra = [];

        if ($request->estado === 'recibida') {
            $extra['fecha_entrega_real'] = now()->toDateString();
        }

        $orden->update(array_merge(['estado' => $request->estado], $extra));

        return response()->json(['success' => true, 'message' => 'Estado actualizado.']);
    }

    // ──────────────────────────────────────────────────────────────
    // Eliminar (soft delete)
    // ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $orden = OrdenCompra::findOrFail($id);

        if (in_array($orden->estado, ['confirmada', 'recibida'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una orden confirmada o recibida.',
            ], 422);
        }

        $orden->delete();

        return response()->json(['success' => true, 'message' => 'Orden eliminada correctamente.']);
    }

    // ──────────────────────────────────────────────────────────────
    // CREAR PDF
    // ──────────────────────────────────────────────────────────────
// En OrdenCompraController.php

// Método para generar PDF (descarga directa)
public function pdf($id)
{
    try {
        $orden = OrdenCompra::with(['detalles', 'usuario'])->findOrFail($id);
        
        // Generar el HTML de la orden
        $html = $this->generarHTMLOrdenCompra($orden);
        
        // Generar PDF con DOMPDF
        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        // Forzar descarga
        return $pdf->download('orden-compra-' . $orden->numero_orden . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('Error al generar PDF: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


// Método privado para generar HTML de la orden (optimizado para impresión)
private function generarHTMLOrdenCompra($orden)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Orden de Compra ' . $orden->numero_orden . '</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "DejaVu Sans", Arial, sans-serif;
                font-size: 12px;
                background: white;
                padding: 20px;
            }
            
            .orden-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            
            .header h1 {
                margin: 0;
                color: #333;
                font-size: 24px;
            }
            
            .empresa-info {
                text-align: center;
                margin-bottom: 20px;
                font-size: 11px;
            }
            
            .orden-info, .proveedor-info {
                margin-bottom: 20px;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 4px;
            }
            
            .orden-info {
                background-color: #f9f9f9;
            }
            
            .info-table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .info-table td {
                padding: 5px;
                border: none;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                vertical-align: top;
            }
            
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .totales {
                width: 350px;
                float: right;
                margin-top: 20px;
            }
            
            .totales table {
                width: 100%;
                border: none;
            }
            
            .totales td {
                border: none;
                padding: 5px;
            }
            
            .footer {
                margin-top: 50px;
                text-align: center;
                font-size: 10px;
                border-top: 1px solid #ddd;
                padding-top: 10px;
                clear: both;
            }
            
            .clearfix {
                clear: both;
            }
            
            .estado {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                font-weight: bold;
            }
            
            .estado-borrador { background-color: #6c757d; color: white; }
            .estado-enviada { background-color: #17a2b8; color: white; }
            .estado-confirmada { background-color: #007bff; color: white; }
            .estado-recibida_parcial { background-color: #ffc107; color: #333; }
            .estado-recibida { background-color: #28a745; color: white; }
            .estado-cancelada { background-color: #dc3545; color: white; }
            
            @media print {
                body {
                    padding: 0;
                    margin: 0;
                }
                .orden-container {
                    margin: 0;
                    padding: 10px;
                }
                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="orden-container">
            <div class="header">
                <h1>ORDEN DE COMPRA</h1>
                <p><strong>N° ' . htmlspecialchars($orden->numero_orden) . '</strong></p>
            </div>
            
            <div class="empresa-info">
                <strong>EMPRESA S.A.S.</strong><br>
                NIT: 900.000.000-0<br>
                Tel: (1) 1234567 - Email: info@empresa.com<br>
                Dirección: Calle 123 # 45-67, Bogotá
            </div>
            
            <div class="orden-info">
                <table class="info-table">
                    <tr>
                        <td style="width: 50%;"><strong>Fecha Orden:</strong> ' . date('d/m/Y', strtotime($orden->fecha_orden)) . '</td>
                        <td><strong>Estado:</strong> <span class="estado estado-' . $orden->estado . '">' . $this->getEstadoTexto($orden->estado) . '</span></td>
                    </tr>' . 
                    ($orden->fecha_entrega_esperada ? '
                    <tr>
                        <td><strong>Fecha Entrega Esperada:</strong> ' . date('d/m/Y', strtotime($orden->fecha_entrega_esperada)) . '</td>
                        <td><strong>Método Pago:</strong> ' . ($orden->metodo_pago ?? '—') . '</td>
                    </tr>' : '') . 
                    ($orden->dias_credito ? '
                    <tr>
                        <td><strong>Días de Crédito:</strong> ' . $orden->dias_credito . '</td>
                        <td></td>
                    </tr>' : '') . '
                </table>
            </div>
            
            <div class="proveedor-info">
                <h6 style="margin-top: 0; margin-bottom: 10px;">PROVEEDOR</h6>
                <strong>' . htmlspecialchars($orden->proveedor_nombre) . '</strong><br>
                ' . ($orden->proveedor_nit ? 'NIT: ' . htmlspecialchars($orden->proveedor_nit) . '<br>' : '') . '
                ' . ($orden->nombre_contacto ? 'Contacto: ' . htmlspecialchars($orden->nombre_contacto) . '<br>' : '') . '
                ' . ($orden->proveedor_telefono ? 'Teléfono: ' . htmlspecialchars($orden->proveedor_telefono) . '<br>' : '') . '
                ' . ($orden->proveedor_email ? 'Email: ' . htmlspecialchars($orden->proveedor_email) . '<br>' : '') . '
                ' . ($orden->proveedor_direccion ? 'Dirección: ' . htmlspecialchars($orden->proveedor_direccion) : '') . '
            </div>
            
            <h6>DETALLE DE PRODUCTOS</h6>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Unidad</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Precio Unit.</th>
                        <th class="text-right">Descuento</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($orden->detalles as $detalle) {
        $html .= '
                    <tr>
                        <td>' . htmlspecialchars($detalle->codigo_producto ?? '—') . '</td>
                        <td>' . htmlspecialchars($detalle->nombre_producto) . '</td>
                        <td class="text-center">' . htmlspecialchars($detalle->unidad_medida) . '</td>
                        <td class="text-right">' . number_format($detalle->cantidad, 2) . '</td>
                        <td class="text-right">$' . number_format($detalle->precio_unitario, 0, ',', '.') . '</td>
                        <td class="text-right">$' . number_format($detalle->descuento, 0, ',', '.') . '</td>
                        <td class="text-right"><strong>$' . number_format($detalle->total_linea, 0, ',', '.') . '</strong></td>
                    </tr>';
    }

                    // Si no hay productos, mostrar mensaje
                    if ($orden->detalles->isEmpty()) {
                        $html .= '<tr><td colspan="7" style="text-align:center;">Sin productos</td></tr>';
                    }
    
    $html .= '
                </tbody>
            </table>
            
            <div class="totales">
                <table>
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-right">$' . number_format($orden->subtotal, 0, ',', '.') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Descuento:</strong></td>
                        <td class="text-right">$' . number_format($orden->descuento_total, 0, ',', '.') . '</td>
                    </tr>';
    
    if ($orden->impuesto_porcentaje > 0) {
        $html .= '
                    <tr>
                        <td><strong>IVA (' . $orden->impuesto_porcentaje . '%):</strong></td>
                        <td class="text-right">$' . number_format($orden->impuesto_valor, 0, ',', '.') . '</td>
                    </tr>';
    }
    
    $html .= '
                    <tr style="border-top: 2px solid #333;">
                        <td><strong>TOTAL:</strong></td>
                        <td class="text-right"><strong>$' . number_format($orden->total, 0, ',', '.') . '</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="clearfix"></div>';
    
    if ($orden->observaciones) {
        $html .= '
            <div style="margin-top: 20px;">
                <h6>Observaciones</h6>
                <p>' . nl2br(htmlspecialchars($orden->observaciones)) . '</p>
            </div>';
    }
    
    if ($orden->terminos_condiciones) {
        $html .= '
            <div style="margin-top: 20px;">
                <h6>Términos y Condiciones</h6>
                <p>' . nl2br(htmlspecialchars($orden->terminos_condiciones)) . '</p>
            </div>';
    }
    
    $html .= '
            <div class="footer">
                Documento generado el ' . date('d/m/Y H:i:s') . ' por ' . ($orden->usuario->name ?? 'Sistema') . '<br>
                Este documento es una orden de compra válida.
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}
    // ──────────────────────────────────────────────────────────────
    // Búsqueda de productos (Select2 AJAX)
    // ──────────────────────────────────────────────────────────────
   public function buscarProductos(Request $request)
{
    $q = $request->get('q', '');
    
    // Obtener las columnas de la tabla productos
    $columns = \Schema::getColumnListing('productos');
    
    // Construir la consulta dinámicamente
    $query = Producto::query();
    
    // Buscar por nombre (siempre disponible)
    $query->where('nombre', 'like', "%{$q}%");
    
    // Buscar por código si existe
    if (in_array('codigo', $columns)) {
        $query->orWhere('codigo', 'like', "%{$q}%");
    }
    
    // Buscar por referencia si existe
    if (in_array('referencia', $columns)) {
        $query->orWhere('referencia', 'like', "%{$q}%");
    }
    
    // Buscar por sku si existe
    if (in_array('sku', $columns)) {
        $query->orWhere('sku', 'like', "%{$q}%");
    }
    
    $productos = $query->limit(10)->get()->map(function($p) use ($columns) {
        return [
            'id'            => $p->id_producto,
            'text'          => ($p->codigo ?? '') ? '[' . $p->codigo . '] ' : '' . $p->nombre,
            'codigo'        => $p->codigo ?? '',
            'nombre'        => $p->nombre,
            'precio'        => floatval($p->precio_compra ?? $p->precio_venta ?? 0),
            'stock'         => intval($p->stock_actual ?? 0),
            'unidad_medida' => $p->unidad_medida ?? 'und'
        ];
    });
    
    return response()->json(['results' => $productos]);
}
    
    // ──────────────────────────────────────────────────────────────
    // Búsqueda de proveedores (Select2 AJAX)
    // ──────────────────────────────────────────────────────────────
    public function buscarProveedores(Request $request)
    {
        $q = $request->get('q', '');
        
        $proveedores = Proveedor::where('razon_social', 'like', "%{$q}%")
            ->orWhere('nit', 'like', "%{$q}%")
            ->orWhere('nombre_contacto', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(function($p) {
                $textoMostrar = $p->razon_social;
                if ($p->nombre_contacto) {
                    $textoMostrar .= ' (Contacto: ' . $p->nombre_contacto . ')';
                }
                $textoMostrar .= ' — ' . ($p->nit ?? 'S/N');
                
                return [
                    'id'       => $p->id_proveedor,
                    'text'     => $textoMostrar,
                    'nombre'   => $p->razon_social,
                    'razon_social' => $p->razon_social,
                    'nombre_contacto' => $p->nombre_contacto ?? '',
                    'nit'      => $p->nit ?? '',
                    'telefono' => $p->telefono ?? '',
                    'email'    => $p->email ?? '',
                    'direccion'=> $p->direccion ?? '',
                ];
            });
        
        return response()->json(['results' => $proveedores]);
    }

    // ──────────────────────────────────────────────────────────────
    // Métodos privados auxiliares
    // ──────────────────────────────────────────────────────────────
    
    private function calcularTotalesConProductos(array $productos, float $porcentajeIva): array
    {
        $subtotal = 0;
        $descuento = 0;
        
        foreach ($productos as $p) {
            $subtotal += $p['cantidad'] * $p['precio_unitario'];
            $descuento += $p['descuento'];
        }
        
        $base = $subtotal - $descuento;
        $iva = $porcentajeIva > 0 ? $base * ($porcentajeIva / 100) : 0;
        $total = $base + $iva;
        
        return [
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'iva' => $iva,
            'total' => $total
        ];
    }
    
    private function calcularTotales(Request $request): array
    {
        $sub = 0;
        $desc = 0;
        
        $productos = $request->input('productos', []);
        
        if (is_string($productos)) {
            $productos = json_decode($productos, true) ?: [];
        }
        
        if (empty($productos) || !is_array($productos)) {
            return ['sub' => 0, 'desc' => 0, 'iva' => 0, 'total' => 0];
        }
        
        foreach ($productos as $p) {
            $cantidad = (float) ($p['cantidad'] ?? 0);
            $precio = (float) ($p['precio_unitario'] ?? 0);
            $descuento = (float) ($p['descuento'] ?? 0);
            
            $sub += $cantidad * $precio;
            $desc += $descuento;
        }
        
        $porcentajeIva = (float) ($request->input('tipo_iva', 0));
        $base = $sub - $desc;
        $iva = $porcentajeIva > 0 ? $base * ($porcentajeIva / 100) : 0;
        $total = $base + $iva;
        
        return ['sub' => $sub, 'desc' => $desc, 'iva' => $iva, 'total' => $total];
    }
    
    private function guardarDetalle(OrdenCompra $orden, array $productos): void
    {
        foreach ($productos as $p) {
            if (is_string($p)) {
                $p = json_decode($p, true);
            }
            
            $idProducto = $p['id_producto'] ?? null;
            $nombreProducto = $p['nombre_producto'] ?? 'Producto';
            $cantidad = (float) ($p['cantidad'] ?? 0);
            $precio = (float) ($p['precio_unitario'] ?? 0);
            $descuento = (float) ($p['descuento'] ?? 0);
            $codigo = $p['codigo_producto'] ?? null;
            $unidad = $p['unidad_medida'] ?? 'und';
            
            $totalLinea = ($cantidad * $precio) - $descuento;
            
            if ($idProducto && !$codigo) {
                $producto = Producto::find($idProducto);
                if ($producto) {
                    $codigo = $producto->codigo;
                    $nombreProducto = $producto->nombre;
                }
            }
            
            OrdenCompraDetalle::create([
                'id_orden' => $orden->id_orden,
                'id_producto' => $idProducto,
                'codigo_producto' => $codigo,
                'nombre_producto' => $nombreProducto,
                'unidad_medida' => $unidad,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'descuento' => $descuento,
                'total_linea' => $totalLinea,
            ]);
        }
    }
    
   // En OrdenCompraController.php

// Método para obtener la vista HTML de la orden
public function vistaPrevia($id)
{
    try {
        $orden = OrdenCompra::with(['detalles', 'usuario'])->findOrFail($id);
        
        // Generar el HTML de la orden
        $html = $this->generarHTMLOrdenCompra($orden);
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'orden' => $orden
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al generar vista previa: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}



private function getEstadoTexto($estado)
{
    $estados = [
        'borrador' => 'Borrador',
        'enviada' => 'Enviada',
        'confirmada' => 'Confirmada',
        'recibida_parcial' => 'Recibida Parcial',
        'recibida' => 'Recibida',
        'cancelada' => 'Cancelada'
    ];
    
    return $estados[$estado] ?? $estado;
}
}