<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('multa_id')->nullable()->after('prestamo_id')->constrained('multas')->nullOnDelete();
            $table->string('metodo_pago', 20)->default('tarjeta')->after('moneda');
            $table->string('ultimos_digitos', 4)->nullable()->after('metodo_pago');
            $table->index(['multa_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['multa_id', 'estado']);
            $table->dropConstrainedForeignId('multa_id');
            $table->dropColumn(['metodo_pago', 'ultimos_digitos']);
        });
    }
};