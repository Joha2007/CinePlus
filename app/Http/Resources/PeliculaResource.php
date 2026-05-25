<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeliculaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id_pelicula,
            'titulo'      => $this->nom_pelicula,
            'descripcion' => $this->descripcion,
            'duracion'    => $this->duracion,
            'rango_edad'  => $this->rango_edad,
            'imagen_url'  => $this->img ? asset('storage/' . $this->img) : null,
            'categorias'  => CategoriaResource::collection($this->whenLoaded('categorias')),
            'horarios'    => HorarioResource::collection($this->whenLoaded('horarios')),
        ];
    }
}
