<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id_asiento,
            'sala_id'     => $this->id_sala1,
            'fila'        => $this->num_fila,
            'num_asiento' => $this->num_asiento,
            'estado'      => $this->estado,
        ];
    }
}
