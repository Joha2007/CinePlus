<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
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
            'id_admin1'       => 'sometimes|exists:administradores,id_admin',
            'nom_productos'   => 'sometimes|string|max:100',
            'descripcion'     => 'sometimes|string',
            'stock'           => 'sometimes|integer|min:0',
            'precio_producto' => 'sometimes|numeric|min:0',
        ];
    }
}
