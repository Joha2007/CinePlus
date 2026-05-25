<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SucursalSeeder::class,
            AdministradorSeeder::class,
            CategoriaSeeder::class,
            PeliculaSeeder::class,
            PeliculaCategoriaSeeder::class,
            SalaSeeder::class,
            HorarioSeeder::class,
            AsientoSeeder::class,
            ClienteSeeder::class,
            ProductoSeeder::class,
            ReservaSeeder::class,
            ReservaAsientoSeeder::class,
            OrdenDulceriaSeeder::class,
            OrdenProductoSeeder::class,
        ]);
    }
}