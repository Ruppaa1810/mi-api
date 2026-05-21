<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        // Trae los productos con sus categorías y marcas de forma anidada
        $productos = Producto::with(['categoria', 'marca'])->get();
        return response()->json($productos, 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nombre'        => 'required|string|max:255',
                'descripcion'   => 'nullable|string',
                'precio'        => 'required|numeric|min:0',
                'unidad_medida' => 'required|in:unidad,metro,kg,m2',
                'stock'         => 'required|integer|min:0',
                'categoria_id'  => 'required|exists:categorias,id',
                'marca_id'      => 'required|exists:marcas,id' // Validación de la marca
            ]);

            $producto = Producto::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente.',
                'data'    => $producto
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        // También incluimos la marca en la vista de un solo producto
        $producto = Producto::with(['categoria', 'marca'])->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $producto = Producto::find($id);

            if (!$producto) {
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            $request->validate([
                'nombre'        => 'required|string|max:255',
                'precio'        => 'required|numeric',
                'unidad_medida' => 'required|in:unidad,metro,kg,m2',
                'stock'         => 'required|integer',
                'categoria_id'  => 'required|exists:categorias,id',
                'marca_id'      => 'required|exists:marcas,id' // Validación de la marca
            ]);

            $producto->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
                'data'    => $producto
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $producto = Producto::find($id);

            if (!$producto) {
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}