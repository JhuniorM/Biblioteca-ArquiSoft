<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\PrestamoService;
use Database\Seeders\CajeroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BibliotecaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_no_puede_autoregistrarse_ni_completar_un_checkout_publico(): void
    {
        $libro = $this->crearLibro();

        $this->get('/registro')->assertNotFound();
        $this->get("/catalogo/{$libro->id}/alquilar")->assertNotFound();
        $this->post("/catalogo/{$libro->id}/alquilar")->assertNotFound();
        $this->get(route('catalogo.show', $libro))
            ->assertOk()
            ->assertSee('acércate a la biblioteca')
            ->assertDontSee('Solicitar préstamo');
    }

    public function test_cliente_no_puede_iniciar_sesion_ni_consultar_prestamos(): void
    {
        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => 'cliente12345',
            'role' => 'estudiante',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'cliente@example.com',
                'password' => 'cliente12345',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->get(route('prestamos.index'))->assertRedirect(route('login'));
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

    public function test_enlace_registrar_cliente_abre_el_formulario_de_clientes(): void
    {
        $personal = User::factory()->create(['role' => 'bibliotecario']);

        $this->actingAs($personal)
            ->get(route('prestamos.clientes', ['registrar' => 1]))
            ->assertOk()
            ->assertSee('id="registrar-cliente"', false)
            ->assertSee('Registrar nuevo cliente')
            ->assertSee('Guardar cliente y continuar');
    }

    public function test_cajero_consulta_y_confirma_pago_registrado_por_bibliotecario(): void
    {
        $bibliotecario = User::factory()->create(['role' => 'bibliotecario']);
        $cajero = User::factory()->create(['role' => 'cajero']);
        $cliente = User::factory()->create(['name' => 'Cliente del pago']);
        $libro = $this->crearLibro();
        $prestamo = app(PrestamoService::class)->registrarPendiente($cliente, $libro);
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

    public function test_flujo_completo_de_prestamo_pendiente_hasta_confirmacion_del_cajero(): void
    {
        $bibliotecario = User::factory()->create(['role' => 'bibliotecario']);
        $cajero = User::factory()->create(['role' => 'cajero']);
        $libro = $this->crearLibro();

        $this->actingAs($bibliotecario)
            ->post(route('prestamos.clientes.crear'), [
                'name' => 'Cliente del flujo completo',
                'email' => 'flujo@example.com',
            ])
            ->assertRedirect();

        $cliente = User::query()->where('email', 'flujo@example.com')->firstOrFail();

        $this->actingAs($bibliotecario)
            ->post(route('prestamos.clientes.registrar'), [
                'cliente_id' => $cliente->id,
                'libro_id' => $libro->id,
            ])
            ->assertRedirect(route('prestamos.clientes', ['cliente' => $cliente->id]));

        $prestamo = Prestamo::query()->where('user_id', $cliente->id)->firstOrFail();
        $pago = $prestamo->pago()->firstOrFail();

        $this->assertSame('pendiente_pago', $prestamo->estado);
        $this->assertSame('pendiente', $pago->estado);
        $this->assertDatabaseHas('libros', ['id' => $libro->id, 'ejemplares_disponibles' => 1]);

        $this->actingAs($cajero)
            ->get(route('pagos.gestion'))
            ->assertOk()
            ->assertSee('flujo@example.com')
            ->assertSee($pago->referencia);

        $this->actingAs($cajero)
            ->post(route('pagos.confirmar', $pago), ['metodo_pago' => 'yape'])
            ->assertRedirect();

        $this->assertDatabaseHas('prestamos', ['id' => $prestamo->id, 'estado' => 'activo']);
        $this->assertDatabaseHas('pagos', [
            'id' => $pago->id,
            'estado' => 'aprobado',
            'confirmado_por_user_id' => $cajero->id,
        ]);
        $this->assertDatabaseHas('libros', ['id' => $libro->id, 'ejemplares_disponibles' => 0]);
        $this->assertDatabaseCount('comprobantes', 1);
    }

    public function test_cajero_ve_la_lista_completa_de_prestamos_con_sus_clientes(): void
    {
        $cajero = User::factory()->create(['role' => 'cajero', 'name' => 'Cajero de Prueba']);
        $bibliotecario = User::factory()->create(['role' => 'bibliotecario']);
        $cliente = User::factory()->create(['name' => 'Cliente Presencial', 'email' => 'cliente-presencial@example.com']);
        $libroRegistrado = $this->crearLibro('Libro registrado por el personal');
        $prestamoRegistrado = app(PrestamoService::class)->registrarPendiente($cliente, $libroRegistrado);

        Pago::create([
            'prestamo_id' => $prestamoRegistrado->id,
            'registrado_por_user_id' => $bibliotecario->id,
            'monto_centavos' => 1000,
            'metodo_pago' => 'pendiente',
            'proveedor' => 'caja',
            'estado' => 'pendiente',
            'referencia' => 'ARQ-CLIENTE-REGISTRADO',
        ]);

        $otroCliente = User::factory()->create(['name' => 'Cliente de otro préstamo']);
        $libroNoRegistrado = $this->crearLibro('Libro no registrado por personal');
        app(PrestamoService::class)->registrar($otroCliente, $libroNoRegistrado);

        $this->actingAs($cajero)
            ->get(route('prestamos.index'))
            ->assertOk()
            ->assertSee('Préstamos registrados')
            ->assertSee('Cliente Presencial')
            ->assertSee('cliente-presencial@example.com')
            ->assertSee('Libro registrado por el personal')
            ->assertSee('pendiente')
            ->assertSee('Libro no registrado por personal')
            ->assertSee('Cliente de otro préstamo')
            ->assertDontSee('Cajero de Prueba');
    }

    public function test_cajero_configurado_se_crea_y_puede_entrar_a_gestion_de_pagos(): void
    {
        config()->set('biblioteca.cajero', [
            'nombre' => 'Cajero de Prueba',
            'email' => 'caja@example.com',
            'password' => 'contrasena-segura-123',
        ]);

        $this->seed(CajeroSeeder::class);

        $cajero = User::query()->where('email', 'caja@example.com')->firstOrFail();

        $this->assertSame('cajero', $cajero->role);
        $this->assertTrue(Hash::check('contrasena-segura-123', $cajero->password));

        $this->post(route('login.store'), [
            'email' => 'caja@example.com',
            'password' => 'contrasena-segura-123',
        ])->assertRedirect(route('catalogo.index'));

        $this->get(route('pagos.gestion'))
            ->assertOk()
            ->assertSee(route('pagos.gestion'))
            ->assertSee('Consultar pagos');

        $this->get(route('catalogo.index'))
            ->assertOk()
            ->assertSee(route('pagos.gestion'))
            ->assertSee('Gestión de pagos');
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
        app(PrestamoService::class)->registrar($cliente, $libro);

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
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Cliente Nuevo',
            'email' => 'nuevo@example.com',
            'role' => 'estudiante',
        ]);
        $cliente = User::query()->where('email', 'nuevo@example.com')->firstOrFail();
        $this->assertFalse(Hash::check('cualquier-password', $cliente->password));
    }

    private function crearLibro(string $titulo = 'El archivo'): Libro
    {
        $categoria = Categoria::firstOrCreate(['nombre' => 'Novela']);

        return Libro::create([
            'categoria_id' => $categoria->id,
            'titulo' => $titulo,
            'autor' => 'Ana Autor',
            'ejemplares_totales' => 1,
            'ejemplares_disponibles' => 1,
        ]);
    }
}
