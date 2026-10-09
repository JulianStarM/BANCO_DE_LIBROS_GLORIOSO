<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('libros', 'cantidad_deteriorado')) {
            Schema::table('libros', function (Blueprint $table) {
                $table->unsignedInteger('cantidad_deteriorado')->default(0)->after('cantidad_deficiente');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('libros', 'cantidad_deteriorado')) {
            Schema::table('libros', function (Blueprint $table) {
                $table->dropColumn('cantidad_deteriorado');
            });
        }
    }
};
