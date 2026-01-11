<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PaymentController;

Route::apiResource('appointments', AppointmentController::class);
Route::apiResource('users', UserAccountController::class);
Route::apiResource('persons', PersonController::class);
Route::apiResource('payments', PaymentController::class);

?>
