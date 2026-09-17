<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->esAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tenés permisos para acceder a esta sección.',
            ], 403);
        }

        return $next($request);
    }
}
