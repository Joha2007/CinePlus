<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clientes')->insert([
            ['nombre_cliente' => 'Ana',   'apellido_cliente' => 'García',  'correo_cli' => 'ana@gmail.com',   'edad_cli' => 25, 'contrasena_cli' => Hash::make('pass123'), 'contacto_cli' => '7500-1001'],
            ['nombre_cliente' => 'Luis',  'apellido_cliente' => 'Ramos',   'correo_cli' => 'luis@gmail.com',  'edad_cli' => 30, 'contrasena_cli' => Hash::make('pass456'), 'contacto_cli' => '7500-1002'],
            ['nombre_cliente' => 'Sofía', 'apellido_cliente' => 'Morales', 'correo_cli' => 'sofia@gmail.com', 'edad_cli' => 22, 'contrasena_cli' => Hash::make('pass789'), 'contacto_cli' => '7500-1003'],
            ['nombre_cliente' => 'Diego', 'apellido_cliente' => 'Fuentes', 'correo_cli' => 'diego@gmail.com', 'edad_cli' => 28, 'contrasena_cli' => Hash::make('pass000'), 'contacto_cli' => '7500-1004'],
        ]);
    }
}