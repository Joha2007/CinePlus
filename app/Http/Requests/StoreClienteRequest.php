<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
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
            'nombre_cliente'   => 'required|string|max:100',
            'apellido_cliente' => 'required|string|max:100',
            'correo_cli'       => 'required|email|unique:clientes,correo_cli',
            'edad_cli'         => 'required|integer|min:18|max:85',
            'contrasena_cli'   => 'required|string|min:6',
            'contacto_cli'     => 'required|string|max:9|regex:/^\d{4}-\d{4}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'correo_cli.unique'    => 'Este correo ya está registrado.',
            'contrasena_cli.min'   => 'La contraseña debe tener al menos 6 caracteres.',
            'edad_cli.min'         => 'Debes tener al menos 18 años para registrarte.',
            'edad_cli.max'         => 'La edad no puede ser mayor a 85 años.',
            'contacto_cli.required'     => 'El contacto es obligatorio.',
            'contacto_cli.regex'        => 'El contacto debe tener el formato 1234-5678.',
            'contacto_cli.max'          => 'El contacto no debe superar los 9 caracteres.',
        ];
    }
}
