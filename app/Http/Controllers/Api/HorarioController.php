<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Http\Resources\HorarioResource;
use App\Models\Horario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HorarioController extends Controller
{
    /**
     * GET /api/v1/horarios
     * Parámetros opcionales: ?fecha=&sala_id=&pelicula_id=&per_page=
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Horario::with(['pelicula', 'sala.sucursal']);

        if ($fecha = $request->query('fecha')) {
            $query->whereDate('fecha', $fecha);
        }

        if ($salaId = $request->query('sala_id')) {
            $query->where('id_sala2', $salaId);
        }

        if ($peliculaId = $request->query('pelicula_id')) {
            $query->where('id_pelicula1', $peliculaId);
        }

        if ($request->query('proximos')) {
            $query->where('fecha', '>=', now()->toDateString());
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        return HorarioResource::collection($query->orderBy('fecha')->orderBy('hora_inicio')->paginate($perPage));
    }

    public function store(StoreHorarioRequest $request): HorarioResource
    {
        $horario = Horario::create($request->validated());
        return new HorarioResource($horario->load(['pelicula', 'sala']));
    }

    public function show(Horario $horario): HorarioResource
    {
        $horario->load(['pelicula', 'sala.sucursal']);
        return new HorarioResource($horario);
    }

    public function update(UpdateHorarioRequest $request, Horario $horario): HorarioResource
    {
        $horario->update($request->validated());
        return new HorarioResource($horario->load(['pelicula', 'sala']));
    }

    public function destroy(Horario $horario): JsonResponse
    {
        $horario->delete();
        return response()->json(['message' => 'Horario eliminado correctamente']);
    }
}
