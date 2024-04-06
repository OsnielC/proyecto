<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EoloelectricasController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(EoloelectricasController::class)->group(function(){
    Route::get('eoloelectricas', 'index');
    Route::get('eoloelectricas/tema', 'tema');
    Route::get('eoloelectricas/documento', 'documento');
    Route::get('eoloelectricas/lugar', 'lugar');
    Route::get('eoloelectricas/tipo', 'tipo');
    Route::get('eoloelectricas/fecha', 'fecha');
    Route::get('eoloelectricas/titulo', 'titulo');
    Route::get('eoloelectricas/institucion', 'institucion');
    Route::get('eoloelectricas/actor', 'actor');
    Route::get('eoloelectricas/autor', 'autor');
});

Route::get('/busqueda', 'BusquedaController@buscar');

