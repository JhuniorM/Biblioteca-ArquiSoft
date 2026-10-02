<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Libro;
use App\Models\Multa;
use App\Models\User;
use App\Services\PrestamoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PrestamoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_prestamo_descuenta_ejemplar_y_define_vencimiento(): void
    {
        $libro = $this->crearLibro(2);
        $usuario = User::factory()->create();

        $prestamo = app(PrestamoService::class)->registrar($usuario, $libro, Carbon::parse('2026-10-01'));

        $this->assertSame('2026-10-16', $prestamo->fecha_vencimiento->toDateString());
        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'activo',
        ]);
        $this->assertDatabaseHas('libros', ['id' => $libro->id, 'ejemplares_disponibles' => 1]);
    }

    public function test_devolver_prestamo_restituye_ejemplar_y_genera_multa_por_atraso(): void
    {
        $libro = $this->crearLibro(1);
        $usuario = User::factory()->create();
        $service = app(PrestamoService::class);
        $prestamo = $service->registrar($usuario, $libro, Carbon::parse('2026-10-01'));

        $service->devolver($prestamo, Carbon::parse('2026-10-18'));

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'devuelto',
        ]);
        $this->assertDatabaseHas('libros', ['id' => $libro->id, 'ejemplares_disponibles' => 1]);
        $this->assertDatabaseHas('multas', [
            'prestamo_id' => $prestamo->id,
            'dias_atraso' => 2,
            'monto_centavos' => 200,
            'estado' => 'pendiente',
        ]);
    }

    public function test_usuario_con_multa_pendiente_no_puede_solicitar_otro_prestamo(): void
    {
        $libro = $this->crearLibro(2);
        $usuario = User::factory()->create();
        $service = app(PrestamoService::class);
        $prestamo = $service->registrar($usuario, $libro, Carbon::parse('2026-10-01'));
        $service->devolver($prestamo, Carbon::parse('2026-10-18'));

        $this->expectException(ValidationException::class);
        $service->registrar($usuario, $libro, Carbon::parse('2026-10-20'));

        $this->assertSame(1, Multa::query()->where('estado', 'pendiente')->count());
    }

    private function crearLibro(int $disponibles): Libro
    {
        $categoria = Categoria::create(['nombre' => fake()->unique()->word()]);

        return Libro::create([
            'categoria_id' => $categoria->id,
            'titulo' => fake()->sentence(3),
            'autor' => fake()->name(),
            'ejemplares_totales' => $disponibles,
            'ejemplares_disponibles' => $disponibles,
        ]);
    }
}
