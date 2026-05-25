<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePeliculaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_pelicula' => 'required|string|max:150',
            'descripcion'  => 'required|string',
            'duracion'     => 'required|integer|min:1',
            'img'          => 'nullable|string|max:255',
            'rango_edad'   => 'required|in:TP,+7,+13,+18',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id_categoria',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_pelicula.required' => 'El nombre de la película es obligatorio.',
            'duracion.min'          => 'La duración debe ser al menos 1 minuto.',
            'rango_edad.in'         => 'El rango de edad debe ser TP, +7, +13 o +18.',
            'categorias.*.exists'   => 'Una o más categorías no existen.',
        ];
    }
}
