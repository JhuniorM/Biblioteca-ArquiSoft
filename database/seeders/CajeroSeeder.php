<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;

class CajeroSeeder extends Seeder
{
    public function run(): void
    {
        $configuracion = config('biblioteca.cajero');
        $email = trim((string) ($configuracion['email'] ?? ''));
        $password = (string) ($configuracion['password'] ?? '');

        if ($email === '' && $password === '') {
            $this->command?->warn('No se creó el cajero: configura BIBLIOTECA_CAJERO_EMAIL y BIBLIOTECA_CAJERO_PASSWORD.');

            return;
        }

        $datos = Validator::make([
            'nombre' => $configuracion['nombre'] ?? 'Cajero ArquiSoft',
            'email' => $email,
            'password' => $password,
        ], [
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:12'],
        ])->validate();

        User::updateOrCreate(
            ['email' => $datos['email']],
            [
                'name' => $datos['nombre'],
                'password' => $datos['password'],
                'role' => 'cajero',
            ],
        );

        $this->command?->info('La cuenta de cajero quedó habilitada.');
    }
}
