<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdenDulceriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id_orden,
            'reserva_id'  => $this->id_reserva2,
            'total'       => $this->total,
            'cant_produc' => $this->cant_produc,
            'productos'   => ProductoResource::collection($this->whenLoaded('productos')),
        ];
    }
}
