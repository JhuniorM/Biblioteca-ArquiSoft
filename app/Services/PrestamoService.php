<?php

namespace App\Services;

use App\Models\Libro;
use App\Models\Multa;
use App\Models\Prestamo;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class PrestamoService
{
    public function __construct(private readonly DatabaseManager $database) {}

    public function registrar(User $usuario, Libro $libro, ?CarbonInterface $fecha = null): Prestamo
    {
        $fecha = $fecha ?: now();

        return $this->database->transaction(function () use ($usuario, $libro, $fecha) {
            $tieneMultas = $usuario->prestamos()
                ->whereHas('multa', fn ($query) => $query->where('estado', 'pendiente'))
                ->exists();

            if ($tieneMultas) {
                throw ValidationException::withMessages([
                    'prestamo' => 'El usuario tiene multas pendientes de pago.',
                ]);
            }

            $libro = Libro::query()->lockForUpdate()->findOrFail($libro->id);

            if ($libro->ejemplares_disponibles < 1) {
                throw ValidationException::withMessages([
                    'libro' => 'El libro no tiene ejemplares disponibles.',
                ]);
            }

            $prestamo = $usuario->prestamos()->create([
                'libro_id' => $libro->id,
                'fecha_prestamo' => $fecha->toDateString(),
                'fecha_vencimiento' => $fecha->copy()->addDays(config('biblioteca.dias_prestamo'))->toDateString(),
                'estado' => 'activo',
            ]);

            $libro->decrement('ejemplares_disponibles');

            return $prestamo->load(['libro', 'usuario' => fn ($query) => $query->select('id', 'name', 'email')]);
        });
    }

    public function registrarPendiente(User $usuario, Libro $libro, ?CarbonInterface $fecha = null): Prestamo
    {
        $fecha = $fecha ?: now();

        return $this->database->transaction(function () use ($usuario, $libro, $fecha) {
            $tieneMultas = $usuario->prestamos()
                ->whereHas('multa', fn ($query) => $query->where('estado', 'pendiente'))
                ->exists();

            if ($tieneMultas) {
                throw ValidationException::withMessages([
                    'prestamo' => 'El usuario tiene multas pendientes de pago.',
                ]);
            }

            $libro = Libro::query()->lockForUpdate()->findOrFail($libro->id);

            if ($libro->ejemplares_disponibles < 1) {
                throw ValidationException::withMessages([
                    'libro' => 'El libro no tiene ejemplares disponibles.',
                ]);
            }

            return $usuario->prestamos()->create([
                'libro_id' => $libro->id,
                'fecha_prestamo' => $fecha->toDateString(),
                'fecha_vencimiento' => $fecha->copy()->addDays(config('biblioteca.dias_prestamo'))->toDateString(),
                'estado' => 'pendiente_pago',
            ])->load(['libro', 'usuario']);
        });
    }

    public function activarPendiente(Prestamo $prestamo): Prestamo
    {
        return $this->database->transaction(function () use ($prestamo) {
            $prestamo = Prestamo::query()->lockForUpdate()->findOrFail($prestamo->id);

            if ($prestamo->estado !== 'pendiente_pago') {
                throw ValidationException::withMessages([
                    'pago' => 'Este préstamo ya fue activado o cerrado.',
                ]);
            }

            $libro = Libro::query()->lockForUpdate()->findOrFail($prestamo->libro_id);

            if ($libro->ejemplares_disponibles < 1) {
                throw ValidationException::withMessages([
                    'libro' => 'El libro ya no tiene ejemplares disponibles.',
                ]);
            }

            $prestamo->update(['estado' => 'activo']);
            $libro->decrement('ejemplares_disponibles');

            return $prestamo->load(['libro', 'usuario']);
        });
    }

    public function devolver(Prestamo $prestamo, ?CarbonInterface $fecha = null, ?User $usuarioDevolucion = null, string $estadoMaterial = 'bueno', ?string $observacion = null): Prestamo
    {
        $fecha = $fecha ?: now();

        return $this->database->transaction(function () use ($prestamo, $fecha, $usuarioDevolucion, $estadoMaterial, $observacion) {
            $prestamo = Prestamo::query()->lockForUpdate()->findOrFail($prestamo->id);

            if ($prestamo->estado !== 'activo') {
                throw ValidationException::withMessages([
                    'prestamo' => 'El préstamo ya fue cerrado.',
                ]);
            }

            $libro = Libro::query()->lockForUpdate()->findOrFail($prestamo->libro_id);
            $diasAtraso = max(0, $prestamo->fecha_vencimiento->diffInDays($fecha, false));

            $prestamo->update([
                'fecha_devolucion' => $fecha->toDateString(),
                'devuelto_por_user_id' => $usuarioDevolucion?->id,
                'estado_material' => $estadoMaterial,
                'observacion_devolucion' => $observacion,
                'estado' => 'devuelto',
            ]);

            $libro->increment('ejemplares_disponibles');

            if ($diasAtraso > 0) {
                Multa::query()->updateOrCreate(
                    ['prestamo_id' => $prestamo->id],
                    [
                        'monto_centavos' => $diasAtraso * config('biblioteca.multa_por_dia_centavos'),
                        'dias_atraso' => $diasAtraso,
                        'estado' => 'pendiente',
                    ],
                );
            }

            return $prestamo->load(['libro', 'multa']);
        });
    }
}
