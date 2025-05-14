<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CajaController;

use App\Http\Controllers\Controller;



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






Route::get('crear_venta', [App\Http\Controllers\VentasController::class, 'index'])->name('crear_venta');

/*

Route::post('productos', ProductoController::class);
Route::post('clientes', ClienteController::class);
Route::post('categorias', CategoriaController::class);
Route::post('proveedores', ProveedorController::class);

*/




// ======================================================

//  RUTAS PARA CAJA

// ======================================================




 
        Route::get('caja', [CajaController::class, 'index'])->name('caja');
        Route::get('estado', [CajaController::class, 'estado'])->name('caja_estado');
        Route::post('abrir', [CajaController::class, 'abrir'])->name('caja_abrir');
        Route::post('cerrar', [CajaController::class, 'cerrar'])->name('caja_cerrar');
        Route::get('historial', [CajaController::class, 'historial'])->name('caja_historial');
        Route::get('{id}/detalles', [CajaController::class, 'detalles'])->name('caja_detalles');
    
