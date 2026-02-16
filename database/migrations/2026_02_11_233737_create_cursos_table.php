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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            // 1. Datos del Curso
            $table->string('nombre_curso');
            $table->string('organizador');
            $table->enum('modalidad', ['Presencial', 'Virtual']);
            $table->integer('horas_lectivas');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // 2. Ponente
            $table->string('ponente_nombres');
            $table->string('ponente_especialidad');

            // 3. Archivos necesarios para el guardado de los certificados
            $table->string('imagen_path')->nullable();
            $table->string('certificado_path')->nullable();
            $table->string('firma1_path')->nullable();
            $table->string('firma2_path')->nullable();

            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at (borrado suave)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
