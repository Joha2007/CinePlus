<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSalaRequest extends FormRequest
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
            'id_suc2'     => 'sometimes|exists:sucursales,id_suc',
            'capaci_sala' => 'sometimes|integer|min:1',
            'num_sala'    => 'sometimes|integer|min:1',
        ];
    }
}
