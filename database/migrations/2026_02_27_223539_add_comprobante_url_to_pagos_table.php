<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Añadimos la columna como text porque las URLs de SIBI pueden ser largas
            $table->text('comprobante_url')->nullable()->after('comprobante');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('comprobante_url');
        });
    }
};
