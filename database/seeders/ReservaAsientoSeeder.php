<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservaAsientoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reserva_asiento')->insert([
            ['id_reserva1' => 1, 'id_asiento1' => 1],
            ['id_reserva1' => 1, 'id_asiento1' => 2],
            ['id_reserva1' => 2, 'id_asiento1' => 3],
            ['id_reserva1' => 3, 'id_asiento1' => 11],
            ['id_reserva1' => 4, 'id_asiento1' => 12],
            ['id_reserva1' => 4, 'id_asiento1' => 13],
        ]);

        // Marcar asientos ocupados
        DB::table('asientos')
            ->whereIn('id_asiento', [1, 2, 3, 11, 12, 13])
            ->update(['estado' => 'Ocupado']);
    }
}