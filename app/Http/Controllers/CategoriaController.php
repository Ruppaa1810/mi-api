<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller 
{
    public function index(): JsonResponse 
    {
        $categorias = Categoria::with('subcategorias')->get();
        return response()->json($categorias);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:50|unique:categorias,nombre',
                'categoria_padre_id' => 'nullable|exists:categorias,id',
            ]);

            $categoria = Categoria::create($request->all());

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

    public function show($id): JsonResponse
    {
        $categoria = Categoria::with('subcategorias', 'categoriaPadre')->find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json($categoria, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            $request->validate([
                'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $id,
                'categoria_padre_id' => 'nullable|exists:categorias,id',
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