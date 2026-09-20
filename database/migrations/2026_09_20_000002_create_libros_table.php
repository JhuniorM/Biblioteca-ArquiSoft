<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->restrictOnDelete();
            $table->string('titulo');
            $table->string('autor');
            $table->string('isbn', 17)->nullable()->unique();
            $table->string('editorial')->nullable();
            $table->unsignedSmallInteger('anio_publicacion')->nullable();
            $table->unsignedInteger('ejemplares_totales')->default(1);
            $table->unsignedInteger('ejemplares_disponibles')->default(1);
            $table->timestamps();

            $table->index(['titulo', 'autor']);
            $table->index('autor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};