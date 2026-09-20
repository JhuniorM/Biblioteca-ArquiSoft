<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->cascadeOnDelete();
            $table->unsignedInteger('monto_centavos');
            $table->string('moneda', 3)->default('PEN');
            $table->string('proveedor', 30)->default('demo');
            $table->string('estado', 20)->default('aprobado');
            $table->string('referencia', 40)->unique();
            $table->timestamps();

            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};