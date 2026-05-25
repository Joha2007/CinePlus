<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSucursalRequest extends FormRequest
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
            'nombre_suc'   => 'required|string|max:100',
            'dir_suc'      => 'required|string|max:200',
            'contacto_suc' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_suc.required'   => 'El nombre de la sucursal es obligatorio.',
            'dir_suc.required'      => 'La dirección es obligatoria.',
            'contacto_suc.required' => 'El contacto es obligatorio.',
        ];
    }
}
