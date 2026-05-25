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
     * Valida los datos del formulario, encripta la contraseña y crea el cliente en la BD.
     * Devuelve el cliente creado junto con su token de acceso (Sanctum).
     */
    public function register(Request $request): JsonResponse
    {
        // Validación de los campos requeridos para el registro
        $data = $request->validate([
            'nombre_cliente'   => 'required|string|max:100',              // Nombre obligatorio, solo texto, máximo 100 caracteres
            'apellido_cliente' => 'required|string|max:100',              // Apellido obligatorio, solo texto, máximo 100 caracteres
            'correo_cli'       => 'required|email|unique:clientes,correo_cli', // Correo válido y único en la tabla clientes
            'edad_cli'         => 'required|integer|min:1|max:120',       // Edad obligatoria, número entero entre 1 y 120
            'contrasena_cli'   => 'required|string|min:6|confirmed',      // Contraseña mínimo 6 caracteres, debe coincidir con contrasena_cli_confirmation
            'contacto_cli'     => 'required|string|max:20',               // Número de contacto obligatorio, máximo 20 caracteres
        ]);

        // Encriptar la contraseña antes de guardarla en la base de datos
        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);

        // Crear el cliente en la base de datos con los datos validados
        $cliente = Cliente::create($data);

        // Generar token de acceso con el rol de cliente
        $token   = $cliente->createToken('cliente-token', ['role:cliente'])->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso.',
            'cliente' => $cliente,
            'token'   => $token,
        ], 201);
    }

    /**
     * Login de cliente.
     * Verifica las credenciales, revoca tokens anteriores y genera uno nuevo.
     */
    public function login(Request $request): JsonResponse
    {
        // Validación básica de los campos de acceso
        $request->validate([
            'correo_cli'     => 'required|email',    // Correo obligatorio con formato de email válido
            'contrasena_cli' => 'required|string',   // Contraseña obligatoria
        ]);

        // Buscar al cliente por correo electrónico
        $cliente = Cliente::where('correo_cli', $request->correo_cli)->first();

        // Verificar que el cliente exista y que la contraseña sea correcta
        if (! $cliente || ! Hash::check($request->contrasena_cli, $cliente->contrasena_cli)) {
            throw ValidationException::withMessages([
                'correo_cli' => ['Las credenciales son incorrectas.'],
            ]);
        }

        // Revocar tokens anteriores para evitar sesiones duplicadas
        $cliente->tokens()->delete();

        // Generar un nuevo token de acceso con el rol de cliente
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
