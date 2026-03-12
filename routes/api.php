<?php

use App\Http\Controllers\Api\CancelOrderController;
use App\Http\Controllers\Api\CompleteOrderController;
use App\Http\Controllers\Api\CreateOrderController;
use App\Http\Controllers\Api\CreatePersonalAccessTokenController;
use App\Http\Controllers\Api\CreateUserController;
use App\Http\Controllers\Api\CreateVehicleController;
use App\Http\Controllers\Api\DeletePersonalAccessTokenController;
use App\Http\Controllers\Api\GetCurrentUserController;
use App\Http\Controllers\Api\GetOrderController;
use App\Http\Controllers\Api\GetOrdersController;
use App\Http\Controllers\Api\MatchOrderController;
use App\Http\Controllers\Api\PickupOrderController;
use App\Http\Controllers\Api\UpdateOrderReviewController;
use App\Http\Controllers\Api\UpdateUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', GetCurrentUserController::class)
    ->middleware('auth:sanctum');
Route::post('/personal-access-tokens', CreatePersonalAccessTokenController::class);
Route::delete('/personal-access-token', DeletePersonalAccessTokenController::class)
    ->middleware('auth:sanctum');
Route::post('/users', CreateUserController::class);
Route::put('/users', UpdateUserController::class)
    ->middleware('auth:sanctum');
Route::get('/orders', GetOrdersController::class)
    ->middleware('auth:sanctum');
Route::post('orders', CreateOrderController::class)
    ->middleware('auth:sanctum');
Route::get('orders/{order:uuid}', GetOrderController::class)
    ->middleware('auth:sanctum')
    ->can('view', 'order');
Route::post('orders/{order:uuid}/cancel', CancelOrderController::class)
    ->middleware('auth:sanctum')
    ->can('cancel', 'order');
Route::post('orders/{order:uuid}/match', MatchOrderController::class)
    ->middleware('auth:sanctum')
    ->can('match', 'order');
Route::post('orders/{order:uuid}/pickup', PickupOrderController::class)
    ->middleware('auth:sanctum')
    ->can('pickup', 'order');
Route::post('orders/{order:uuid}/complete', CompleteOrderController::class)
    ->middleware('auth:sanctum')
    ->can('complete', 'order');
Route::post('orders/{order:uuid}/review', UpdateOrderReviewController::class)
    ->middleware('auth:sanctum')
    ->can('review', 'order');
Route::post('vehicles', CreateVehicleController::class)
    ->middleware('auth:sanctum');
