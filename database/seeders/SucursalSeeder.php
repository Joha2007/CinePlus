<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sucursales')->insert([
            ['nombre_suc' => 'CinePlus Multiplaza',  'dir_suc' => 'Multiplaza, Antiguo Cuscatlán', 'contacto_suc' => '2222-1111'],
            ['nombre_suc' => 'CinePlus Metrocentro', 'dir_suc' => 'Metrocentro, San Salvador',     'contacto_suc' => '2222-2222'],
            ['nombre_suc' => 'CinePlus Galerías',    'dir_suc' => 'Galerías Escalón, San Salvador', 'contacto_suc' => '2222-3333'],
        ]);
    }
}