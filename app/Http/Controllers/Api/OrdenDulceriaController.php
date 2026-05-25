<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdenDulceriaRequest;
use App\Http\Requests\UpdateOrdenDulceriaRequest;
use App\Models\OrdenDulceria;
use Illuminate\Http\JsonResponse;

class OrdenDulceriaController extends Controller
{
    public function index(): JsonResponse
    {
        $ordenes = OrdenDulceria::with(['reserva.cliente', 'productos'])->get();
        return response()->json($ordenes);
    }

    public function store(StoreOrdenDulceriaRequest $request): JsonResponse
    {
        $data      = $request->validated();
        $productos = $data['productos'] ?? [];
        unset($data['productos']);

        $orden = OrdenDulceria::create($data);

        if (!empty($productos)) {
            $orden->productos()->sync($productos);
        }

        return response()->json($orden->load(['reserva', 'productos']), 201);
    }

    public function show(OrdenDulceria $orden): JsonResponse
    {
        $orden->load(['reserva.cliente', 'productos']);
        return response()->json($orden);
    }

    public function update(UpdateOrdenDulceriaRequest $request, OrdenDulceria $orden): JsonResponse
    {
        $data      = $request->validated();
        $productos = $data['productos'] ?? null;
        unset($data['productos']);

        $orden->update($data);

        if (!is_null($productos)) {
            $orden->productos()->sync($productos);
        }

        return response()->json($orden->load(['reserva', 'productos']));
    }

    public function destroy(OrdenDulceria $orden): JsonResponse
    {
        $orden->delete();
        return response()->json(['message' => 'Orden eliminada correctamente']);
    }
}
