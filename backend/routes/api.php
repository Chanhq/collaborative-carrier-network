<?php

use App\Http\Controllers\AuctioneerController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\TransportRequestController;
use App\Http\Middleware\Authorization\EnsureUserIsAuctioneerMiddleware;
use App\Http\Middleware\Authorization\EnsureUserIsCarrierMiddleware;
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

Route::post('/auth/register', [AuthenticationController::class, 'register']);
Route::post('/auth/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthenticationController::class, 'logout']);
    Route::get('/auth/user', [AuthenticationController::class, 'getAuthenticatedUser']);
});

Route::middleware(['auth:sanctum', EnsureUserIsCarrierMiddleware::class])->prefix('carrier-frontend')
    ->group(function () {
        Route::get('/transport-request', [CarrierController::class, 'getTransportRequests']);
        Route::post('/transport-request', [CarrierController::class, 'addTransportRequest']);
        Route::get('/cost-model', [CarrierController::class, 'getCostModel']);
        Route::post('/cost-model', [CarrierController::class, 'setCostModel']);
        Route::get('/map', [CarrierController::class, 'getMapData']);
        Route::get('/auction-evaluation', [CarrierController::class, 'getAuctionEvaluationData']);
        Route::put('/complete-transport-requests', [CarrierController::class, 'completeTransportRequests']);
    });

Route::middleware(['auth:sanctum', EnsureUserIsAuctioneerMiddleware::class ])->prefix('auctioneer-frontend')
    ->group(function () {
        Route::get(
            '/auction/transport-requests',
            [AuctioneerController::class, 'getAuctionData']
        );
        Route::post('/auction/start', [AuctioneerController::class, 'startAuction']);
    });
