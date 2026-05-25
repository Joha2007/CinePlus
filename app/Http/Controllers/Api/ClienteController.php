<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index(): JsonResponse
    {
        $clientes = Cliente::all();
        return response()->json($clientes);
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);
        $cliente = Cliente::create($data);
        return response()->json($cliente, 201);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        $cliente->load('reservas');
        return response()->json($cliente);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $data = $request->validated();
        if (!empty($data['contrasena_cli'])) {
            $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);
        }
        $cliente->update($data);
        return response()->json($cliente);
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    // GET /api/clientes/{cliente}/reservas
    public function reservas(Cliente $cliente): JsonResponse
    {
        return response()->json($cliente->reservas()->with(['horario.pelicula', 'asientos'])->get());
    }
}
