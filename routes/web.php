<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;



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
Route::post('guardar-imagen', [ProductoController::class, 'guardarImagen'])->name('guardarImagen');



// ======================================================

//  RUTAS PARA CAJA

// ======================================================


// Rutas para la gestión de caja
Route::prefix('caja')->name('caja.')->group(function () {
    Route::get('/caja', [CajaController::class, 'index'])->name('caja');
    Route::post('/abrir', [CajaController::class, 'abrirCaja'])->name('caja.abrir');
    Route::post('/cerrar', [CajaController::class, 'cerrarCaja'])->name('caja.cerrar');
    Route::post('/movimiento', [CajaController::class, 'registrarMovimiento'])->name('caja.movimiento');
    Route::get('/movimientos', [CajaController::class, 'obtenerMovimientos'])->name('caja.movimientos');
     Route::get('historial', [CajaController::class, 'historial'])->name('caja_historial');
});

     /*
 
        Route::get('caja', [CajaController::class, 'index'])->name('caja');
        Route::get('estado', [CajaController::class, 'estado'])->name('caja_estado');
        Route::post('abrir', [CajaController::class, 'abrir'])->name('caja_abrir');
        Route::post('cerrar', [CajaController::class, 'cerrar'])->name('caja_cerrar');
        Route::get('historial', [CajaController::class, 'historial'])->name('caja_historial');
        Route::get('{id}/detalles', [CajaController::class, 'detalles'])->name('caja_detalles');

        
     */
