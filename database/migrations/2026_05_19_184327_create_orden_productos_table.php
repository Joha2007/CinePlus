<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_productos', function (Blueprint $table) {

            $table->unsignedBigInteger('id_orden1');
            $table->unsignedBigInteger('id_producto1');

            $table->primary(['id_orden1', 'id_producto1']);

            $table->foreign('id_orden1')
                  ->references('id_orden')
                  ->on('orden_dulcerias')
                  ->onDelete('cascade');

            $table->foreign('id_producto1')
                  ->references('id_producto')
                  ->on('productos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_productos');
    }
};