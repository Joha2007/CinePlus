<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaRequest extends FormRequest
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
        $id = $this->route('reserva')?->id_reserva;

        return [
            'id_cliente1'      => 'sometimes|exists:clientes,id_cliente',
            'id_horario1'      => 'sometimes|exists:horarios,id_horario',
            'fecha_compra'     => 'sometimes|date',
            'metodo_pago'      => 'sometimes|in:Tarjeta,Efectivo,Transferencia',
            'monto'            => 'sometimes|numeric|min:0',
            'estado'           => 'sometimes|in:Pendiente,Confirmada,Cancelada',
            'num_confirmacion' => "sometimes|string|max:20|unique:reservas,num_confirmacion,{$id},id_reserva",
            'asientos'         => 'nullable|array',
            'asientos.*'       => 'exists:asientos,id_asiento',
        ];
    }
}
