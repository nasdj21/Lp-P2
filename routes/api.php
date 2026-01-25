<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfessionalServiceController; 
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ClientController; 
use App\Http\Controllers\WorkerScheduleController;

// Rutas públicas (NO requieren autenticación)
Route::post('/login', [AuthController::class, 'login']);
//Route::post('/register', [AuthController::class, 'register']);
Route::post('register', [AuthController::class, 'registerCompleteUser']);

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
    Route::apiResource('professional-services', ProfessionalServiceController::class);
    Route::apiResource('professionals', ProfessionalController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('worker-schedules', WorkerScheduleController::class);
    Route::put('/update-user/{user_account_id}', [AuthController::class, 'updateCompleteUser']);

});