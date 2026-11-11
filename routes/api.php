<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Rutas Públicas ---
Route::post('register', [AuthController::class, 'register']);
// Máximo 5 intentos de login por minuto (Clase 5: limitar intentos)
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);

Route::get('/marcas', [MarcaController::class, 'index']);
Route::get('/marcas/{id}', [MarcaController::class, 'show']);

Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);

Route::get('/imagenes/{id}', [ImagenController::class, 'show']);

// --- Rutas Protegidas (Requieren Token) ---
Route::middleware('auth:sanctum')->group(function () {
    // Info del usuario actual
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);

    // Comprar y ver mis compras: cualquier usuario logueado
    Route::middleware('role:Cliente|Administrador')->group(function () {
        Route::post('/pedidos', [PedidoController::class, 'store']);
        Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos']);
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::post('/categorias', [CategoriaController::class, 'store']);
        Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
        Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

        Route::post('/marcas', [MarcaController::class, 'store']);
        Route::put('/marcas/{id}', [MarcaController::class, 'update']);
        Route::delete('/marcas/{id}', [MarcaController::class, 'destroy']);

        Route::post('/productos', [ProductoController::class, 'store']);
        Route::put('/productos/{id}', [ProductoController::class, 'update']);
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
        Route::post('/productos/actualizar-precios', [ProductoController::class, 'actualizarPreciosMasivo']);

        Route::post('/imagenes', [ImagenController::class, 'store']);

        Route::get('/pedidos', [PedidoController::class, 'index']);
        Route::put('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);
    });
});
