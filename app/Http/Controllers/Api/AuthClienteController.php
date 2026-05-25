<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthClienteController extends Controller
{
    /**
     * Registro de nuevo cliente.
     * POST /api/v1/auth/cliente/register
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre_cliente'   => 'required|string|max:100',              // Nombre obligatorio, solo texto, máximo 100 caracteres
            'apellido_cliente' => 'required|string|max:100',              // Apellido obligatorio, solo texto, máximo 100 caracteres
            'correo_cli'       => 'required|email|unique:clientes,correo_cli', // Correo válido y único en la tabla clientes
            'edad_cli'         => 'required|integer|min:18|max:85',
            'contrasena_cli'   => 'required|string|min:6|confirmed',      // Contraseña mínimo 6 caracteres, debe coincidir con contrasena_cli_confirmation
            'contacto_cli'     => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',               // Número de contacto obligatorio, máximo 20 caracteres
        ]);

        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);
        $cliente = Cliente::create($data);
        $token   = $cliente->createToken('cliente-token', ['role:cliente'])->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso.',
            'cliente' => new ClienteResource($cliente),
            'token'   => $token,
        ], 201);
    }

    /**
     * Login de cliente.
     * POST /api/v1/auth/cliente/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'correo_cli'     => 'required|email',
            'contrasena_cli' => 'required|string',
        ]);

        $cliente = Cliente::where('correo_cli', $request->correo_cli)->first();

        if (! $cliente || ! Hash::check($request->contrasena_cli, $cliente->contrasena_cli)) {
            throw ValidationException::withMessages([
                'correo_cli' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $cliente->tokens()->delete();
        $token = $cliente->createToken('cliente-token', ['role:cliente'])->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'cliente' => new ClienteResource($cliente),
            'token'   => $token,
        ]);
    }

    /**
     * GET /api/v1/cliente/me — retorna el cliente autenticado.
     */
    public function me(Request $request): ClienteResource
    {
        return new ClienteResource($request->user()->load('reservas'));
    }

    /**
     * POST /api/v1/cliente/logout — revoca el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
