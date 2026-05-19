<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {

            $table->id('id_cliente');

            $table->string('nombre_cliente');
            $table->string('apellido_cliente');
            $table->string('correo_cli')->unique();
            $table->integer('edad_cli');
            $table->string('contrasena_cli');
            $table->string('contacto_cli');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};