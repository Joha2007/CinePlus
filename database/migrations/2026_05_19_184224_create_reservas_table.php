<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {

            $table->id('id_reserva');

            $table->unsignedBigInteger('id_cliente1');
            $table->unsignedBigInteger('id_horario1');

            $table->date('fecha_compra');
            $table->string('metodo_pago');
            $table->decimal('monto', 8, 2);
            $table->string('estado');
            $table->string('num_confirmacion');

            $table->timestamps();

            $table->foreign('id_cliente1')
                  ->references('id_cliente')
                  ->on('clientes')
                  ->onDelete('cascade');

            $table->foreign('id_horario1')
                  ->references('id_horario')
                  ->on('horarios')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};