<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePeliculaRequest extends FormRequest
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
            'nom_pelicula' => 'sometimes|string|max:150',
            'descripcion'  => 'sometimes|string',
            'duracion'     => 'sometimes|integer|min:1',
            'img'          => 'nullable|string|max:255',
            'rango_edad'   => 'sometimes|in:TP,+7,+13,+18',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id_categoria',
        ];
    }
}
