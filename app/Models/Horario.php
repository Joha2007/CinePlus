<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pelicula;
use App\Models\Sala;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';

    protected $fillable = [
        'id_pelicula1',
        'id_sala2',
        'hora_inicio',
        'fecha',
        'tec_proyecc',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class, 'id_pelicula1');
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'id_sala2');
    }
}