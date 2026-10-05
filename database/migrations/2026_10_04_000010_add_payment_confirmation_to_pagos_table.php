<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('registrado_por_user_id')->nullable()->after('prestamo_id')->constrained('users')->nullOnDelete();
            $table->foreignId('confirmado_por_user_id')->nullable()->after('registrado_por_user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('confirmado_en')->nullable()->after('confirmado_por_user_id');
            $table->index(['confirmado_en', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['confirmado_en', 'created_at']);
            $table->dropConstrainedForeignId('registrado_por_user_id');
            $table->dropConstrainedForeignId('confirmado_por_user_id');
            $table->dropColumn('confirmado_en');
        });
    }
};