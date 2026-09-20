<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')
                ->constrained('libros')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion')->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamps();

            $table->index(['user_id', 'estado']);
            $table->index(['libro_id', 'estado']);
            $table->index('fecha_prestamo');
            $table->index('fecha_devolucion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};