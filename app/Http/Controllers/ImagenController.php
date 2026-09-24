<?php

namespace App\Http\Controllers;

use App\Models\Imagen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ImagenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'imagen.required' => 'Elegí una imagen.',
            'imagen.image'    => 'El archivo tiene que ser una imagen.',
            'imagen.mimes'    => 'Formatos permitidos: JPG, PNG o WEBP.',
            'imagen.max'      => 'La imagen no puede pesar más de 2 MB.',
        ]);

        $archivo = $request->file('imagen');

        $imagen = Imagen::create([
            'tipo'  => $archivo->getMimeType(),
            'datos' => base64_encode(file_get_contents($archivo->getRealPath())),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Imagen subida correctamente.',
            'data'    => ['url' => rtrim(config('app.url'), '/') . '/api/imagenes/' . $imagen->id],
        ], 201);
    }

    public function show($id): Response
    {
        $imagen = Imagen::findOrFail($id);

        return response(base64_decode($imagen->datos), 200, [
            'Content-Type'  => $imagen->tipo,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
