<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdministradorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id_admin,
            'nombre'     => $this->nombre_adm,
            'apellido'   => $this->apellido_adm,
            'correo'     => $this->correo_adm,
            'contacto'   => $this->contacto_adm,
            'sucursal'   => new SucursalResource($this->whenLoaded('sucursal')),
        ];
    }
}
