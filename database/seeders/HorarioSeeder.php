<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('horarios')->insert([
            ['id_pelicula1' => 1, 'id_sala2' => 1, 'hora_inicio' => '14:00:00', 'fecha' => '2026-06-01', 'tec_proyecc' => 'IMAX'],
            ['id_pelicula1' => 1, 'id_sala2' => 1, 'hora_inicio' => '18:00:00', 'fecha' => '2026-06-01', 'tec_proyecc' => '3D'],
            ['id_pelicula1' => 2, 'id_sala2' => 2, 'hora_inicio' => '15:30:00', 'fecha' => '2026-06-01', 'tec_proyecc' => '2D'],
            ['id_pelicula1' => 2, 'id_sala2' => 3, 'hora_inicio' => '20:00:00', 'fecha' => '2026-06-02', 'tec_proyecc' => '3D'],
            ['id_pelicula1' => 3, 'id_sala2' => 4, 'hora_inicio' => '16:00:00', 'fecha' => '2026-06-02', 'tec_proyecc' => '2D'],
            ['id_pelicula1' => 4, 'id_sala2' => 5, 'hora_inicio' => '19:00:00', 'fecha' => '2026-06-03', 'tec_proyecc' => 'IMAX'],
        ]);
    }
}