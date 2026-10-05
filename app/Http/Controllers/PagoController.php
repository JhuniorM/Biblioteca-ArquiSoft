<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Comprobante;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\PrestamoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(
        private readonly PrestamoService $prestamos,
        private readonly \App\Services\DemoPaymentGateway $pasarela,
    ) {}

    public function create(Request $request, Libro $libro): View
    {
        abort_if($libro->ejemplares_disponibles < 1, 409, 'Este libro no tiene ejemplares disponibles.');

        return view('pagos.checkout', [
            'libro' => $libro->load('categoria'),
            'precioCentavos' => config('biblioteca.precio_alquiler_centavos'),
            'clienteNombre' => $request->string('nombre')->toString(),
            'clienteEmail' => $request->string('email')->toString(),
        ]);
    }

    public function store(Request $request, Libro $libro): RedirectResponse
    {
        $registrador = $request->user();
        $requiereCliente = ! $request->user()
            || in_array($request->user()->role, ['bibliotecario', 'administrador'], true);

        $datos = $request->validate([
            'nombre' => [$requiereCliente ? 'required' : 'nullable', 'string', 'max:120'],
            'email' => [$requiereCliente ? 'required' : 'nullable', 'email', 'max:150'],
            'metodo_pago' => ['required', 'in:tarjeta,yape,plin'],
            'numero_tarjeta' => ['required_if:metodo_pago,tarjeta', 'nullable', 'digits:16'],
            'vencimiento' => ['required_if:metodo_pago,tarjeta', 'nullable', 'date_format:m/y'],
            'cvv' => ['required_if:metodo_pago,tarjeta', 'nullable', 'digits:3,4'],
        ]);

        $autorizacion = $this->pasarela->autorizar($datos);

        if (! $autorizacion['aprobado']) {
            return back()->withInput()->withErrors(['pago' => $autorizacion['mensaje']]);
        }

        $usuario = $request->user();
        $prestamo = DB::transaction(function () use ($datos, $libro, $usuario, $registrador) {
            $esPersonal = $usuario && in_array($usuario->role, ['bibliotecario', 'administrador'], true);

            if (! $usuario || ($esPersonal && $datos['email'])) {
                $usuario = User::query()->firstOrCreate(
                    ['email' => $datos['email']],
                    [
                        'name' => $datos['nombre'],
                        'password' => str()->random(32),
                        'role' => 'estudiante',
                    ],
                );
            }

            $prestamo = $this->prestamos->registrar($usuario, $libro);

            Pago::create([
                'prestamo_id' => $prestamo->id,
                'registrado_por_user_id' => $registrador?->id,
                'monto_centavos' => config('biblioteca.precio_alquiler_centavos'),
                'moneda' => 'PEN',
                'metodo_pago' => $datos['metodo_pago'],
                'ultimos_digitos' => isset($datos['numero_tarjeta']) ? substr($datos['numero_tarjeta'], -4) : null,
                'proveedor' => 'demo',
                'estado' => 'aprobado',
                'referencia' => 'ARQ-'.str()->upper(str()->random(12)),
            ]);

            Comprobante::create([
                'pago_id' => $prestamo->pago->id,
                'numero' => 'COMP-'.str()->upper(str()->random(12)),
                'emitido_en' => now(),
            ]);

            return $prestamo;
        });

        if (! $request->user()) {
            Auth::login($prestamo->usuario);
            $request->session()->regenerate();
        }

        return redirect()->route('pagos.success', $prestamo)->with('success', 'Pago aprobado y préstamo registrado.');
    }

    public function gestion(): View
    {
        return view('pagos.gestion', [
            'pagos' => Pago::query()
                ->with(['prestamo.usuario', 'prestamo.libro', 'comprobante', 'registradoPor', 'confirmadoPor'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function confirmar(Request $request, Pago $pago): RedirectResponse
    {
        $datos = $request->validate([
            'metodo_pago' => ['required', 'in:tarjeta,yape,plin'],
        ]);

        if ($pago->estado !== 'pendiente') {
            return back()->with('success', 'Este pago ya fue procesado.');
        }

        DB::transaction(function () use ($request, $pago, $datos) {
            $pago = Pago::query()->lockForUpdate()->findOrFail($pago->id);

            $this->prestamos->activarPendiente($pago->prestamo);
            $pago->update([
                'estado' => 'aprobado',
                'metodo_pago' => $datos['metodo_pago'],
                'confirmado_por_user_id' => $request->user()->id,
                'confirmado_en' => now(),
            ]);

            Comprobante::create([
                'pago_id' => $pago->id,
                'numero' => 'COMP-'.str()->upper(str()->random(12)),
                'emitido_en' => now(),
            ]);
        });

        return back()->with('success', 'Pago confirmado. El préstamo quedó activo y el libro puede entregarse.');
    }

    public function success(Prestamo $prestamo): View
    {
        return view('pagos.success', ['prestamo' => $prestamo->load(['libro', 'pago.comprobante'])]);
    }
}
