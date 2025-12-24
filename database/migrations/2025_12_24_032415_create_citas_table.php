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
      Schema::create('citas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('paciente_id')->constrained('usuarios'); // Relación con la tabla usuarios (paciente)
        $table->foreignId('terapeuta_id')->constrained('usuarios'); // Relación con la tabla usuarios (terapeuta)
        $table->dateTime('fecha_hora'); // La fecha y hora de la cita
        $table->timestamps(); // Campos de marca de tiempo
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
