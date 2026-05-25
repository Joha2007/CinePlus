<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Models\Reserva;
use Illuminate\Http\JsonResponse;

class ReservaController extends Controller
{
    public function index(): JsonResponse
    {
        $reservas = Reserva::with(['cliente', 'horario.pelicula', 'horario.sala', 'asientos', 'ordenes'])->get();
        return response()->json($reservas);
    }

    public function store(StoreReservaRequest $request): JsonResponse
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

        return response()->json($reserva->load(['cliente', 'horario.pelicula', 'asientos']), 201);
    }

    public function show(Reserva $reserva): JsonResponse
    {
        $reserva->load(['cliente', 'horario.pelicula', 'horario.sala.sucursal', 'asientos', 'ordenes.productos']);
        return response()->json($reserva);
    }

    public function update(UpdateReservaRequest $request, Reserva $reserva): JsonResponse
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

        return response()->json($reserva->load(['cliente', 'horario.pelicula', 'asientos']));
    }

    public function destroy(Reserva $reserva): JsonResponse
    {
        // Liberar asientos al cancelar
        $reserva->asientos()->update(['estado' => 'Disponible']);
        $reserva->delete();
        return response()->json(['message' => 'Reserva eliminada correctamente']);
    }

    // GET /api/reservas/{reserva}/asientos
    public function asientos(Reserva $reserva): JsonResponse
    {
        return response()->json($reserva->asientos);
    }

    // GET /api/reservas/{reserva}/ordenes
    public function ordenes(Reserva $reserva): JsonResponse
    {
        return response()->json($reserva->ordenes()->with('productos')->get());
    }
}
