<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id('id_pelicula');
            $table->string('nom_pelicula');
            $table->text('descripcion');
            $table->integer('duracion'); // en minutos
            $table->string('img')->nullable();
            $table->string('rango_edad', 10)->default('TP'); // TP, +7, +13, +18
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};