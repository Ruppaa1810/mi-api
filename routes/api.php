<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('categorias', CategoriaController::class);

Route::apiResource('marcas', MarcaController::class);

Route::apiResource('productos', ProductoController::class);

Route::post('productos/actualizar-precios', [ProductoController::class, 'actualizarPreciosMasivo']);