<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ServicioController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para Citas
Route::resource('citas', CitaController::class);

// Rutas para Usuarios
Route::resource('usuarios', UsuarioController::class);

// Rutas para Reportes
Route::resource('reportes', ReporteController::class);

// Rutas para Pagos
Route::resource('pagos', PagoController::class);

// Rutas para Servicios
Route::resource('servicios', ServicioController::class);