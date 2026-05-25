<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use App\Models\Sucursal;
use Illuminate\Http\JsonResponse;

class SucursalController extends Controller
{
    public function index(): JsonResponse
    {
        $sucursales = Sucursal::with(['salas', 'administradores'])->get();
        return response()->json($sucursales);
    }

    public function store(StoreSucursalRequest $request): JsonResponse
    {
        $sucursal = Sucursal::create($request->validated());
        return response()->json($sucursal, 201);
    }

    public function show(Sucursal $sucursal): JsonResponse
    {
        $sucursal->load(['salas', 'administradores']);
        return response()->json($sucursal);
    }

    public function update(UpdateSucursalRequest $request, Sucursal $sucursal): JsonResponse
    {
        $sucursal->update($request->validated());
        return response()->json($sucursal);
    }

    public function destroy(Sucursal $sucursal): JsonResponse
    {
        $sucursal->delete();
        return response()->json(['message' => 'Sucursal eliminada correctamente'], 200);
    }

    // GET /api/sucursales/{sucursal}/salas
    public function salas(Sucursal $sucursal): JsonResponse
    {
        return response()->json($sucursal->salas()->with('horarios')->get());
    }

    // GET /api/sucursales/{sucursal}/administradores
    public function administradores(Sucursal $sucursal): JsonResponse
    {
        return response()->json($sucursal->administradores);
    }
}
