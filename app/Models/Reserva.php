<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_cliente1',
        'id_horario1',
        'fecha_compra',
        'metodo_pago',
        'monto',
        'estado',
        'num_confirmacion'
    ];

    protected function casts(): array
    {
        return [
            'fecha_compra' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente1');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario1');
    }

    public function asientos()
    {
        return $this->belongsToMany(
            Asiento::class,
            'reserva_asiento',
            'id_reserva1',
            'id_asiento1'
        );
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenDulceria::class, 'id_reserva2');
    }
}