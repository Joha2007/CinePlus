<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
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
        $id = $this->route('cliente')?->id_cliente;

        return [
            'nombre_cliente'   => 'sometimes|string|max:100',
            'apellido_cliente' => 'sometimes|string|max:100',
            'correo_cli'       => "sometimes|email|unique:clientes,correo_cli,{$id},id_cliente",
            'edad_cli'         => 'sometimes|integer|min:1|max:120',
            'contrasena_cli'   => 'sometimes|nullable|string|min:6',
            'contacto_cli'     => 'sometimes|string|max:20',
        ];
    }
}
