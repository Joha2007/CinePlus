<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdministradorResource;
use App\Models\Administrador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthAdministradorController extends Controller
{
    /**
     * Login de administrador.
     * POST /api/v1/auth/admin/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'correo_adm'     => 'required|email',
            'contrasena_adm' => 'required|string',
        ]);

        $admin = Administrador::where('correo_adm', $request->correo_adm)->first();

        if (! $admin || ! Hash::check($request->contrasena_adm, $admin->contrasena_adm)) {
            throw ValidationException::withMessages([
                'correo_adm' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $admin->tokens()->delete();
        $token = $admin->createToken('admin-token', ['role:admin'])->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'admin'   => new AdministradorResource($admin->load('sucursal')),
            'token'   => $token,
        ]);
    }

    /**
     * GET /api/v1/admin/me — retorna el administrador autenticado.
     */
    public function me(Request $request): AdministradorResource
    {
        return new AdministradorResource($request->user()->load(['sucursal']));
    }

    /**
     * POST /api/v1/admin/logout — revoca el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
