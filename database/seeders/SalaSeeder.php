<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('salas')->insert([
            ['id_suc2' => 1, 'capaci_sala' => 50,  'num_sala' => 1],
            ['id_suc2' => 1, 'capaci_sala' => 40,  'num_sala' => 2],
            ['id_suc2' => 2, 'capaci_sala' => 60,  'num_sala' => 1],
            ['id_suc2' => 2, 'capaci_sala' => 45,  'num_sala' => 2],
            ['id_suc2' => 3, 'capaci_sala' => 55,  'num_sala' => 1],
        ]);
    }
}