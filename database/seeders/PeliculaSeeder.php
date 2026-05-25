<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeliculaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('peliculas')->insert([
            [
                'nom_pelicula' => 'Deadpool & Wolverine',
                'descripcion'  => 'Deadpool recluta a Wolverine para una misión que pondrá en riesgo el universo Marvel.',
                'duracion'     => 127,
                'img'          => 'peliculas/Deadpool & Wolverine.webp',
                'rango_edad'   => '+18',
            ],
            [
                'nom_pelicula' => 'Inside Out 2',
                'descripcion'  => 'Riley enfrenta la adolescencia junto a nuevas emociones que irrumpen en su mente.',
                'duracion'     => 100,
                'img'          => 'peliculas/Inside Out 2.webp',
                'rango_edad'   => 'TP',
            ],
            [
                'nom_pelicula' => 'Alien: Romulus',
                'descripcion'  => 'Un grupo de jóvenes colonizadores se enfrenta a la forma de vida más aterradora del universo.',
                'duracion'     => 119,
                'img'          => 'peliculas/Alien Romulus.webp',
                'rango_edad'   => '+18',
            ],
            [
                'nom_pelicula' => 'Twisters',
                'descripcion'  => 'Una cazadora de tormentas y un aventurero se unen para enfrentar tornados devastadores.',
                'duracion'     => 122,
                'img'          => 'peliculas/Twisters.webp',
                'rango_edad'   => '+13',
            ],
        ]);
    }
}