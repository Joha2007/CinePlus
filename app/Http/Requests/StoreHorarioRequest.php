<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHorarioRequest extends FormRequest
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
            'id_pelicula1' => 'required|exists:peliculas,id_pelicula',
            'id_sala2'     => 'required|exists:salas,id_sala',
            'hora_inicio'  => 'required|date_format:H:i',
            'fecha'        => 'required|date|after_or_equal:today',
            'tec_proyecc'  => 'required|in:2D,3D,IMAX',
        ];
    }

    public function messages(): array
    {
        return [
            'id_pelicula1.required' => 'La película es obligatoria.',
            'id_pelicula1.exists'   => 'La película seleccionada no existe.',
            'id_sala2.required'     => 'La sala es obligatoria.',
            'id_sala2.exists'       => 'La sala seleccionada no existe.',
            'hora_inicio.date_format' => 'La hora debe tener formato HH:MM.',
            'fecha.after_or_equal'  => 'La fecha no puede ser anterior a hoy.',
            'tec_proyecc.in'        => 'La tecnología debe ser 2D, 3D o IMAX.',
        ];
    }
}
