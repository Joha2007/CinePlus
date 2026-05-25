<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id_cliente,
            'nombre'   => $this->nombre_cliente,
            'apellido' => $this->apellido_cliente,
            'correo'   => $this->correo_cli,
            'edad'     => $this->edad_cli,
            'contacto' => $this->contacto_cli,
            'reservas' => ReservaResource::collection($this->whenLoaded('reservas')),
        ];
    }
}
