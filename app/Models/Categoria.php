<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nom_categoria',
    ];

    public function peliculas()
    {
        return $this->belongsToMany(
            Pelicula::class,
            'pelicula_categoria',
            'id_categoria',
            'id_pelicula'
        );
    }
}
