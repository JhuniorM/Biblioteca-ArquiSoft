<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Libro;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibliotecaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_autenticado_registra_pago_y_prestamo(): void
    {
        $libro = $this->crearLibro();

        $this->post('/registro', [
            'name' => 'Ana Perez',
            'email' => 'ana@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('catalogo.index'));

        $this->post(route('pagos.store', $libro), [
            'metodo_pago' => 'tarjeta',
            'numero_tarjeta' => '4111111111111111',
            'vencimiento' => '12/30',
            'cvv' => '123',
        ])->assertRedirect();

        $this->assertDatabaseHas('prestamos', ['libro_id' => $libro->id, 'estado' => 'activo']);
        $this->assertDatabaseHas('pagos', ['metodo_pago' => 'tarjeta', 'ultimos_digitos' => '1111']);
        $this->assertDatabaseMissing('pagos', ['referencia' => '4111111111111111']);
    }

    public function test_checkout_permite_registrar_cliente_nuevo(): void
    {
        $libro = $this->crearLibro();

        $this->post(route('pagos.store', $libro), [
            'nombre' => 'Cliente Desde Checkout',
            'email' => 'checkout@example.com',
            'metodo_pago' => 'tarjeta',
            'numero_tarjeta' => '4111111111111111',
            'vencimiento' => '12/30',
            'cvv' => '123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'checkout@example.com', 'role' => 'estudiante']);
        $this->assertDatabaseHas('prestamos', ['libro_id' => $libro->id]);
    }

    public function test_entidad_bancaria_puede_rechazar_el_pago_antes_del_prestamo(): void
    {
        $libro = $this->crearLibro();

        $this->post(route('pagos.store', $libro), [
            'nombre' => 'Pago Rechazado',
            'email' => 'rechazado@example.com',
            'metodo_pago' => 'tarjeta',
            'numero_tarjeta' => '4111111111110002',
            'vencimiento' => '12/30',
            'cvv' => '123',
        ])->assertSessionHasErrors('pago');

        $this->assertDatabaseCount('prestamos', 0);
        $this->assertDatabaseCount('pagos', 0);
        $this->assertDatabaseCount('comprobantes', 0);
    }

    public function test_personal_puede_buscar_cliente_y_ver_su_historial(): void
    {
        $personal = User::factory()->create(['role' => 'bibliotecario']);
        $cliente = User::factory()->create(['name' => 'Cliente Visible', 'email' => 'cliente@example.com']);

        $this->actingAs($personal)
            ->get(route('prestamos.clientes', ['buscar' => 'cliente@example.com']))
            ->assertOk()
            ->assertSee('Cliente Visible')
            ->assertSee('cliente@example.com');
    }

    public function test_cajero_consulta_y_confirma_pago_registrado_por_bibliotecario(): void
    {
        $bibliotecario = User::factory()->create(['role' => 'bibliotecario']);
        $cajero = User::factory()->create(['role' => 'cajero']);
        $cliente = User::factory()->create(['name' => 'Cliente del pago']);
        $libro = $this->crearLibro();
        $prestamo = app(\App\Services\PrestamoService::class)->registrarPendiente($cliente, $libro);
        $pago = Pago::create([
            'prestamo_id' => $prestamo->id,
            'registrado_por_user_id' => $bibliotecario->id,
            'monto_centavos' => 1000,
            'metodo_pago' => 'yape',
            'referencia' => 'ARQ-CAJERO-TEST',
            'metodo_pago' => 'pendiente',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($cajero)
            ->get(route('pagos.gestion'))
            ->assertOk()
            ->assertSee('ARQ-CAJERO-TEST')
            ->assertSee('Cliente del pago');

        $this->actingAs($cajero)
            ->post(route('pagos.confirmar', $pago), ['metodo_pago' => 'yape'])
            ->assertRedirect();

        $this->assertDatabaseHas('pagos', [
            'id' => $pago->id,
            'confirmado_por_user_id' => $cajero->id,
            'estado' => 'aprobado',
            'metodo_pago' => 'yape',
        ]);
        $this->assertDatabaseHas('prestamos', ['id' => $prestamo->id, 'estado' => 'activo']);
        $this->assertDatabaseCount('comprobantes', 1);
    }

    public function test_estudiante_no_puede_consultar_panel_de_pagos(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'estudiante']))
            ->get(route('pagos.gestion'))
            ->assertForbidden();
    }

    public function test_cliente_no_puede_entrar_al_panel_de_personal(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'estudiante']))
            ->get(route('prestamos.clientes'))
            ->assertForbidden();
    }

    public function test_personal_registra_prestamo_presencial_a_nombre_del_cliente(): void
    {
        $personal = User::factory()->create(['role' => 'bibliotecario']);
        $cliente = User::factory()->create(['role' => 'estudiante']);
        $libro = $this->crearLibro();

        $this->actingAs($personal)
            ->post(route('prestamos.clientes.registrar'), [
                'cliente_id' => $cliente->id,
                'libro_id' => $libro->id,
            ])
            ->assertRedirect(route('prestamos.clientes', ['cliente' => $cliente->id]));

        $this->assertDatabaseHas('prestamos', [
            'user_id' => $cliente->id,
            'libro_id' => $libro->id,
            'estado' => 'pendiente_pago',
        ]);
        $this->assertDatabaseHas('pagos', ['estado' => 'pendiente', 'registrado_por_user_id' => $personal->id]);
        $this->assertDatabaseCount('comprobantes', 0);
        $this->assertDatabaseHas('libros', ['id' => $libro->id, 'ejemplares_disponibles' => 1]);
    }

    public function test_personal_puede_ver_a_quien_pertenece_cada_prestamo(): void
    {
        $personal = User::factory()->create(['role' => 'bibliotecario']);
        $cliente = User::factory()->create(['name' => 'Cliente del Libro', 'email' => 'dueno@example.com']);
        $libro = $this->crearLibro();
        app(\App\Services\PrestamoService::class)->registrar($cliente, $libro);

        $this->actingAs($personal)
            ->get(route('prestamos.todos'))
            ->assertOk()
            ->assertSee('Cliente del Libro')
            ->assertSee('dueno@example.com')
            ->assertSee($libro->titulo);
    }

    public function test_personal_puede_registrar_un_cliente_nuevo(): void
    {
        $personal = User::factory()->create(['role' => 'bibliotecario']);

        $this->actingAs($personal)
            ->post(route('prestamos.clientes.crear'), [
                'name' => 'Cliente Nuevo',
                'email' => 'nuevo@example.com',
                'password' => 'cliente12345',
                'password_confirmation' => 'cliente12345',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Cliente Nuevo',
            'email' => 'nuevo@example.com',
            'role' => 'estudiante',
        ]);

        $this->post(route('login.store'), [
            'email' => 'nuevo@example.com',
            'password' => 'cliente12345',
        ])->assertRedirect(route('catalogo.index'));
    }

    public function test_personal_con_modal_asigna_el_prestamo_al_cliente_indicado(): void
    {
        $personal = User::factory()->create(['role' => 'bibliotecario']);
        $libro = $this->crearLibro();

        $this->actingAs($personal)->post(route('pagos.store', $libro), [
            'nombre' => 'Cliente Presencial',
            'email' => 'presencial@example.com',
            'metodo_pago' => 'yape',
        ])->assertRedirect();

        $cliente = User::query()->where('email', 'presencial@example.com')->firstOrFail();
        $this->assertDatabaseHas('prestamos', ['user_id' => $cliente->id, 'libro_id' => $libro->id]);
        $this->assertDatabaseMissing('prestamos', ['user_id' => $personal->id, 'libro_id' => $libro->id]);
    }

    private function crearLibro(): Libro
    {
        $categoria = Categoria::create(['nombre' => 'Novela']);

        return Libro::create([
            'categoria_id' => $categoria->id,
            'titulo' => 'El archivo',
            'autor' => 'Ana Autor',
            'ejemplares_totales' => 1,
            'ejemplares_disponibles' => 1,
        ]);
    }
}