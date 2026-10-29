<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Producto::with(['categoria', 'marca']);

        // Busca la palabra en el nombre o en la descripción (Notion)
        if ($request->has('q') && $request->q !== '') {
            $busqueda = '%' . mb_strtolower($request->q) . '%';
            $query->where(function ($q) use ($busqueda) {
                $q->whereRaw('LOWER(nombre) LIKE ?', [$busqueda])
                  ->orWhereRaw('LOWER(descripcion) LIKE ?', [$busqueda]);
            });
        }

        // La tienda solo muestra los productos activos y con stock (Notion)
        if ($request->boolean('solo_disponibles')) {
            $query->where('estado', 'Activo')->where('stock', '>', 0);
        }

        if ($request->has('categoria_id') && $request->categoria_id !== '') {
            $ids = Categoria::where('categoria_padre_id', $request->categoria_id)
                ->pluck('id')
                ->push((int) $request->categoria_id);
            $query->whereIn('categoria_id', $ids);
        }

        if ($request->has('marca_id') && $request->marca_id !== '') {
            $query->where('marca_id', $request->marca_id);
        }

        if ($request->has('precio_min') && $request->precio_min !== '') {
            $query->where('precio', '>=', $request->precio_min);
        }

        if ($request->has('precio_max') && $request->precio_max !== '') {
            $query->where('precio', '<=', $request->precio_max);
        }

        $orden = $request->get('orden');
        if ($orden === 'precio_asc') {
            $query->orderBy('precio', 'asc');
        } elseif ($orden === 'precio_desc') {
            $query->orderBy('precio', 'desc');
        }

        return response()->json($query->get(), 200);
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
                'estado'        => 'required|in:Activo,Inactivo', // Validación ENUM
                'categoria_id'  => 'required|exists:categorias,id',
                'marca_id'      => 'required|exists:marcas,id',
                'imagen_url'    => 'nullable|url|max:2000'
            ]);

            $producto = Producto::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente.',
                'data'    => $producto
            ], 201);

        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
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
                'estado'        => 'required|in:Activo,Inactivo', // Validación ENUM
                'categoria_id'  => 'required|exists:categorias,id',
                'marca_id'      => 'required|exists:marcas,id',
                'imagen_url'    => 'nullable|url|max:2000'
            ]);

            $producto->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
                'data'    => $producto
            ], 200);

        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
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
            if ($e instanceof ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarPreciosMasivo(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids'        => 'required|array|min:1',
                'ids.*'      => 'integer|exists:productos,id',
                'porcentaje' => 'required|numeric'
            ]);

            $productos = Producto::whereIn('id', $request->ids)->get();

            foreach ($productos as $producto) {
                $nuevoPrecio = $producto->precio * (1 + ($request->porcentaje / 100));
                $producto->update(['precio' => round($nuevoPrecio, 2)]);
            }

            return response()->json([
                'success' => true,
                'message' => "Se actualizó el precio de {$productos->count()} producto(s).",
                'data'    => $productos
            ], 200);

        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los precios.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
