<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaRequest;
use App\Http\Requests\UpdateSalaRequest;
use App\Models\Sala;
use Illuminate\Http\JsonResponse;

class SalaController extends Controller
{
    public function index(): JsonResponse
    {
        $salas = Sala::with('sucursal')->get();
        return response()->json($salas);
    }

    public function store(StoreSalaRequest $request): JsonResponse
    {
        $sala = Sala::create($request->validated());
        return response()->json($sala->load('sucursal'), 201);
    }

    public function show(Sala $sala): JsonResponse
    {
        $sala->load(['sucursal', 'horarios', 'asientos']);
        return response()->json($sala);
    }

    public function update(UpdateSalaRequest $request, Sala $sala): JsonResponse
    {
        $sala->update($request->validated());
        return response()->json($sala->load('sucursal'));
    }

    public function destroy(Sala $sala): JsonResponse
    {
        $sala->delete();
        return response()->json(['message' => 'Sala eliminada correctamente']);
    }

    // GET /api/salas/{sala}/asientos
    public function asientos(Sala $sala): JsonResponse
    {
        return response()->json($sala->asientos);
    }
}
