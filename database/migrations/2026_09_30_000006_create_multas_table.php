<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->cascadeOnDelete();
            $table->unsignedInteger('monto_centavos');
            $table->unsignedInteger('dias_atraso');
            $table->string('estado', 20)->default('pendiente');
            $table->timestamp('pagada_en')->nullable();
            $table->timestamps();

            $table->unique('prestamo_id');
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multas');
    }
};
