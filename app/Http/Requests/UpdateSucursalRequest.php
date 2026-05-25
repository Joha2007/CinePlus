<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSucursalRequest extends FormRequest
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
            'nombre_suc'   => 'sometimes|string|max:100',
            'dir_suc'      => 'sometimes|string|max:200',
            'contacto_suc' => 'sometimes|string|max:20',
        ];
    }
}
