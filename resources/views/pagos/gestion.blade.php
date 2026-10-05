<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de pagos | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    @include('partials.navegacion', ['active' => ''])
    <main class="mx-auto max-w-6xl px-6 py-12 lg:px-12">
        <p class="mt-16 text-[10px] font-bold uppercase tracking-[0.3em] text-[#bd9360]">Control financiero</p>
        <h1 class="mt-3 font-display text-5xl text-[#302b26]">Pagos registrados</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#716960]">Consulta los pagos registrados por el personal y confirma las operaciones pendientes de revisión.</p>
        @if (session('success'))<p class="mt-5 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</p>@endif

        <div class="mt-10 overflow-x-auto border-y border-[#e2d7ca]">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="border-b border-[#e2d7ca] text-[10px] font-bold uppercase tracking-[0.14em] text-[#9b7650]">
                    <tr><th class="px-3 py-4">Referencia</th><th class="px-3 py-4">Cliente</th><th class="px-3 py-4">Préstamo</th><th class="px-3 py-4">Importe</th><th class="px-3 py-4">Registrado por</th><th class="px-3 py-4">Confirmación</th><th class="px-3 py-4"></th></tr>
                </thead>
                <tbody class="divide-y divide-[#e2d7ca]">
                    @forelse ($pagos as $pago)
                        <tr>
                            <td class="px-3 py-5 font-semibold text-[#302b26]">{{ $pago->referencia }}<p class="mt-1 text-xs font-normal text-[#716960]">{{ $pago->metodo_pago }}</p></td>
                            <td class="px-3 py-5">{{ $pago->prestamo->usuario->name }}<p class="mt-1 text-xs text-[#716960]">{{ $pago->prestamo->usuario->email }}</p></td>
                            <td class="px-3 py-5">{{ $pago->prestamo->libro->titulo }}</td>
                            <td class="px-3 py-5">S/ {{ number_format($pago->monto_centavos / 100, 2) }}</td>
                            <td class="px-3 py-5 text-[#716960]">{{ $pago->registradoPor?->name ?? 'Cliente / sistema' }}</td>
                            <td class="px-3 py-5">
                                @if ($pago->confirmado_en)
                                    <span class="text-emerald-700">Confirmado</span><p class="mt-1 text-xs text-[#716960]">{{ $pago->confirmadoPor?->name }} · {{ $pago->confirmado_en->format('d/m/Y H:i') }}</p>
                                @else
                                    <span class="text-amber-700">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-3 py-5 text-right">
                                @if ($pago->estado === 'pendiente' && in_array(auth()->user()->role, ['cajero', 'administrador'], true))
                                    <form action="{{ route('pagos.confirmar', $pago) }}" method="POST" class="flex items-center gap-2">@csrf<select name="metodo_pago" required class="rounded-lg border border-[#ded3c6] bg-white px-2 py-2 text-xs"><option value="yape">Yape</option><option value="plin">Plin</option><option value="tarjeta">Tarjeta</option></select><button class="rounded-full bg-[#24211f] px-4 py-2 text-[10px] font-bold uppercase tracking-[0.1em] text-white">Confirmar pago</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-12 text-center text-[#716960]">No hay pagos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $pagos->links() }}</div>
    </main>
</body>
</html>
