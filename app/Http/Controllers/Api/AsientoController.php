<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsientoRequest;
use App\Http\Requests\UpdateAsientoRequest;
use App\Models\Asiento;
use Illuminate\Http\JsonResponse;

class AsientoController extends Controller
{
    public function index(): JsonResponse
    {
        $asientos = Asiento::with('sala')->get();
        return response()->json($asientos);
    }

    public function store(StoreAsientoRequest $request): JsonResponse
    {
        $asiento = Asiento::create($request->validated());
        return response()->json($asiento->load('sala'), 201);
    }

    public function show(Asiento $asiento): JsonResponse
    {
        $asiento->load('sala.sucursal');
        return response()->json($asiento);
    }

    public function update(UpdateAsientoRequest $request, Asiento $asiento): JsonResponse
    {
        $asiento->update($request->validated());
        return response()->json($asiento->load('sala'));
    }

    public function destroy(Asiento $asiento): JsonResponse
    {
        $asiento->delete();
        return response()->json(['message' => 'Asiento eliminado correctamente']);
    }
}
