<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', [\App\Http\Controllers\AuthenticationController::class, 'register']);
Route::post('/auth/login', [\App\Http\Controllers\AuthenticationController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthenticationController::class, 'logout']);
});

Route::middleware('auth:sanctum')->prefix('carrier')->group(function () {
    Route::get('/', [\App\Http\Controllers\TestController::class, 'test']);
});

Route::middleware('auth:sanctum')->prefix('auctioneer')->group(function () {
    Route::get('/', [\App\Http\Controllers\TestController::class, 'test']);
});
