<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos', function (Blueprint $table) {
            $table->id('id_asiento');
            $table->unsignedBigInteger('id_sala1');
            $table->char('num_fila', 1);   // A, B, C, D, E
            $table->integer('num_asiento'); // 1, 2, 3...
            $table->string('estado')->default('Disponible'); // Disponible, Ocupado
            $table->timestamps();

            $table->foreign('id_sala1')
                  ->references('id_sala')
                  ->on('salas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos');
    }
};