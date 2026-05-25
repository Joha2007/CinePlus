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
        'duracion',
        'img',
        'rango_edad',
    ];

    protected function casts(): array
    {
        return [
            'duracion' => 'integer',
        ];
    }

    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            'pelicula_categoria',
            'id_pelicula',
            'id_categoria'
        );
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_pelicula1');
    }
}