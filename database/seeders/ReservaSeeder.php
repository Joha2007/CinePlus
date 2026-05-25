<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reservas')->insert([
            ['id_cliente1' => 1, 'id_horario1' => 1, 'fecha_compra' => '2026-05-20', 'metodo_pago' => 'Tarjeta', 'monto' => 7.00, 'estado' => 'Confirmada', 'num_confirmacion' => 'RES-0001'],
            ['id_cliente1' => 2, 'id_horario1' => 2, 'fecha_compra' => '2026-05-21', 'metodo_pago' => 'Efectivo', 'monto' => 6.00, 'estado' => 'Confirmada', 'num_confirmacion' => 'RES-0002'],
            ['id_cliente1' => 3, 'id_horario1' => 3, 'fecha_compra' => '2026-05-22', 'metodo_pago' => 'Tarjeta', 'monto' => 5.50, 'estado' => 'Pendiente',  'num_confirmacion' => 'RES-0003'],
            ['id_cliente1' => 4, 'id_horario1' => 4, 'fecha_compra' => '2026-05-22', 'metodo_pago' => 'Efectivo', 'monto' => 6.50, 'estado' => 'Confirmada', 'num_confirmacion' => 'RES-0004'],
        ]);
    }
}