<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CalendarController;

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


// ======================================================

//  RUTAS PARA CITAS MEDICAS

// ======================================================



Route::get('citas_medicas', [CalendarController::class, 'index']);


// ======================================================

//  RUTAS PARA ADMINISTRAR FULLCALENDAR

// ======================================================



Route::get('fullcalendareventmaster', [CalendarController::class, 'index']) ;

Route::post('fullcalendareventmaster/create', [CalendarController::class, 'create']);

Route::post('fullcalendareventmaster/update', [CalendarController::class, 'update']);

Route::delete('fullcalendareventmaster/delete/{id}', [CalendarController::class, 'destroy']);

Route::get('fullcalendareventmaster/update_event', [CalendarController::class, 'update_event']);
