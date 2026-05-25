<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaRequest;
use App\Http\Requests\UpdateSalaRequest;
use App\Http\Resources\SalaResource;
use App\Http\Resources\AsientoResource;
use App\Models\Sala;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SalaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SalaResource::collection(Sala::with('sucursal')->get());
    }

    public function store(StoreSalaRequest $request): SalaResource
    {
        $sala = Sala::create($request->validated());
        return new SalaResource($sala->load('sucursal'));
    }

    public function show(Sala $sala): SalaResource
    {
        $sala->load(['sucursal', 'horarios', 'asientos']);
        return new SalaResource($sala);
    }

    public function update(UpdateSalaRequest $request, Sala $sala): SalaResource
    {
        $sala->update($request->validated());
        return new SalaResource($sala->load('sucursal'));
    }

    public function destroy(Sala $sala): JsonResponse
    {
        $sala->delete();
        return response()->json(['message' => 'Sala eliminada correctamente']);
    }

    // GET /api/v1/salas/{sala}/asientos
    public function asientos(Sala $sala): AnonymousResourceCollection
    {
        return AsientoResource::collection($sala->asientos);
    }
}
