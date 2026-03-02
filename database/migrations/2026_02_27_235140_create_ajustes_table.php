<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_ajustes_table.php
    public function up(): void
    {
        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique(); // Ej: 'facturacion_activa'
            $table->boolean('valor')->default(false);
            $table->timestamps();
        });

        // Insertar el estado inicial (Desactivado por defecto)
        DB::table('ajustes')->insert([
            'clave' => 'facturacion_activa',
            'valor' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajustes');
    }
};
