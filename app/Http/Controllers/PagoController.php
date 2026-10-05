<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Pago;
use App\Services\PrestamoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(private readonly PrestamoService $prestamos) {}

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
}
