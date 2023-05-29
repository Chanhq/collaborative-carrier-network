<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TransportRequestController;
use App\Http\Middleware\Authorization\EnsureUserIsAuctioneerMiddleware;
use App\Http\Middleware\Authorization\EnsureUserIsCarrierMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Map;

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

Route::post('/auth/register', [AuthenticationController::class, 'register']);
Route::post('/auth/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthenticationController::class, 'logout']);
    Route::get('/auth/user', [AuthenticationController::class, 'getAuthenticatedUser']);
});

Route::middleware(['auth:sanctum', EnsureUserIsCarrierMiddleware::class])->prefix('carrier-frontend')
    ->group(function () {
        Route::post('/transport-request', [TransportRequestController::class, 'create']);
    });

Route::middleware(['auth:sanctum', EnsureUserIsAuctioneerMiddleware::class ])->prefix('auctioneer-frontend')
    ->group(function () {
        Route::get('/', function () {
            return 'OK';
        });
    });
