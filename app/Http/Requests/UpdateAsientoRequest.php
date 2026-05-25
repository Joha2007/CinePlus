<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAsientoRequest extends FormRequest
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
            'id_sala1'    => 'sometimes|exists:salas,id_sala',
            'num_fila'    => 'sometimes|string|size:1|regex:/^[A-Z]$/',
            'num_asiento' => 'sometimes|integer|min:1',
            'estado'      => 'sometimes|in:Disponible,Ocupado',
        ];
    }
}
