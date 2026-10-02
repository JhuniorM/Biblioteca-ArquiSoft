<?php

namespace App\Http\Controllers;

use App\Models\Multa;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MultaController extends Controller
{
    public function index(Request $request): View
    {
        $multas = $request->user()->prestamos()
            ->with(['libro', 'multa'])
            ->whereHas('multa', fn ($query) => $query->where('estado', 'pendiente'))
            ->latest('fecha_devolucion')
            ->get()
            ->pluck('multa');

        return view('multas.index', ['multas' => $multas]);
    }

    public function pagar(Request $request, Multa $multa): RedirectResponse
    {
        abort_unless(
            $multa->prestamo->user_id === $request->user()->id || in_array($request->user()->role, ['cajero', 'bibliotecario', 'administrador'], true),
            403,
        );

        $datos = $request->validate(['metodo_pago' => ['required', 'in:tarjeta,yape,plin']]);

        DB::transaction(function () use ($multa, $datos) {
            $multa = Multa::query()->lockForUpdate()->findOrFail($multa->id);

            if ($multa->estado === 'pendiente') {
                $multa->update(['estado' => 'pagada', 'pagada_en' => now()]);
                Pago::create([
                    'prestamo_id' => $multa->prestamo_id,
                    'multa_id' => $multa->id,
                    'monto_centavos' => $multa->monto_centavos,
                    'moneda' => 'PEN',
                    'metodo_pago' => $datos['metodo_pago'],
                    'proveedor' => 'demo',
                    'estado' => 'aprobado',
                    'referencia' => 'ARQ-'.str()->upper(str()->random(12)),
                ]);
            }
        });

        return back()->with('success', 'Multa pagada y confirmada.');
    }
}
