<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdministradorRequest;
use App\Http\Requests\UpdateAdministradorRequest;
use App\Http\Resources\AdministradorResource;
use App\Models\Administrador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class AdministradorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AdministradorResource::collection(Administrador::with('sucursal')->get());
    }

    public function store(StoreAdministradorRequest $request): AdministradorResource
    {
        $data = $request->validated();
        $data['contrasena_adm'] = Hash::make($data['contrasena_adm']);
        $administrador = Administrador::create($data);
        return new AdministradorResource($administrador->load('sucursal'));
    }

    public function show(Administrador $administrador): AdministradorResource
    {
        $administrador->load(['sucursal']);
        return new AdministradorResource($administrador);
    }

    public function update(UpdateAdministradorRequest $request, Administrador $administrador): AdministradorResource
    {
        $data = $request->validated();
        if (!empty($data['contrasena_adm'])) {
            $data['contrasena_adm'] = Hash::make($data['contrasena_adm']);
        }
        $administrador->update($data);
        return new AdministradorResource($administrador->load('sucursal'));
    }

    public function destroy(Administrador $administrador): JsonResponse
    {
        $administrador->delete();
        return response()->json(['message' => 'Administrador eliminado correctamente']);
    }
}
