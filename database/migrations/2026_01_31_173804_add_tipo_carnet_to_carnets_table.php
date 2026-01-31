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
        Schema::table('carnets', function (Blueprint $table) {
            // Añadimos el tipo con un enum para mayor orden
            $table->enum('tipo_tramite', ['Colegiatura', 'Duplicado'])->after('agremiado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carnets', function (Blueprint $table) {
            //
        });
    }
};
