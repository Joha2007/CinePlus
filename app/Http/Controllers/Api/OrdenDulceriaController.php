<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdenDulceriaRequest;
use App\Http\Requests\UpdateOrdenDulceriaRequest;
use App\Http\Resources\OrdenDulceriaResource;
use App\Models\OrdenDulceria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrdenDulceriaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OrdenDulceriaResource::collection(
            OrdenDulceria::with(['reserva.cliente', 'productos'])->paginate(15)
        );
    }

    public function store(StoreOrdenDulceriaRequest $request): OrdenDulceriaResource
    {
        $data      = $request->validated();
        $productos = $data['productos'] ?? [];
        unset($data['productos']);

        $orden = OrdenDulceria::create($data);

        if (!empty($productos)) {
            $orden->productos()->sync($productos);
        }

        return new OrdenDulceriaResource($orden->load(['reserva', 'productos']));
    }

    public function show(OrdenDulceria $orden): OrdenDulceriaResource
    {
        $orden->load(['reserva.cliente', 'productos']);
        return new OrdenDulceriaResource($orden);
    }

    public function update(UpdateOrdenDulceriaRequest $request, OrdenDulceria $orden): OrdenDulceriaResource
    {
        $data      = $request->validated();
        $productos = $data['productos'] ?? null;
        unset($data['productos']);

        $orden->update($data);

        if (!is_null($productos)) {
            $orden->productos()->sync($productos);
        }

        return new OrdenDulceriaResource($orden->load(['reserva', 'productos']));
    }

    public function destroy(OrdenDulceria $orden): JsonResponse
    {
        $orden->delete();
        return response()->json(['message' => 'Orden eliminada correctamente']);
    }
}
