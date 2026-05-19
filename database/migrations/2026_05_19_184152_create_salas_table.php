<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salas', function (Blueprint $table) {

            $table->id('id_sala');

            $table->unsignedBigInteger('id_suc2');

            $table->integer('capaci_sala');
            $table->integer('num_sala');

            $table->timestamps();

            $table->foreign('id_suc2')
                  ->references('id_suc')
                  ->on('sucursales')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};