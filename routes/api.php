<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas de Categorías
Route::apiResource('categorias', CategoriaController::class);

// Rutas de Productos
Route::apiResource('productos', ProductoController::class);

// Ruta especial para la administración (la que usarás en el panel de control)
Route::post('productos/actualizar-precios', [ProductoController::class, 'actualizarPreciosMasivo']);