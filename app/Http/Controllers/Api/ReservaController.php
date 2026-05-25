<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Http\Resources\AsientoResource;
use App\Http\Resources\OrdenDulceriaResource;
use App\Models\Reserva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReservaController extends Controller
{
    /**
     * GET /api/v1/admin/reservas  o  GET /api/v1/cliente/reservas
     * Parámetros opcionales: ?estado=&per_page=
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Reserva::with(['cliente', 'horario.pelicula', 'horario.sala', 'asientos', 'ordenes']);

        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        return ReservaResource::collection($query->latest()->paginate($perPage));
    }

    public function store(StoreReservaRequest $request): ReservaResource
    {
        $data     = $request->validated();
        $asientos = $data['asientos'] ?? [];
        unset($data['asientos']);

        $reserva = Reserva::create($data);

        if (!empty($asientos)) {
            $reserva->asientos()->sync($asientos);
            // Marcar asientos como ocupados
            \App\Models\Asiento::whereIn('id_asiento', $asientos)
                ->update(['estado' => 'Ocupado']);
        }

        return new ReservaResource($reserva->load(['cliente', 'horario.pelicula', 'asientos']));
    }

    public function show(Reserva $reserva): ReservaResource
    {
        $reserva->load(['cliente', 'horario.pelicula', 'horario.sala.sucursal', 'asientos', 'ordenes.productos']);
        return new ReservaResource($reserva);
    }

    public function update(UpdateReservaRequest $request, Reserva $reserva): ReservaResource
    {
        $data     = $request->validated();
        $asientos = $data['asientos'] ?? null;
        unset($data['asientos']);

        $reserva->update($data);

        if (!is_null($asientos)) {
            // Liberar asientos anteriores
            $reserva->asientos()->update(['estado' => 'Disponible']);
            $reserva->asientos()->sync($asientos);
            \App\Models\Asiento::whereIn('id_asiento', $asientos)
                ->update(['estado' => 'Ocupado']);
        }

        return new ReservaResource($reserva->load(['cliente', 'horario.pelicula', 'asientos']));
    }

    public function destroy(Reserva $reserva): JsonResponse
    {
        // Liberar asientos al cancelar
        $reserva->asientos()->update(['estado' => 'Disponible']);
        $reserva->delete();
        return response()->json(['message' => 'Reserva eliminada correctamente']);
    }

    // GET /api/v1/admin/reservas/{reserva}/asientos
    public function asientos(Reserva $reserva): AnonymousResourceCollection
    {
        return AsientoResource::collection($reserva->asientos);
    }

    // GET /api/v1/admin/reservas/{reserva}/ordenes
    public function ordenes(Reserva $reserva): AnonymousResourceCollection
    {
        return OrdenDulceriaResource::collection($reserva->ordenes()->with('productos')->get());
    }
}
