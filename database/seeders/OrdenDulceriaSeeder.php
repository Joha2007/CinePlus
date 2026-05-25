<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdenDulceriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orden_dulcerias')->insert([
            ['id_reserva2' => 1, 'total' => 6.25, 'cant_produc' => 2],
            ['id_reserva2' => 2, 'total' => 5.50, 'cant_produc' => 2],
            ['id_reserva2' => 3, 'total' => 3.50, 'cant_produc' => 1],
            ['id_reserva2' => 4, 'total' => 8.75, 'cant_produc' => 3],
        ]);
    }
}