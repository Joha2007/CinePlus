<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {

            $table->id('id_producto');

            $table->unsignedBigInteger('id_admin1');

            $table->string('nom_productos');
            $table->text('descripcion');
            $table->integer('stock');
            $table->decimal('precio_producto', 8, 2);

            $table->timestamps();

            $table->foreign('id_admin1')
                  ->references('id_admin')
                  ->on('administradores')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};