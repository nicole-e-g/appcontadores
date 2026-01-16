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
            // 1. Agregamos el discriminador y la fecha específica
            $table->string('tipo_pago'); // 'Habilidad' o 'Constancia'
            $table->date('fecha_pago')->nullable(); // Para registrar el día exacto
            
            // 2. Hacemos que los campos de cuotas sean opcionales para las constancias
            $table->integer('año')->nullable()->change();
            $table->integer('mes_inicio')->nullable()->change();
            $table->integer('mes_final')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['tipo_pago', 'fecha_pago']);
            $table->integer('año')->nullable(false)->change();
            $table->integer('mes_inicio')->nullable(false)->change();
            $table->integer('mes_final')->nullable(false)->change();
        });
    }
};
