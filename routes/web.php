<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\compraController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CajaMenorController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RemisionController;
use App\http\Controllers\ventaController;
use App\Http\Controllers\proveedorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistorialVentasController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/inicio', [App\Http\Controllers\HomeController::class, 'index'])->name('inicio');



// ======================================================

//  RUTAS PARA CLIENTES

// ======================================================

// Rutas para clientes
Route::get('clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
Route::get('clientes-data', [App\Http\Controllers\ClienteController::class, 'getData'])->name('clientes.data');
Route::get('busqueda-clientes', [App\Http\Controllers\ClienteController::class, 'busquedaClientes'])->name('busqueda-clientes');
Route::get('verificar-cliente', [App\Http\Controllers\ClienteController::class, 'verificarCliente'])->name('verificar-cliente');
Route::get('clientes-data', [App\Http\Controllers\ClienteController::class, 'getData'])->name('clientes.data');
Route::post('guardar_clientes', [App\Http\Controllers\ClienteController::class, 'store'])->name('clientes.store');
Route::get('clientes/{id}', [App\Http\Controllers\ClienteController::class, 'show'])->name('clientes.show');
Route::get('clientes/{id}/edit', [App\Http\Controllers\ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('clientes/{id}', [App\Http\Controllers\ClienteController::class, 'update'])->name('clientes.update');
Route::delete('clientes/{id}', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('clientes.destroy');


// ======================================================

//  RUTAS PARA DASHBOARD

// ======================================================

 
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');     
Route::get('/dashboard/detalle-venta/{id}', [DashboardController::class, 'getDetalleVenta'])->name('dashboard.detalle-venta');
Route::get('/dashboard/check-updates', [DashboardController::class, 'checkUpdates'])->name('dashboard.check-updates');


// ======================================================

//  RUTAS PARA PUNTO DE VENTAS

// ======================================================


Route::get('venta', [PuntoVentaController::class, 'index'])->name('venta');
Route::post('/buscar-productos', [PuntoVentaController::class, 'buscarProductos'])->name('buscar-productos');
Route::post('/buscar-clientes', [PuntoVentaController::class, 'buscarClientes'])->name('buscar-clientes');
Route::post('/procesar-venta', [PuntoVentaController::class, 'procesarVenta'])->name('procesar-venta');
Route::get('/productos-frecuentes', [PuntoVentaController::class, 'productosFrecuentes'])->name('productos.frecuentes');
Route::get('/ticket/{venta}', [PuntoVentaController::class, 'generarTicket'])->name('ticket');
Route::get('/factura/{venta}', [PuntoVentaController::class, 'generarFactura'])->name('factura');


// ======================================================

//  RUTAS PARA HISTORIAL VENTAS

// ======================================================


 // Ruta para la vista principal
    Route::get('/historial-ventas', [HistorialVentasController::class, 'index'])->name('historial_ventas');    
    // Ruta para obtener datos de la tabla (DataTables)
    Route::get('/historial-ventas/data', [HistorialVentasController::class, 'getVentasData'])->name('historial.ventas.data');    
    // Ruta para ver detalle de una venta específica
    Route::get('/ventas/detalle/{id}', [HistorialVentasController::class, 'getDetalleVenta'])->name('ventas.detalle');    
    
    // Ruta para imprimir ticket
    Route::get('/ventas/ticket/{id}', [HistorialVentasController::class, 'imprimirTicket'])->name('ventas.ticket');    

    // Ruta para cancelar venta (solo pendientes)
    Route::post('/ventas/cancelar/{id}', [HistorialVentasController::class, 'cancelarVenta'])->name('ventas.cancelar');    

    // Eliminar venta y restablecer stock
    Route::delete('/historial-ventas/eliminar/{id}', [HistorialVentasController::class, 'eliminarVenta'])->name('ventas.eliminar');
        
    // Ruta para exportar reporte
    Route::get('/ventas/reporte', [HistorialVentasController::class, 'exportarReporte'])->name('ventas.reporte');    
    // Ruta para vista de todas las ventas (opcional - como alternativa)
    Route::get('/ventas/todas', [HistorialVentasController::class, 'ventasTodas'])->name('ventas.todas');
Route::get('/historial-ventas/exportar-excel', [App\Http\Controllers\HistorialVentasController::class, 'exportarExcel'])->name('historial.ventas.exportar.excel');
  

/*

Route::get('venta', [App\Http\Controllers\VentaController::class, 'index'])->name('venta');
Route::post('crear_venta', [App\Http\Controllers\VentaController::class, 'create'])->name('crear_venta');
 Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create');
 Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

*/


// ======================================================

//  RUTAS PARA CATEGORIAS

// ======================================================


Route::get('categorias', [App\Http\Controllers\CategoriaController::class, 'index'])->name('categorias');
Route::post('/categorias', [App\Http\Controllers\CategoriaController::class, 'store'])->name('crear_categorias');
Route::get('mostrar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'show'])->name('mostrar_categoria');
Route::get('editar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'edit'])->name('editar_categoria');
Route::post('actualizar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('categoria.update');
Route::delete('eliminar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('categoria.destroy');


// ======================================================

//  RUTAS PARA PRODUCTOS

// ======================================================

Route::post('/compras/guardar', [App\Http\Controllers\compraController::class, 'registrarCompra'])->name('compras.guardar');
Route::get('/compras/listar',  [App\Http\Controllers\compraController::class, 'listarCompras'])->name('compras.listar');
Route::get('/compras/estadisticas', [App\Http\Controllers\compraController::class, 'estadisticasCompras'])->name('compras.estadisticas');
Route::get('compras', [App\Http\Controllers\compraController::class, 'index'])->name('compras');

Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::post('/productos-guardar', [ProductoController::class, 'store'])->name('producto.guardar');

Route::get('/compras/buscar-productos', [ProductoController::class, 'buscarProductos'])->name('buscar.producto');
Route::get('mostrar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'show'])->name('productos.show');
Route::get('editar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'edit'])->name('productos.edit');
Route::post('/compras/guardar',     [ProductoController::class, 'registrarCompra'])->name('compras.guardar');
Route::get('/compras/listar', [CompraController::class, 'listarCompras'])->name('compras.listar');
Route::get('/compras/estadisticas', [CompraController::class, 'estadisticasCompras'])->name('compras.estadisticas');


Route::post('/actualizar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'update'])->name('productos.update');
Route::delete('eliminar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('productos.destroy');

Route::get('/obtener-categorias', [App\Http\Controllers\ProductoController::class, 'obtenerCategorias'])->name('categorias.obtener');
Route::get('/por-categoria', [App\Http\Controllers\ProductoController::class, 'porCategoria'])->name('productos.por-categoria');
//Route::get('/buscar-producto', [App\Http\Controllers\ProductoController::class, 'buscarProductos'])->name('buscar-producto');
Route::get('/filtrar-productos', [App\Http\Controllers\ProductoController::class, 'porCategoria'])->name('filtrar-productos');
Route::get('/productos-todos', [App\Http\Controllers\ProductoController::class, 'todosLosProductos'])->name('productos-todos');
Route::get('/productos/frecuentes', [App\Http\Controllers\productoController::class, 'productosFrecuentes'])->name('productos/frecuentes');

// =================================

// RUTAS PARA COTIZACIONES

// ==================================

// 1. Rutas estáticas PRIMERO (sin parámetros)
Route::get('/cotizaciones',                      [CotizacionController::class, 'index'])          ->name('cotizaciones.index');
Route::get('/cotizaciones/data',                 [CotizacionController::class, 'getData'])         ->name('cotizaciones.data');
Route::get('/cotizaciones/numero-siguiente',     [CotizacionController::class, 'numeroSiguiente'])->name('cotizaciones.numero-siguiente');
Route::post('/cotizaciones',                     [CotizacionController::class, 'store'])           ->name('cotizaciones-guardar');
Route::get('/buscar-clientes-cotizacion',       [CotizacionController::class, 'buscarClientes'])  ->name('buscar-clientes-cotizacion');
Route::get('/buscar-productos-cotizacion',      [CotizacionController::class, 'buscarProductos'])->name('buscar-productos-cotizacion');

// 2. Rutas con {id} AL FINAL (van después de todas las estáticas)
Route::get('/cotizaciones/{id}/pdf',             [CotizacionController::class, 'generarPDF'])     ->name('cotizaciones.pdf');
Route::post('/cotizaciones/{id}/cambiar-estado', [CotizacionController::class, 'cambiarEstado'])  ->name('cotizaciones.cambiar-estado');
Route::get('/cotizaciones/{id}',                 [CotizacionController::class, 'show'])           ->name('cotizaciones.show');
Route::put('/cotizaciones/{id}',                 [CotizacionController::class, 'update'])         ->name('cotizaciones.update');
Route::delete('/cotizaciones/{id}',              [CotizacionController::class, 'destroy'])        ->name('cotizaciones.destroy');


// Vista principal + DataTable
Route::get('/remisiones',               [RemisionController::class, 'index'])->name('remisiones.index');
Route::get('/remisiones/data',          [RemisionController::class, 'data'])->name('remisiones.data');

// Número siguiente (llamado al abrir modal)
Route::get('/remisiones/numero-siguiente', [RemisionController::class, 'numeroSiguiente'])->name('remisiones.numero-siguiente');

// Búsqueda de productos para Select2
Route::get('/buscar-productos-remision', [RemisionController::class, 'buscarProductos'])->name('buscar-productos-remision');

// CRUD
Route::post('/remisiones',              [RemisionController::class, 'store'])->name('remisiones.store');
Route::get('/remisiones/{id}',          [RemisionController::class, 'show'])->name('remisiones.show');
Route::put('/remisiones/{id}',          [RemisionController::class, 'update'])->name('remisiones.update');
Route::delete('/remisiones/{id}',       [RemisionController::class, 'destroy'])->name('remisiones.destroy');

// Cambiar estado
Route::post('/remisiones/{id}/cambiar-estado', [RemisionController::class, 'cambiarEstado'])->name('remisiones.cambiar-estado');



// ======================================================

//  RUTAS PARA INVETARIO

// ======================================================

 
Route::get('/inventarios', [App\Http\Controllers\InventarioController::class, 'index']);
Route::get('/inventarios/kardex/{id_producto}',[App\Http\Controllers\InventarioController::class, 'kardex']);
Route::get('/inventarios/reporte',[App\Http\Controllers\InventarioController::class, 'reporte']);
Route::get('/inventarios/listar',[App\Http\Controllers\InventarioController::class, 'listar']);
Route::get('/inventarios/{id}',[App\Http\Controllers\InventarioController::class, 'show']);
Route::get('/inventarios/estadisticas',[App\Http\Controllers\InventarioController::class, 'estadisticas']);
  


// ======================================================

//  RUTAS PARA CAJA

// ======================================================


    Route::get('caja', [App\Http\Controllers\CajaMenorController::class, 'index'])->name('index');
    Route::post('abrir_caja', [App\Http\Controllers\CajaMenorController::class, 'abrirCaja'])->name('abrir_caja');
    Route::post('cerrar_caja', [App\Http\Controllers\CajaMenorController::class, 'cerrarCaja'])->name('cerrar_caja');
    Route::post('movimiento_caja', [App\Http\Controllers\CajaMenorController::class, 'registrarMovimiento'])->name('movimiento_caja');
    Route::get('Obtener_movimientos/{id}', [App\Http\Controllers\CajaMenorController::class, 'obtenerMovimientos'])->name('obtener_movimientos');
    Route::post('reporte_caja', [App\Http\Controllers\CajaMenorController::class, 'generarReporte'])->name('reporte_caja');
    Route::post('/movimientos-caja/datatable', [CajaMenorController::class, 'datatable'])->name('movimientos.datatable');
    Route::get('/movimientos-caja/export/excel', [CajaMenorController::class, 'exportarExcel'])->name('movimientos.export.excel');
    Route::get('/movimientos-caja/export/pdf', [CajaMenorController::class, 'exportarPdf'])->name('movimientos.export.pdf');


    // ======================================================

//  RUTAS PARA PROVEEDORES

// ======================================================


   Route::resource('proveedores', ProveedorController::class);
   Route::get('proveedores-data', [ProveedorController::class, 'getData'])->name('proveedores.data');



// Rutas para búsquedas AJAX

/*
Route::post('/buscar_cliente', [ClienteController::class, 'buscar']);
Route::post('/buscar-productos', [ProductoController::class, 'buscar']);
Route::get('/filtrar-productos', [ProductoController::class, 'filtrarProductos']);
Route::post('/productos-todos', [ProductoController::class, 'todosLosProductos']);
Route::post('/obtener-categorias', [ProductoController::class, 'obtenerCategorias']);

*/
    // Verificar stock
  //  Route::post('verificar-stock', [puntoVentaController::class, 'verificarStock'])->name('pos-verificar-stock');
    
   