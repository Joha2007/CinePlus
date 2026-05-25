<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Models\Horario;
use Illuminate\Http\JsonResponse;

class HorarioController extends Controller
{
    public function index(): JsonResponse
    {
        $horarios = Horario::with(['pelicula', 'sala'])->get();
        return response()->json($horarios);
    }

    public function store(StoreHorarioRequest $request): JsonResponse
    {
        $horario = Horario::create($request->validated());
        return response()->json($horario->load(['pelicula', 'sala']), 201);
    }

    public function show(Horario $horario): JsonResponse
    {
        $horario->load(['pelicula', 'sala.sucursal']);
        return response()->json($horario);
    }

    public function update(UpdateHorarioRequest $request, Horario $horario): JsonResponse
    {
        $horario->update($request->validated());
        return response()->json($horario->load(['pelicula', 'sala']));
    }

    public function destroy(Horario $horario): JsonResponse
    {
        $horario->delete();
        return response()->json(['message' => 'Horario eliminado correctamente']);
    }
}
