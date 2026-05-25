<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdministradorRequest extends FormRequest
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
        $id = $this->route('administrador')?->id_admin;

        return [
            'id_suc'        => 'sometimes|exists:sucursales,id_suc',
            'nombre_adm'    => 'sometimes|string|max:100',
            'apellido_adm'  => 'sometimes|string|max:100',
            'correo_adm'    => "sometimes|email|unique:administradores,correo_adm,{$id},id_admin",
            'contacto_adm'  => 'sometimes|string|max:9|regex:/^\d{4}-\d{4}$/',
            'contrasena_adm'=> 'sometimes|nullable|string|min:6',
        ];
    }

     public function messages(): array
    {
        return [
            'id_suc.required'        => 'La sucursal es obligatoria.',
            'id_suc.exists'          => 'La sucursal seleccionada no existe.',
            'correo_adm.unique'      => 'Este correo ya está registrado.',
            'contrasena_adm.min'     => 'La contraseña debe tener al menos 6 caracteres.',
            'contacto_adm.required'     => 'El contacto es obligatorio.',
            'contacto_adm.regex'        => 'El contacto debe tener el formato 1234-5678.',
            'contacto_adm.max'          => 'El contacto no debe superar los 9 caracteres.',
        ];
    }
}
