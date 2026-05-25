<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id_reserva,
            'num_confirmacion' => $this->num_confirmacion,
            'estado'           => $this->estado,
            'metodo_pago'      => $this->metodo_pago,
            'monto'            => $this->monto,
            'fecha_compra'     => $this->fecha_compra,
            'cliente'          => new ClienteResource($this->whenLoaded('cliente')),
            'horario'          => new HorarioResource($this->whenLoaded('horario')),
            'asientos'         => AsientoResource::collection($this->whenLoaded('asientos')),
            'ordenes'          => OrdenDulceriaResource::collection($this->whenLoaded('ordenes')),
        ];
    }
}
