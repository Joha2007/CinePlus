<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsientoSeeder extends Seeder
{
    public function run(): void
    {
        $asientos = [];
        $filas = ['A', 'B', 'C', 'D', 'E'];

        // Sala 1 — 5 filas x 10 asientos = 50
        foreach ($filas as $fila) {
            for ($num = 1; $num <= 10; $num++) {
                $asientos[] = [
                    'id_sala1'    => 1,
                    'num_fila'    => $fila,
                    'num_asiento' => $num,
                    'estado'      => 'Disponible',
                ];
            }
        }

        // Sala 2 — 5 filas x 8 asientos = 40
        foreach ($filas as $fila) {
            for ($num = 1; $num <= 8; $num++) {
                $asientos[] = [
                    'id_sala1'    => 2,
                    'num_fila'    => $fila,
                    'num_asiento' => $num,
                    'estado'      => 'Disponible',
                ];
            }
        }

        DB::table('asientos')->insert($asientos);
    }
}