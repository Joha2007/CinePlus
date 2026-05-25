<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id_sala,
            'num_sala'  => $this->num_sala,
            'capacidad' => $this->capaci_sala,
            'sucursal'  => new SucursalResource($this->whenLoaded('sucursal')),
            'asientos'  => AsientoResource::collection($this->whenLoaded('asientos')),
        ];
    }
}
