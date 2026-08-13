<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MarcaController extends Controller
{
    public function index(): JsonResponse
    {
        // Traigo todas las marcas, junto con la cantidad de productos que tiene cada una
        // (productos_count se usa en el panel admin para mostrar la columna "Productos")
        $marcas = Marca::withCount('productos')->get();
        return response()->json($marcas);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:50|unique:marcas,nombre',
            ]);

            $marca = Marca::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Marca creada exitosamente.',
                'data'    => $marca
            ], 201);

        } catch (\Exception $e) {
            // Si es un error de validación, lo dejamos pasar para que Laravel responda 422
            if ($e instanceof ValidationException) {
                throw $e;
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la marca.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $marca = Marca::find($id);

        if (!$marca) {
            return response()->json(['message' => 'Marca no encontrada'], 404);
        }

        return response()->json($marca, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $marca = Marca::find($id);

            if (!$marca) {
                return response()->json(['message' => 'Marca no encontrada'], 404);
            }

            $request->validate([
                'nombre' => 'required|string|max:50|unique:marcas,nombre,' . $id
            ]);

            $marca->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada correctamente.',
                'data'    => $marca
            ], 200);

        } catch (\Exception $e) {
            // Si es un error de validación, lo dejamos pasar para que Laravel responda 422
            if ($e instanceof ValidationException) {
                throw $e;
            }

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
            $marca = Marca::find($id);

            if (!$marca) {
                return response()->json(['message' => 'Marca no encontrada'], 404);
            }

            $marca->delete();

            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada correctamente.'
            ], 200);

        } catch (\Exception $e) {
            // Si es un error de validación, lo dejamos pasar para que Laravel responda 422
            if ($e instanceof ValidationException) {
                throw $e;
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la marca.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
