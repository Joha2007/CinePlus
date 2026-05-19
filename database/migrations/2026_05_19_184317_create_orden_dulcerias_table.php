<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_dulcerias', function (Blueprint $table) {

            $table->id('id_orden');

            $table->unsignedBigInteger('id_reserva2');

            $table->decimal('total', 8, 2);
            $table->integer('cant_produc');

            $table->timestamps();

            $table->foreign('id_reserva2')
                  ->references('id_reserva')
                  ->on('reservas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_dulcerias');
    }
};