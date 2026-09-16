<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductCategoryImageController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductImageController;
use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    // bungkus ke middleware (Gerbang)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']); // get profile
        Route::post('/logout', [AuthController::class, 'logout']); // logout

        // Product Categories
        Route::get('product-categories/options', [ProductCategoryController::class, 'options']); // options
        Route::post('product-categories/{id}/image', [ProductCategoryImageController::class, 'store']); // upload image
        Route::apiResource('product-categories', ProductCategoryController::class); // pakai apiResource karena ada function operasi CRUD

        // Product
        Route::get('products/options', [ProductController::class, 'options']); // options
        Route::post('products/{id}/image', [ProductImageController::class, 'store']); // upload image
        Route::apiResource('products', ProductController::class); // pakai apiResource karena ada function operasi CRUD

        // Customer
        Route::get('customers/options', [CustomerController::class, 'options']); // options
        Route::apiResource('customers', CustomerController::class); // pakai apiResource karena ada function operasi CRUD
    });
});
