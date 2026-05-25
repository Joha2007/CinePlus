<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHorarioRequest extends FormRequest
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
            'id_pelicula1' => 'sometimes|exists:peliculas,id_pelicula',
            'id_sala2'     => 'sometimes|exists:salas,id_sala',
            'hora_inicio'  => 'sometimes|date_format:H:i',
            'fecha'        => 'sometimes|date',
            'tec_proyecc'  => 'sometimes|in:2D,3D,IMAX',
        ];
    }
}
