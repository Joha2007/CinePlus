<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HorarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id_horario,
            'fecha'       => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'tecnologia'  => $this->tec_proyecc,
            'pelicula'    => new PeliculaResource($this->whenLoaded('pelicula')),
            'sala'        => new SalaResource($this->whenLoaded('sala')),
        ];
    }
}
