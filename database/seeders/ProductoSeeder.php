<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('productos')->insert([
            ['id_admin1' => 1, 'nom_productos' => 'Palomitas Grandes',  'descripcion' => 'Palomitas de maíz tamaño grande con mantequilla', 'stock' => 100, 'precio_producto' => 3.50, 'img' => 'snacks/Palomitas Grandes.jpeg'],
            ['id_admin1' => 1, 'nom_productos' => 'Palomitas Medianas', 'descripcion' => 'Palomitas de maíz tamaño mediano',                 'stock' => 150, 'precio_producto' => 2.50 , 'img' => 'snacks/Palomitas Medianas.jpeg'],
            ['id_admin1' => 2, 'nom_productos' => 'Refresco Grande',    'descripcion' => 'Bebida gaseosa tamaño grande 32 oz',               'stock' => 200, 'precio_producto' => 2.75, 'img' => 'snacks/Refresco Grande.jpeg'],
            ['id_admin1' => 2, 'nom_productos' => 'Refresco Mediano',   'descripcion' => 'Bebida gaseosa tamaño mediano 22 oz',              'stock' => 200, 'precio_producto' => 2.00, 'img' => 'snacks/Refresco Mediano.jpeg'],
            ['id_admin1' => 3, 'nom_productos' => 'Nachos con Queso',   'descripcion' => 'Nachos con salsa de queso y jalapeños',            'stock' => 80,  'precio_producto' => 3.00, 'img' => 'snacks/Nachos con Queso.jpeg'],
            ['id_admin1' => 3, 'nom_productos' => 'Hot Dog',            'descripcion' => 'Hot dog con mostaza y ketchup',                   'stock' => 60,  'precio_producto' => 2.25, 'img' => 'snacks/Hot Dog.jpeg'],
        ]);
    }
}