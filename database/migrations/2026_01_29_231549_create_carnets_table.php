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
        Schema::create('carnets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos'); // Relación con el pago
            $table->foreignId('agremiado_id')->constrained('agremiados');
            $table->enum('estado_entrega', ['Pendiente', 'Entregado'])->default('Pendiente');
            $table->date('fecha_entrega')->nullable();
            $table->string('entregado_por')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnets');
    }
};
