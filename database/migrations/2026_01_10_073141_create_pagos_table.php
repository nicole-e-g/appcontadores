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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id(); // ID único para el pago
            
            // Relación con el agremiado: Debe coincidir con el ID de tu tabla 'agremiados'
            // onDelete('cascade') borra los pagos si borras al agremiado
            $table->foreignId('agremiado_id')->constrained('agremiados')->onDelete('cascade');
            
            // Datos del periodo que pides en tu modal
            $table->integer('año'); 
            $table->integer('mes_inicio'); // Guardaremos el número (1=Enero, 12=Diciembre)
            $table->integer('mes_final');
            
            // Datos del comprobante
            $table->string('comprobante'); 
            $table->decimal('monto', 8, 2)->default(20.00); // Monto sugerido de 20 soles
            
            // Para mantener el estándar de tu sistema
            $table->softDeletes(); // Habilita el borrado suave (deleted_at)
            $table->timestamps();  // Crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
