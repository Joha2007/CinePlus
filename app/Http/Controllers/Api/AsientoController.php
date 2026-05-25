<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsientoRequest;
use App\Http\Requests\UpdateAsientoRequest;
use App\Http\Resources\AsientoResource;
use App\Models\Asiento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AsientoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AsientoResource::collection(Asiento::with('sala')->get());
    }

    public function store(StoreAsientoRequest $request): AsientoResource
    {
        $asiento = Asiento::create($request->validated());
        return new AsientoResource($asiento->load('sala'));
    }

    public function show(Asiento $asiento): AsientoResource
    {
        $asiento->load('sala.sucursal');
        return new AsientoResource($asiento);
    }

    public function update(UpdateAsientoRequest $request, Asiento $asiento): AsientoResource
    {
        $asiento->update($request->validated());
        return new AsientoResource($asiento->load('sala'));
    }

    public function destroy(Asiento $asiento): JsonResponse
    {
        $asiento->delete();
        return response()->json(['message' => 'Asiento eliminado correctamente']);
    }
}
