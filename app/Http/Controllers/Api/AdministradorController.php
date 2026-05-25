<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdministradorRequest;
use App\Http\Requests\UpdateAdministradorRequest;
use App\Models\Administrador;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AdministradorController extends Controller
{
    public function index(): JsonResponse
    {
        $administradores = Administrador::with('sucursal')->get();
        return response()->json($administradores);
    }

    public function store(StoreAdministradorRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['contrasena_adm'] = Hash::make($data['contrasena_adm']);
        $administrador = Administrador::create($data);
        return response()->json($administrador->load('sucursal'), 201);
    }

    public function show(Administrador $administrador): JsonResponse
    {
        $administrador->load(['sucursal', 'productos']);
        return response()->json($administrador);
    }

    public function update(UpdateAdministradorRequest $request, Administrador $administrador): JsonResponse
    {
        $data = $request->validated();
        if (!empty($data['contrasena_adm'])) {
            $data['contrasena_adm'] = Hash::make($data['contrasena_adm']);
        }
        $administrador->update($data);
        return response()->json($administrador->load('sucursal'));
    }

    public function destroy(Administrador $administrador): JsonResponse
    {
        $administrador->delete();
        return response()->json(['message' => 'Administrador eliminado correctamente']);
    }
}
