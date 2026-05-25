<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenDulceriaRequest extends FormRequest
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
            'id_reserva2' => 'required|exists:reservas,id_reserva',
            'total'       => 'required|numeric|min:0',
            'cant_produc' => 'required|integer|min:1',
            'productos'   => 'nullable|array',
            'productos.*' => 'exists:productos,id_producto',
        ];
    }

    public function messages(): array
    {
        return [
            'id_reserva2.required'  => 'La reserva es obligatoria.',
            'id_reserva2.exists'    => 'La reserva seleccionada no existe.',
            'total.required'        => 'El total es obligatorio.',
            'cant_produc.required'  => 'La cantidad de productos es obligatoria.',
            'productos.*.exists'    => 'Uno o más productos no existen.',
        ];
    }
}
