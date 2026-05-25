<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdministradorRequest extends FormRequest
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
            'id_suc'        => 'required|exists:sucursales,id_suc',
            'nombre_adm'    => 'required|string|max:100',
            'apellido_adm'  => 'required|string|max:100',
            'correo_adm'    => 'required|email|unique:administradores,correo_adm',
            'contacto_adm'  => 'required|string|max:20',
            'contrasena_adm'=> 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'id_suc.required'        => 'La sucursal es obligatoria.',
            'id_suc.exists'          => 'La sucursal seleccionada no existe.',
            'correo_adm.unique'      => 'Este correo ya está registrado.',
            'contrasena_adm.min'     => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
