<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use App\Http\Resources\SucursalResource;
use App\Http\Resources\SalaResource;
use App\Http\Resources\AdministradorResource;
use App\Models\Sucursal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SucursalController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SucursalResource::collection(Sucursal::with(['salas'])->get());
    }

    public function store(StoreSucursalRequest $request): SucursalResource
    {
        $sucursal = Sucursal::create($request->validated());
        return new SucursalResource($sucursal);
    }

    public function show(Sucursal $sucursal): SucursalResource
    {
        $sucursal->load(['salas', 'administradores']);
        return new SucursalResource($sucursal);
    }

    public function update(UpdateSucursalRequest $request, Sucursal $sucursal): SucursalResource
    {
        $sucursal->update($request->validated());
        return new SucursalResource($sucursal);
    }

    public function destroy(Sucursal $sucursal): JsonResponse
    {
        $sucursal->delete();
        return response()->json(['message' => 'Sucursal eliminada correctamente']);
    }

    // GET /api/v1/sucursales/{sucursal}/salas
    public function salas(Sucursal $sucursal): AnonymousResourceCollection
    {
        return SalaResource::collection($sucursal->salas()->with('horarios')->get());
    }

    // GET /api/v1/admin/sucursales/{sucursal}/administradores
    public function administradores(Sucursal $sucursal): AnonymousResourceCollection
    {
        return AdministradorResource::collection($sucursal->administradores);
    }
}
