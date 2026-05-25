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
            'contacto_adm'  => 'sometimes|string|max:20',
            'contrasena_adm'=> 'sometimes|nullable|string|min:6',
        ];
    }
}
