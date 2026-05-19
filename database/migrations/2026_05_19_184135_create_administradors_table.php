<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administradores', function (Blueprint $table) {

            $table->id('id_admin');

            $table->unsignedBigInteger('id_suc');

            $table->string('nombre_adm');
            $table->string('apellido_adm');
            $table->string('correo_adm')->unique();
            $table->string('contacto_adm');
            $table->string('contrasena_adm');

            $table->timestamps();

            $table->foreign('id_suc')
                  ->references('id_suc')
                  ->on('sucursales')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administradores');
    }
};