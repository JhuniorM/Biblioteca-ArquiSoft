<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $libro->titulo }} | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <header class="bg-[#151311] px-6 py-5 text-[#f9f4ec] lg:px-16"><a href="{{ route('catalogo.index') }}" class="font-display text-xl tracking-wide text-[#e1bd7c]">Biblioteca ArquiSoft</a></header>
    <main class="mx-auto max-w-5xl px-6 py-14 lg:px-12">
        <a href="{{ route('catalogo.index') }}" class="text-xs font-bold uppercase tracking-[0.14em] text-[#9b7650]">&larr; Volver al cat&aacute;logo</a>
        <div class="mt-10 grid gap-10 md:grid-cols-[240px_1fr]">
            <div class="flex aspect-[2/3] items-center justify-center bg-[#315756] p-6 text-center font-display text-4xl text-white shadow-xl">{{ $libro->titulo }}</div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#bd9360]">{{ $libro->categoria?->nombre }}</p>
                <h1 class="mt-3 font-display text-5xl leading-tight text-[#302b26]">{{ $libro->titulo }}</h1>
                <p class="mt-4 text-lg text-[#716960]">{{ $libro->autor }}</p>
                <div class="mt-10 border-t border-[#e2d7ca] pt-5 text-sm text-[#716960]">
                    <p>ISBN: <span class="font-semibold text-[#302b26]">{{ $libro->isbn ?? 'No registrado' }}</span></p>
                    <p class="mt-3">Disponibilidad: <span class="font-semibold {{ $libro->ejemplares_disponibles > 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $libro->ejemplares_disponibles > 0 ? 'Disponible' : 'Agotado' }}</span></p>
                </div>
                @if ($libro->ejemplares_disponibles > 0)
                    <a href="{{ route('pagos.create', $libro) }}" class="mt-8 inline-flex rounded-full bg-[#24211f] px-7 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-white transition hover:bg-[#bd9360]">Alquilar ahora &rarr;</a>
                @else
                    <span class="mt-8 inline-flex rounded-full bg-[#d8cec2] px-7 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-[#716960]">Sin disponibilidad</span>
                @endif
            </div>
        </div>
    </main>
</body>
</html>