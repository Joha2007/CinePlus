<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthClienteController extends Controller
{
    /**
     * Registro de nuevo cliente.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre_cliente'   => 'required|string|max:100',
            'apellido_cliente' => 'required|string|max:100',
            'correo_cli'       => 'required|email|unique:clientes,correo_cli',
            'edad_cli'         => 'required|integer|min:1|max:120',
            'contrasena_cli'   => 'required|string|min:6|confirmed',
            'contacto_cli'     => 'required|string|max:20',
        ]);

        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);

        $cliente = Cliente::create($data);
        $token   = $cliente->createToken('cliente-token', ['role:cliente'])->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso.',
            'cliente' => $cliente,
            'token'   => $token,
        ], 201);
    }

    /**
     * Login de cliente.
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

        // Revocar tokens anteriores
        $cliente->tokens()->delete();

        $token = $cliente->createToken('cliente-token', ['role:cliente'])->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'cliente' => $cliente,
            'token'   => $token,
        ]);
    }

    /**
     * Retorna el cliente autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('reservas'));
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
