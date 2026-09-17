<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('categorias', [CategoriaController::class, 'index']);
Route::get('categorias/{id}', [CategoriaController::class, 'show']);
Route::get('marcas', [MarcaController::class, 'index']);
Route::get('marcas/{id}', [MarcaController::class, 'show']);
Route::get('productos', [ProductoController::class, 'index']);
Route::get('productos/{id}', [ProductoController::class, 'show']);

Route::middleware('auth:sanctum', 'admin')->prefix('admin')->group(function () {
    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
    Route::apiResource('marcas', MarcaController::class)->except(['index', 'show']);
    Route::apiResource('productos', ProductoController::class)->except(['index', 'show']);
    Route::post('productos/actualizar-precios', [ProductoController::class, 'actualizarPreciosMasivo']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
});
