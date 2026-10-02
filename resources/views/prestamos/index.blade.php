<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis préstamos | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <main class="mx-auto max-w-5xl px-6 py-12 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-4"><a href="{{ route('catalogo.index') }}" class="font-display text-xl text-[#bd9360]">Biblioteca ArquiSoft</a><div class="flex gap-5 text-xs font-bold uppercase tracking-[0.12em] text-[#716960]"><a href="{{ route('multas.index') }}">Multas</a><form action="{{ route('logout') }}" method="POST">@csrf<button>Salir</button></form></div></div>
        <h1 class="mt-16 font-display text-5xl text-[#302b26]">Mis préstamos</h1>
        <p class="mt-3 text-sm text-[#716960]">Consulta tus libros, fechas de devolución y comprobantes.</p>
        <div class="mt-8 divide-y divide-[#e2d7ca] border-y border-[#e2d7ca]">
            @forelse ($prestamos as $prestamo)
                <article class="flex flex-wrap items-center justify-between gap-5 py-6"><div><h2 class="font-display text-2xl text-[#302b26]">{{ $prestamo->libro->titulo }}</h2><p class="mt-2 text-sm text-[#716960]">Prestado: {{ $prestamo->fecha_prestamo->format('d/m/Y') }} · Vence: {{ $prestamo->fecha_vencimiento?->format('d/m/Y') ?? 'Pendiente' }}</p></div><span class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-[0.1em] {{ $prestamo->estado === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-[#e5ddd3] text-[#716960]' }}">{{ $prestamo->estado }}</span></article>
            @empty
                <p class="py-16 text-center text-sm text-[#716960]">Aún no tienes préstamos registrados.</p>
            @endforelse
        </div>
    </main>
</body>
</html>