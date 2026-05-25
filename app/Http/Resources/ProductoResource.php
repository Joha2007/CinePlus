<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id_producto,
            'nombre'      => $this->nom_productos,
            'descripcion' => $this->descripcion,
            'precio'      => $this->precio_producto,
            'stock'       => $this->stock,
            'imagen_url'  => $this->img ? asset('storage/' . $this->img) : null,
            'sucursal_id' => optional($this->whenLoaded('administrador'))->id_suc ?? null,
        ];
    }
}
