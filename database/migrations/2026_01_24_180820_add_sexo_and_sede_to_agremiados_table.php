<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('agremiados', function (Blueprint $table) {
            // 'sexo' limitado a F y M
            $table->enum('sexo', ['F', 'M'])->after('apellidos')->nullable();

            // 'sede' con tus datos predeterminados (Ejemplo: Huánuco, Tingo María, etc.)
            $table->enum('sede', ['Huánuco', 'Tingo María'])
                ->after('sexo')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agremiados', function (Blueprint $table) {
            //
        });
    }
};
