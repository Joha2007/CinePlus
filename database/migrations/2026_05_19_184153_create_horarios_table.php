<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {

            $table->id('id_horario');

            $table->unsignedBigInteger('id_pelicula1');
            $table->unsignedBigInteger('id_sala2');

            $table->time('hora_inicio');
            $table->date('fecha');
            $table->string('tec_proyeco');

            $table->timestamps();

            $table->foreign('id_pelicula1')
                  ->references('id_pelicula')
                  ->on('peliculas')
                  ->onDelete('cascade');

            $table->foreign('id_sala2')
                  ->references('id_sala')
                  ->on('salas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};