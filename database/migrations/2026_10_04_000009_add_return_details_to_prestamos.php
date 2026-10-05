<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->string('estado_material', 20)->default('bueno')->after('devuelto_por_user_id');
            $table->text('observacion_devolucion')->nullable()->after('estado_material');
        });
    }

    public function down(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropColumn(['estado_material', 'observacion_devolucion']);
        });
    }
};