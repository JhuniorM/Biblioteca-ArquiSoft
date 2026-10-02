<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis multas | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <main class="mx-auto max-w-4xl px-6 py-12 lg:px-12">
        <div class="flex items-center justify-between gap-4"><a href="{{ route('catalogo.index') }}" class="font-display text-xl text-[#bd9360]">Biblioteca ArquiSoft</a><form action="{{ route('logout') }}" method="POST">@csrf<button class="text-xs font-bold uppercase tracking-[0.12em] text-[#716960]">Salir</button></form></div>
        <h1 class="mt-16 font-display text-5xl text-[#302b26]">Multas pendientes</h1>
        @if (session('success'))<p class="mt-5 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</p>@endif
        <div class="mt-8 divide-y divide-[#e2d7ca] border-y border-[#e2d7ca]">
            @forelse ($multas as $multa)
                <article class="flex flex-wrap items-center justify-between gap-5 py-6"><div><h2 class="font-display text-2xl text-[#302b26]">{{ $multa->prestamo->libro->titulo }}</h2><p class="mt-2 text-sm text-[#716960]">{{ $multa->dias_atraso }} días de atraso</p></div><div class="flex items-center gap-5"><strong class="text-[#302b26]">S/ {{ number_format($multa->monto_centavos / 100, 2) }}</strong><form action="{{ route('multas.pagar', $multa) }}" method="POST" class="flex items-center gap-2">@csrf<select name="metodo_pago" required class="rounded-lg border border-[#ded3c6] bg-white px-3 py-2 text-sm"><option value="tarjeta">Tarjeta</option><option value="yape">Yape</option><option value="plin">Plin</option></select><button class="rounded-full bg-[#24211f] px-5 py-3 text-xs font-bold uppercase tracking-[0.1em] text-white">Pagar</button></form></div></article>
            @empty
                <p class="py-16 text-center text-sm text-[#716960]">No tienes multas pendientes.</p>
            @endforelse
        </div>
    </main>
</body>
</html>
