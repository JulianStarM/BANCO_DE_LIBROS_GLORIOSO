<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('modulo', 50)->index(); // estudiantes, libros, entregas, etc.
            $table->string('clave', 100);          // grados, secciones, campos_obligatorios
            $table->json('valor');                 // array de valores
            $table->string('descripcion')->nullable();
            $table->timestamps();

            $table->unique(['modulo', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
