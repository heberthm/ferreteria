<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CajaMenorController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\ClienteController;




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



Route::get('clientes', [ClienteController::class, 'index'])->name('clientes');
Route::post('guardar_clientes', [ClienteController::class, 'store'])->name('guardar_clientes');
Route::get('buscar_cliente', [ClienteController::class, 'buscar'])->name('buscar_cliente');
Route::post('verificar_cliente', [ClienteController::class, 'verificarCliente'])->name('verificar_cliente');




// ======================================================

//  RUTAS PARA VENTAS

// ======================================================



Route::get('venta', [App\Http\Controllers\VentaController::class, 'index'])->name('venta');
Route::post('crear_venta', [App\Http\Controllers\VentaController::class, 'create'])->name('crear_venta');


// ======================================================

//  RUTAS PARA CATEGORIAS

// ======================================================

Route::get('categorias', [App\Http\Controllers\CategoriaController::class, 'index'])->name('categorias');
Route::post('/categorias', [App\Http\Controllers\CategoriaController::class, 'store'])->name('crear_categorias');

// ======================================================

//  RUTAS PARA PRODUCTOS

// ======================================================

Route::get('productos', [App\Http\Controllers\ProductoController::class, 'index'])->name('productos');
Route::post('/productos', [App\Http\Controllers\ProductoController::class, 'store'])->name('crear_productos');
Route::get('mostrar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'show'])->name('productos.show');
Route::get('editar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'edit'])->name('productos.edit');
Route::post('actualizar_producto/{id_producto}', [App\Http\Controllers\ProductoController::class, 'update'])->name('productos.update');
Route::delete('eliminar_producto/{id}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('productos.destroy');
Route::get('/productos/search', [ProductoController::class, 'search'])->name('productos.search');
Route::get('/productos/frecuentes', [ProductoController::class, 'frecuentes'])->name('productos.frecuentes');



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



// ====================================================

// RUTAS PARA VENTAS

// ===================================================



    Route::get('venta', [PuntoVentaController::class, 'index'])->name('venta');
    Route::post('/buscar-productos', [PuntoVentaController::class, 'buscarProductos'])->name('buscar-productos');
    Route::post('/buscar-clientes', [PuntoVentaController::class, 'buscarClientes'])->name('buscar-clientes');
    Route::post('/procesar-venta', [PuntoVentaController::class, 'procesarVenta'])->name('procesar-venta');
    Route::get('/ticket/{venta}', [PuntoVentaController::class, 'generarTicket'])->name('ticket');
    Route::get('/factura/{venta}', [PuntoVentaController::class, 'generarFactura'])->name('factura');

