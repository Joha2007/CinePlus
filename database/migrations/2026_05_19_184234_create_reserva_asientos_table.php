<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva_asiento', function (Blueprint $table) {

            $table->unsignedBigInteger('id_reserva1');
            $table->unsignedBigInteger('id_asiento1');

            $table->primary(['id_reserva1', 'id_asiento1']);

            $table->foreign('id_reserva1')
                  ->references('id_reserva')
                  ->on('reservas')
                  ->onDelete('cascade');

            $table->foreign('id_asiento1')
                  ->references('id_asiento')
                  ->on('asientos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_asiento');
    }
};