<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asiento extends Model
{
    protected $table = 'asientos';
    protected $primaryKey = 'id_asiento';

    protected $fillable = [
        'id_sala1',
        'num_fila',
        'estado'
    ];

    protected function casts(): array
    {
        return [
            'num_fila' => 'integer',
        ];
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'id_sala1');
    }

    public function reservas()
    {
        return $this->belongsToMany(
            Reserva::class,
            'reserva_asiento',
            'id_asiento1',
            'id_reserva1'
        );
    }
}