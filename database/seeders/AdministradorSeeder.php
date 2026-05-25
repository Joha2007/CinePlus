<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdministradorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('administradores')->insert([
            ['id_suc' => 1, 'nombre_adm' => 'Carlos',  'apellido_adm' => 'Hernández', 'correo_adm' => 'carlos@cineplus.com', 'contacto_adm' => '7111-1001', 'contrasena_adm' => Hash::make('admin123')],
            ['id_suc' => 2, 'nombre_adm' => 'María',   'apellido_adm' => 'López',     'correo_adm' => 'maria@cineplus.com',  'contacto_adm' => '7111-1002', 'contrasena_adm' => Hash::make('admin456')],
            ['id_suc' => 3, 'nombre_adm' => 'Roberto', 'apellido_adm' => 'Martínez',  'correo_adm' => 'roberto@cineplus.com','contacto_adm' => '7111-1003', 'contrasena_adm' => Hash::make('admin789')],
        ]);
    }
}