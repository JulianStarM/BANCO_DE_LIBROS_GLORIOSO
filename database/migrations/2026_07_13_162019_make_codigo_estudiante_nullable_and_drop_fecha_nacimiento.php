<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->string('codigo_estudiante', 20)->nullable()->change();
            $table->dropColumn('fecha_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->string('codigo_estudiante', 20)->nullable(false)->change();
            $table->date('fecha_nacimiento')->nullable();
        });
    }
};
