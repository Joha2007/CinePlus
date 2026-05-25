<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdenProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orden_productos')->insert([
            // Orden 1: Palomitas Grandes + Refresco Grande
            ['id_orden1' => 1, 'id_producto1' => 1],
            ['id_orden1' => 1, 'id_producto1' => 3],
            // Orden 2: Palomitas Medianas + Refresco Mediano
            ['id_orden1' => 2, 'id_producto1' => 2],
            ['id_orden1' => 2, 'id_producto1' => 4],
            // Orden 3: Palomitas Grandes
            ['id_orden1' => 3, 'id_producto1' => 1],
            // Orden 4: Nachos + Refresco Grande + Hot Dog
            ['id_orden1' => 4, 'id_producto1' => 5],
            ['id_orden1' => 4, 'id_producto1' => 3],
            ['id_orden1' => 4, 'id_producto1' => 6],
        ]);
    }
}