<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes
Route::prefix('v1')->group(function () {

    // Products API
    Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/products/{product}', [App\Http\Controllers\Api\ProductController::class, 'show']);
    Route::get('/products/search', [App\Http\Controllers\Api\ProductController::class, 'search']);

    // Categories API
    Route::get('/categories', [App\Http\Controllers\Api\ProductController::class, 'categories']);

    // Brands API
    Route::get('/brands', [App\Http\Controllers\Api\ProductController::class, 'brands']);

    // Filters
    Route::get('/filters', [App\Http\Controllers\Api\ProductController::class, 'filters']);

});

// Webhook routes (no CSRF protection needed)
Route::prefix('webhooks')->group(function () {

    // Yape payment notification
    Route::post('/yape', [App\Http\Controllers\Api\WebhookController::class, 'yape'])->name('webhook.yape');

});

// Authenticated API routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // User info
    Route::get('/user', function () {
        return request()->user();
    });

    // Cart API
    // Route::get('/cart', [App\Http\Controllers\Api\CartController::class, 'index']);
    // Route::post('/cart/add', [App\Http\Controllers\Api\CartController::class, 'add']);
    // Route::patch('/cart/{item}', [App\Http\Controllers\Api\CartController::class, 'update']);
    // Route::delete('/cart/{item}', [App\Http\Controllers\Api\CartController::class, 'remove']);

});
