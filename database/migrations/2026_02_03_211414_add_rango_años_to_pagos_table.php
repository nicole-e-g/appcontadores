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
        Schema::table('pagos', function (Blueprint $table) {
            // Renombramos para mantener los datos existentes de un solo año
            $table->renameColumn('año', 'año_inicio');

            // Agregamos el límite del rango como opcional
            $table->integer('año_final')->after('año')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->renameColumn('año_inicio', 'año');
            $table->dropColumn('año_final');
        });
    }
};
