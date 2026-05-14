<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CategoriaController extends Controller 
{
    /**
     * Listar todas las categorías.
     */
    public function index(): JsonResponse 
    {
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    /**
     * Crear una nueva categoría.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // 1. Validar (nombre obligatorio, máximo 50 caracteres y único)
            $request->validate([
                'nombre' => 'required|string|max:50|unique:categorias,nombre',
            ]);

            // 2. Intentar guardar
            $categoria = Categoria::create($request->all());

            // 3. Respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente.',
                'data'    => $categoria
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una categoría específica.
     */
    public function show($id): JsonResponse
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json($categoria, 200);
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            // Validar: el nombre es único pero ignora el ID actual para permitir guardar sin cambios
            $request->validate([
                'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $id
            ]);

            $categoria->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente.',
                'data'    => $categoria
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}