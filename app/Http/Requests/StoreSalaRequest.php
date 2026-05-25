<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalaRequest extends FormRequest
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
            'id_suc2'     => 'required|exists:sucursales,id_suc',
            'capaci_sala' => 'required|integer|min:1',
            'num_sala'    => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_suc2.required'     => 'La sucursal es obligatoria.',
            'id_suc2.exists'       => 'La sucursal seleccionada no existe.',
            'capaci_sala.required' => 'La capacidad de la sala es obligatoria.',
            'num_sala.required'    => 'El número de sala es obligatorio.',
        ];
    }
}
