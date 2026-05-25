<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SucursalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id_suc,
            'nombre'    => $this->nombre_suc,
            'direccion' => $this->dir_suc,
            'contacto'  => $this->contacto_suc,
            'salas'     => SalaResource::collection($this->whenLoaded('salas')),
        ];
    }
}
