<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta login básica (para evitar error de ruta no definida)
Route::get('/login', function () {
    return response()->json([
        'message' => 'Esta es la interfaz web. Por favor usa /api/login para autenticación API.'
    ]);
})->name('login');