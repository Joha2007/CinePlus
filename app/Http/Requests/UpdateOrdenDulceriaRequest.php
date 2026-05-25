<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenDulceriaRequest extends FormRequest
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
            'id_reserva2' => 'sometimes|exists:reservas,id_reserva',
            'total'       => 'sometimes|numeric|min:0',
            'cant_produc' => 'sometimes|integer|min:1',
            'productos'   => 'nullable|array',
            'productos.*' => 'exists:productos,id_producto',
        ];
    }
}
