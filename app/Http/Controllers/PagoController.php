<?php

namespace App\Http\Controllers;

use App\Models\Libro;
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
    private const PRECIO_ALQUILER_CENTAVOS = 1000;

    public function __construct(private readonly PrestamoService $prestamos) {}

    public function create(Request $request, Libro $libro): View
    {
        abort_if($libro->ejemplares_disponibles < 1, 409, 'Este libro no tiene ejemplares disponibles.');

        return view('pagos.checkout', [
            'libro' => $libro->load('categoria'),
            'precioCentavos' => self::PRECIO_ALQUILER_CENTAVOS,
            'clienteNombre' => $request->string('nombre')->toString(),
            'clienteEmail' => $request->string('email')->toString(),
        ]);
    }

    public function store(Request $request, Libro $libro): RedirectResponse
    {
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

        $usuario = $request->user();
        $prestamo = DB::transaction(function () use ($datos, $libro, $usuario) {
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
                'monto_centavos' => self::PRECIO_ALQUILER_CENTAVOS,
                'moneda' => 'PEN',
                'metodo_pago' => $datos['metodo_pago'],
                'ultimos_digitos' => isset($datos['numero_tarjeta']) ? substr($datos['numero_tarjeta'], -4) : null,
                'proveedor' => 'demo',
                'estado' => 'aprobado',
                'referencia' => 'ARQ-'.str()->upper(str()->random(12)),
            ]);

            return $prestamo;
        });

        if (! $request->user()) {
            Auth::login($prestamo->usuario);
            $request->session()->regenerate();
        }

        return redirect()->route('pagos.success', $prestamo)->with('success', 'Pago aprobado y préstamo registrado.');
    }

    public function success(Prestamo $prestamo): View
    {
        return view('pagos.success', ['prestamo' => $prestamo->load(['libro', 'pago'])]);
    }
}
