<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;

// Rutas públicas (NO requieren autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas (requieren autenticación con Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // CRUD
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('users', UserAccountController::class);
    Route::apiResource('persons', PersonController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('services', ServiceController::class);
});