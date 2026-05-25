<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelicula_categoria', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pelicula');
            $table->unsignedBigInteger('id_categoria');
            $table->primary(['id_pelicula', 'id_categoria']);

            $table->foreign('id_pelicula')
                  ->references('id_pelicula')
                  ->on('peliculas')
                  ->onDelete('cascade');

            $table->foreign('id_categoria')
                  ->references('id_categoria')
                  ->on('categorias')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelicula_categoria');
    }
};