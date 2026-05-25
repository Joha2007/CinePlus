<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
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
            'id_admin1'       => 'required|exists:administradores,id_admin',
            'nom_productos'   => 'required|string|max:100',
            'descripcion'     => 'required|string',
            'stock'           => 'required|integer|min:0',
            'precio_producto' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_admin1.required'       => 'El administrador es obligatorio.',
            'id_admin1.exists'         => 'El administrador seleccionado no existe.',
            'nom_productos.required'   => 'El nombre del producto es obligatorio.',
            'precio_producto.required' => 'El precio es obligatorio.',
            'stock.min'                => 'El stock no puede ser negativo.',
        ];
    }
}
