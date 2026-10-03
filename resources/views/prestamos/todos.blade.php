<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Préstamos registrados | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <main class="mx-auto max-w-6xl px-6 py-12 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-4"><a href="{{ route('catalogo.index') }}" class="font-display text-xl text-[#bd9360]">Biblioteca ArquiSoft</a><div class="flex flex-wrap items-center gap-5 text-xs font-bold uppercase tracking-[0.12em] text-[#716960]"><a href="{{ route('catalogo.index') }}" class="rounded-full border border-[#bd9360] px-4 py-2 text-[#9b7650]">Volver al cat&aacute;logo</a><a href="{{ route('prestamos.clientes') }}">Clientes</a><form action="{{ route('logout') }}" method="POST">@csrf<button>Salir</button></form></div></div>
        <p class="mt-16 text-[10px] font-bold uppercase tracking-[0.3em] text-[#bd9360]">Control de circulación</p>
        <h1 class="mt-3 font-display text-5xl text-[#302b26]">Préstamos registrados</h1>
        <p class="mt-3 text-sm leading-6 text-[#716960]">Consulta quién tiene cada libro, cuándo lo recibió y cuándo debe devolverlo.</p>
        <form method="GET" action="{{ route('prestamos.todos') }}" class="mt-8 flex flex-wrap gap-3">
            <input name="buscar" value="{{ $buscar }}" placeholder="Buscar cliente por nombre o correo" class="min-w-[260px] flex-1 rounded-lg border border-[#ded3c6] bg-white px-4 py-3 text-sm">
            <select name="estado" class="rounded-lg border border-[#ded3c6] bg-white px-4 py-3 text-sm"><option value="">Todos los estados</option><option value="activo" @selected($estado === 'activo')>Activos</option><option value="devuelto" @selected($estado === 'devuelto')>Devueltos</option></select>
            <button class="rounded-full bg-[#24211f] px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-white">Filtrar</button>
        </form>
        <div class="mt-10 overflow-x-auto rounded-2xl bg-[#fbf8f3] shadow-sm">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="border-b border-[#e2d7ca] text-[10px] uppercase tracking-[0.15em] text-[#9b7650]"><tr><th class="px-5 py-4">Cliente</th><th class="px-5 py-4">Libro</th><th class="px-5 py-4">Préstamo</th><th class="px-5 py-4">Vencimiento</th><th class="px-5 py-4">Devolución</th><th class="px-5 py-4">Estado</th><th class="px-5 py-4">Devuelto por</th><th class="px-5 py-4">Acción</th></tr></thead>
                <tbody class="divide-y divide-[#e2d7ca]">
                    @forelse ($prestamos as $prestamo)
                        <tr><td class="px-5 py-5"><strong class="block text-[#302b26]">{{ $prestamo->usuario->name }}</strong><span class="text-xs text-[#716960]">{{ $prestamo->usuario->email }} · #{{ $prestamo->usuario->id }}</span></td><td class="px-5 py-5 font-display text-lg text-[#302b26]">{{ $prestamo->libro->titulo }}</td><td class="px-5 py-5 text-[#716960]">{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</td><td class="px-5 py-5 text-[#716960]">{{ $prestamo->fecha_vencimiento?->format('d/m/Y') ?? 'Pendiente' }}</td><td class="px-5 py-5 text-xs text-[#716960]">{{ $prestamo->fecha_devolucion?->format('d/m/Y') ?? 'Pendiente' }}</td><td class="px-5 py-5"><span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $prestamo->estado === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-[#e5ddd3] text-[#716960]' }}">{{ $prestamo->estado }}</span>@if ($prestamo->multa)<span class="mt-2 block text-xs {{ $prestamo->multa->estado === 'pendiente' ? 'text-rose-700' : 'text-emerald-700' }}">Multa {{ $prestamo->multa->estado }}</span>@endif</td><td class="px-5 py-5 text-xs text-[#716960]">{{ $prestamo->devueltoPor?->name ?? 'Pendiente' }}</td><td class="px-5 py-5">@if ($prestamo->estado === 'activo')<form action="{{ route('prestamos.devolver', $prestamo) }}" method="POST">@csrf<button class="rounded-full border border-[#bd9360] px-4 py-2 text-[10px] font-bold uppercase tracking-[0.1em] text-[#9b7650]">Devolver</button></form>@else<span class="text-xs text-[#8b8178]">Cerrado</span>@endif</td></tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-14 text-center text-[#716960]">No hay préstamos registrados con esos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $prestamos->links() }}</div>
    </main>
</body>
</html>