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
        Schema::create('participante_congresos', function (Blueprint $table) {
            $table->id();
            $table->string('dni')->unique(); // Será usuario y contraseña
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('modalidad'); // Presencial / Virtual
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participante_congresos');
    }
};
