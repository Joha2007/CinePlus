<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    protected $table = 'peliculas';
    protected $primaryKey = 'id_pelicula';

    protected $fillable = [
        'nom_pelicula',
        'descripcion',
        'duracion'
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_pelicula1');
    }
}