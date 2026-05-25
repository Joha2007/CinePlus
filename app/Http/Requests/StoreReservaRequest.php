<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
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
            'id_cliente1'      => 'required|exists:clientes,id_cliente',
            'id_horario1'      => 'required|exists:horarios,id_horario',
            'fecha_compra'     => 'required|date',
            'metodo_pago'      => 'required|in:Tarjeta,Efectivo,Transferencia',
            'monto'            => 'required|numeric|min:0',
            'estado'           => 'required|in:Pendiente,Confirmada,Cancelada',
            'num_confirmacion' => 'required|string|max:20|unique:reservas,num_confirmacion',
            'asientos'         => 'nullable|array',
            'asientos.*'       => 'exists:asientos,id_asiento',
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente1.required'      => 'El cliente es obligatorio.',
            'id_cliente1.exists'        => 'El cliente seleccionado no existe.',
            'id_horario1.required'      => 'El horario es obligatorio.',
            'id_horario1.exists'        => 'El horario seleccionado no existe.',
            'metodo_pago.in'            => 'El método de pago debe ser Tarjeta, Efectivo o Transferencia.',
            'estado.in'                 => 'El estado debe ser Pendiente, Confirmada o Cancelada.',
            'num_confirmacion.unique'   => 'El número de confirmación ya existe.',
            'asientos.*.exists'         => 'Uno o más asientos no existen.',
        ];
    }
}
