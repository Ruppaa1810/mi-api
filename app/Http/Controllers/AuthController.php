<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $usuario = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password,
                'role'     => 'usuario',
            ]);

            $token = $usuario->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso.',
                'data'    => [
                    'usuario' => $usuario,
                    'token'   => $token,
                ],
            ], 201);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $usuario = User::where('email', $request->email)->first();

            if (!$usuario || !Hash::check($request->password, $usuario->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales son incorrectas.'],
                ]);
            }

            $token = $usuario->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso.',
                'data'    => [
                    'usuario' => $usuario,
                    'token'   => $token,
                ],
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user(),
        ]);
    }
}
