<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('estudiante')->after('email');
            $table->index('role');
        });

        Schema::table('prestamos', function (Blueprint $table) {
            $table->date('fecha_vencimiento')->nullable()->after('fecha_prestamo');
            $table->index(['estado', 'fecha_vencimiento']);
        });
    }

    public function down(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropIndex(['estado', 'fecha_vencimiento']);
            $table->dropColumn('fecha_vencimiento');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
