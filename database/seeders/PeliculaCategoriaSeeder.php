<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeliculaCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelicula_categoria')->insert([
            // Deadpool & Wolverine → Acción, Comedia
            ['id_pelicula' => 1, 'id_categoria' => 1],
            ['id_pelicula' => 1, 'id_categoria' => 2],
            // Inside Out 2 → Animación, Comedia, Drama
            ['id_pelicula' => 2, 'id_categoria' => 5],
            ['id_pelicula' => 2, 'id_categoria' => 2],
            ['id_pelicula' => 2, 'id_categoria' => 4],
            // Alien: Romulus → Terror, Ciencia Ficción, Suspenso
            ['id_pelicula' => 3, 'id_categoria' => 3],
            ['id_pelicula' => 3, 'id_categoria' => 6],
            ['id_pelicula' => 3, 'id_categoria' => 8],
            // Twisters → Acción, Suspenso
            ['id_pelicula' => 4, 'id_categoria' => 1],
            ['id_pelicula' => 4, 'id_categoria' => 8],
        ]);
    }
}