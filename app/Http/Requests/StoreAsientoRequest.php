<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAsientoRequest extends FormRequest
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
            'id_sala1'    => 'required|exists:salas,id_sala',
            'num_fila'    => 'required|string|size:1|regex:/^[A-Z]$/',
            'num_asiento' => 'required|integer|min:1',
            'estado'      => 'sometimes|in:Disponible,Ocupado',
        ];
    }

    public function messages(): array
    {
        return [
            'id_sala1.required'      => 'La sala es obligatoria.',
            'id_sala1.exists'        => 'La sala seleccionada no existe.',
            'num_fila.regex'         => 'La fila debe ser una letra mayúscula (A-Z).',
            'num_asiento.required'   => 'El número de asiento es obligatorio.',
            'estado.in'              => 'El estado debe ser Disponible u Ocupado.',
        ];
    }
}
