<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthAdministradorController extends Controller
{
    /**
     * Login de administrador.
     * Verifica las credenciales del administrador, revoca tokens anteriores
     * y genera un nuevo token de acceso con rol de admin.
     */
    public function login(Request $request): JsonResponse
    {
        // Validación de los campos de acceso del administrador
        $request->validate([
            'correo_adm'     => 'required|email',    // Correo del administrador obligatorio y con formato válido
            'contrasena_adm' => 'required|string',   // Contraseña del administrador obligatoria
        ]);

        // Buscar al administrador por su correo electrónico
        $admin = Administrador::where('correo_adm', $request->correo_adm)->first();

        // Verificar que el administrador exista y que la contraseña sea correcta
        if (! $admin || ! Hash::check($request->contrasena_adm, $admin->contrasena_adm)) {
            throw ValidationException::withMessages([
                'correo_adm' => ['Las credenciales son incorrectas.'],
            ]);
        }

        // Revocar tokens anteriores para evitar sesiones duplicadas
        $admin->tokens()->delete();

        // Generar un nuevo token de acceso con el rol de administrador
        $token = $admin->createToken('admin-token', ['role:admin'])->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'admin'   => $admin->load('sucursal'),
            'token'   => $token,
        ]);
    }

    /**
     * Retorna el administrador autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load(['sucursal', 'productos']));
    }

    /**
     * Logout — revoca el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
