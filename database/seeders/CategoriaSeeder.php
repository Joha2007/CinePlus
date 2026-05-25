<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categorias')->insert([
            ['nom_categoria' => 'Acción'],
            ['nom_categoria' => 'Comedia'],
            ['nom_categoria' => 'Terror'],
            ['nom_categoria' => 'Drama'],
            ['nom_categoria' => 'Animación'],
            ['nom_categoria' => 'Ciencia Ficción'],
            ['nom_categoria' => 'Romance'],
            ['nom_categoria' => 'Suspenso'],
        ]);
    }
}