<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PagoController extends Controller
{
    private const PRECIO_ALQUILER_CENTAVOS = 1000;

    public function create(Libro $libro): View
    {
        abort_if($libro->ejemplares_disponibles < 1, 409, 'Este libro no tiene ejemplares disponibles.');

        return view('pagos.checkout', [
            'libro' => $libro->load('categoria'),
            'precioCentavos' => self::PRECIO_ALQUILER_CENTAVOS,
        ]);
    }

    public function store(Request $request, Libro $libro): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'numero_tarjeta' => ['required', 'digits:16'],
            'vencimiento' => ['required', 'date_format:m/y'],
            'cvv' => ['required', 'digits:3,4'],
        ]);

        $prestamo = DB::transaction(function () use ($datos, $libro) {
            $libroBloqueado = Libro::query()->lockForUpdate()->findOrFail($libro->id);
            abort_if($libroBloqueado->ejemplares_disponibles < 1, 409, 'El libro se agotó mientras procesábamos el pago.');

            $usuario = User::query()->firstOrCreate(
                ['email' => $datos['email']],
                ['name' => $datos['nombre'], 'password' => bcrypt(str()->random(32))],
            );

            $prestamo = Prestamo::create([
                'libro_id' => $libroBloqueado->id,
                'user_id' => $usuario->id,
                'fecha_prestamo' => now()->toDateString(),
                'estado' => 'activo',
            ]);

            $libroBloqueado->decrement('ejemplares_disponibles');

            Pago::create([
                'prestamo_id' => $prestamo->id,
                'monto_centavos' => self::PRECIO_ALQUILER_CENTAVOS,
                'moneda' => 'PEN',
                'proveedor' => 'demo',
                'estado' => 'aprobado',
                'referencia' => 'ARQ-'.str()->upper(str()->random(12)),
            ]);

            return $prestamo;
        });

        return redirect()->route('pagos.success', $prestamo)->with('success', 'Pago aprobado y préstamo registrado.');
    }

    public function success(Prestamo $prestamo): View
    {
        return view('pagos.success', ['prestamo' => $prestamo->load(['libro', 'pago'])]);
    }
}