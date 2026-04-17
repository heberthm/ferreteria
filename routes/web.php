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
use App\Http\Controllers\DevolucionController;
use App\http\Controllers\ventaController;
use App\Http\Controllers\OrdenCompraController;
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
Route::post('/categorias', [App\Http\Controllers\CategoriaController::class, 'store'])->name('categorias.store');
Route::get('mostrar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'show'])->name('mostrar_categoria');
Route::get('editar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'edit'])->name('editar_categoria');
Route::post('actualizar_categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('categoria.update');
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');



// ======================================================

//  RUTAS PARA PRODUCTOS

// ======================================================

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


Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
Route::get('/compras/listar', [CompraController::class, 'listarCompras'])->name('compras.listar');
Route::post('/compras/guardar', [CompraController::class, 'guardar'])->name('compras.guardar');
Route::get('/compras/estadisticas', [CompraController::class, 'estadisticasCompras'])->name('compras.estadisticas');
Route::get('/compras/buscar-productos', [CompraController::class, 'buscarProductos'])->name('compras.buscar-productos');

// Nuevas rutas para acciones en compras
Route::get('/compras/mostrar/{id}', [CompraController::class, 'mostrarCompra'])->name('compras.mostrar');
Route::post('/compras/actualizar/{id}', [CompraController::class, 'actualizarCompra'])->name('compras.actualizar');
Route::delete('/compras/anular/{id}', [CompraController::class, 'anularCompra'])->name('compras.anular');


Route::get('/mostrar_producto/{id}', [ProductoController::class, 'show'])->name('productos.show');
Route::get('/editar_producto/{id}', [ProductoController::class, 'edit'])->name('productos.edit');
Route::post('/actualizar_producto/{id}', [ProductoController::class, 'update'])->name('productos.update');


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

//  RUTAS PARA DEVOLUCIONES

// ======================================================

Route::get('devoluciones', [DevolucionController::class, 'index'])->name('devoluciones.index');
Route::post('devoluciones', [DevolucionController::class, 'store'])->name('devoluciones.store');

Route::get('devoluciones/data', [DevolucionController::class, 'getData'])->name('devoluciones.data');
Route::get('devoluciones/next-number', [DevolucionController::class, 'numeroSiguiente'])->name('devoluciones.next-number');
Route::get('devoluciones/siguiente-numero', [DevolucionController::class, 'numeroSiguiente'])->name('devoluciones.siguiente-numero');
Route::get('clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
Route::get('devoluciones/buscar-ventas', [DevolucionController::class, 'buscarVentasCliente'])->name('devoluciones.buscar-ventas');



// Luego las rutas resource o con parámetros
Route::get('devoluciones/{id}', [DevolucionController::class, 'show'])->name('devoluciones.show');
Route::put('devoluciones/{id}', [DevolucionController::class, 'update'])->name('devoluciones.update');
Route::delete('devoluciones/{id}', [DevolucionController::class, 'destroy'])->name('devoluciones.destroy');
Route::get('devoluciones/detalles-venta/{id}', [DevolucionController::class, 'getDetallesVenta'])->name('devoluciones.detalles-venta');
Route::post('devoluciones/aprobar/{id}', [DevolucionController::class, 'aprobar'])->name('devoluciones.aprobar');
Route::post('devoluciones/rechazar/{id}', [DevolucionController::class, 'rechazar'])->name('devoluciones.rechazar');
Route::post('devoluciones/completar/{id}', [DevolucionController::class, 'completar'])->name('devoluciones.completar');
Route::post('devoluciones/cancelar/{id}', [DevolucionController::class, 'cancelar'])->name('devoluciones.cancelar');
Route::get('devoluciones/pdf/{id}', [DevolucionController::class, 'pdf'])->name('devoluciones.pdf');

// Rutas adicionales
Route::get('devoluciones/data', [DevolucionController::class, 'getData'])->name('devoluciones.data');
Route::get('devoluciones/buscar-ventas', [DevolucionController::class, 'buscarVentasCliente'])->name('devoluciones.buscar-ventas');
Route::get('devoluciones/detalles-venta/{id}', [DevolucionController::class, 'getDetallesVenta'])->name('devoluciones.detalles-venta');
Route::post('devoluciones/aprobar/{id}', [DevolucionController::class, 'aprobar'])->name('devoluciones.aprobar');
Route::post('devoluciones/rechazar/{id}', [DevolucionController::class, 'rechazar'])->name('devoluciones.rechazar');
Route::post('devoluciones/completar/{id}', [DevolucionController::class, 'completar'])->name('devoluciones.completar');
Route::post('devoluciones/cancelar/{id}', [DevolucionController::class, 'cancelar'])->name('devoluciones.cancelar');
Route::get('devoluciones/pdf/{id}', [DevolucionController::class, 'pdf'])->name('devoluciones.pdf');


// ======================================================

//  RUTAS PARA INVETARIO

// ======================================================

// Rutas para el módulo de inventario
Route::get('/inventarios', [InventarioController::class, 'index'])->name('inventarios');
Route::get('/inventario/data', [InventarioController::class, 'getData'])->name('inventario.data');
Route::get('/inventario/resumen', [InventarioController::class, 'getResumen'])->name('inventario.resumen');
Route::get('/inventario/detalle/{id}', [InventarioController::class, 'getDetalle'])->name('inventario.detalle');
Route::get('/inventario/exportar', [InventarioController::class, 'exportar'])->name('inventario.exportar');
Route::post('/inventario/registrar', [InventarioController::class, 'registrarMovimiento'])->name('inventario.registrar');
Route::get('/inventario/imprimir/{id}', [InventarioController::class, 'imprimir'])->name('inventario.imprimir');

// Rutas auxiliares para selects - ¡ESTAS SON LAS QUE FALTABAN!
Route::get('/productos/list', [ProductoController::class, 'list'])->name('productos.list');
Route::get('/categorias/list', [CategoriaController::class, 'list'])->name('categorias.list');
Route::get('/proveedores/list', [proveedorController::class, 'list'])->name('proveedores.list');


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
  
Route::get('/proveedores/lista', [ProveedorController::class, 'getLista'])->name('proveedores.lista');
Route::resource('proveedores', ProveedorController::class);
Route::get('proveedores-data', [ProveedorController::class, 'getData'])->name('proveedores.data');


// ====================================================

// RUTAS PARA ORDENES DE COMPRA

// ====================================================


Route::get('ordenes-compra',                          [OrdenCompraController::class, 'index'])->name('ordenes-compra');
Route::get('ordenes-compra/data',                     [OrdenCompraController::class, 'getData'])->name('ordenes-compra.data');
Route::get('ordenes-compra/numero-siguiente',         [OrdenCompraController::class, 'numeroSiguiente'])->name('ordenes-compra.numero-siguiente');
Route::post('ordenes-compra',                         [OrdenCompraController::class, 'store'])->name('ordenes-compra.store');
Route::get('ordenes-compra/{id}',                     [OrdenCompraController::class, 'show'])->name('ordenes-compra.show');
Route::put('ordenes-compra/{id}',                     [OrdenCompraController::class, 'update'])->name('ordenes-compra.update');
Route::delete('ordenes-compra/{id}',                  [OrdenCompraController::class, 'destroy'])->name('ordenes-compra.destroy');
 
Route::post('ordenes-compra/{id}/cambiar-estado',     [OrdenCompraController::class, 'cambiarEstado'])->name('ordenes-compra.cambiar-estado');
Route::get('ordenes-compra/{id}/pdf',                 [OrdenCompraController::class, 'pdf'])->name('ordenes-compra.pdf');
 
// Búsqueda de proveedores para Select2
Route::get('buscar-proveedores',                      [OrdenCompraController::class, 'buscarProveedores'])->name('buscar-proveedores');