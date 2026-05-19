<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    protected $table = 'salas';
    protected $primaryKey = 'id_sala';

    protected $fillable = [
        'id_suc2',
        'capaci_sala',
        'num_sala'
    ];

    protected function casts(): array
    {
        return [
            'capaci_sala' => 'integer',
            'num_sala' => 'integer',
        ];
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_suc2');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_sala2');
    }

    public function asientos()
    {
        return $this->hasMany(Asiento::class, 'id_sala1');
    }
}