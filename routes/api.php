<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return auth()->user(); // Mendapatkan data user yang terautentikasi
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return response()->json(['message' => 'Welcome Admin']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/product', [ProductController::class, 'insert'])->middleware('role:admin');
    Route::get('/products', [ProductController::class, 'getProducts'])->middleware('role:admin');
    Route::get('/products_active', [ProductController::class, 'getProductsActive']);
    Route::put('/product/{id}/status', [ProductController::class, 'updateStatus'])->middleware('role:admin');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->middleware('role:admin');
    Route::put('/product/{id}', [ProductController::class, 'update'])->middleware('role:admin');
});

