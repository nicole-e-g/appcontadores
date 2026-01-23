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
        Schema::table('agremiados', function (Blueprint $table) {
            // Añadimos el RUC después del DNI para mantener el orden lógico
            $table->string('ruc', 11)->nullable()->unique()->after('dni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agremiados', function (Blueprint $table) {
            $table->dropColumn('ruc');
        });
    }
};
