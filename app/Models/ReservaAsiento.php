<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaAsiento extends Model
{
    protected $table = 'reserva_asiento';

    public $timestamps = false;

    protected $fillable = [
        'id_reserva1',
        'id_asiento1'
    ];
}