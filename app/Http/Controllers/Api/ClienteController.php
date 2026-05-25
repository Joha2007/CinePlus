<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Http\Resources\ReservaResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ClienteResource::collection(Cliente::paginate(15));
    }

    public function store(StoreClienteRequest $request): ClienteResource
    {
        $data = $request->validated();
        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);
        $cliente = Cliente::create($data);
        return new ClienteResource($cliente);
    }

    public function show(Cliente $cliente): ClienteResource
    {
        $cliente->load('reservas');
        return new ClienteResource($cliente);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): ClienteResource
    {
        $data = $request->validated();
        if (!empty($data['contrasena_cli'])) {
            $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);
        }
        $cliente->update($data);
        return new ClienteResource($cliente);
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    // GET /api/v1/admin/clientes/{cliente}/reservas
    public function reservas(Cliente $cliente): AnonymousResourceCollection
    {
        return ReservaResource::collection(
            $cliente->reservas()->with(['horario.pelicula', 'asientos'])->get()
        );
    }
}
