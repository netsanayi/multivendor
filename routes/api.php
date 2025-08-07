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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for future implementation
Route::middleware('auth:sanctum')->group(function () {
    // Products API
    Route::apiResource('products', \App\Modules\Products\Controllers\Api\ProductController::class);
    
    // Categories API
    Route::apiResource('categories', \App\Modules\Categories\Controllers\Api\CategoryController::class);
    
    // Cart API (future implementation)
    // Route::post('cart/add', [\App\Modules\Cart\Controllers\Api\CartController::class, 'add']);
    // Route::post('cart/update', [\App\Modules\Cart\Controllers\Api\CartController::class, 'update']);
    // Route::delete('cart/remove/{id}', [\App\Modules\Cart\Controllers\Api\CartController::class, 'remove']);
    // Route::get('cart', [\App\Modules\Cart\Controllers\Api\CartController::class, 'index']);
});

// Public API endpoints
Route::get('products', [\App\Modules\Products\Controllers\Api\ProductController::class, 'index']);
Route::get('products/{product}', [\App\Modules\Products\Controllers\Api\ProductController::class, 'show']);
Route::get('categories', [\App\Modules\Categories\Controllers\Api\CategoryController::class, 'index']);
Route::get('brands', [\App\Modules\Brands\Controllers\Api\BrandController::class, 'index']);
Route::get('currencies', [\App\Modules\Currencies\Controllers\Api\CurrencyController::class, 'index']);
