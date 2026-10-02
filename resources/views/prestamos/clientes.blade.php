<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de clientes | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <main class="mx-auto max-w-6xl px-6 py-12 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-4"><a href="{{ route('catalogo.index') }}" class="font-display text-xl text-[#bd9360]">Biblioteca ArquiSoft</a><div class="flex gap-5 text-xs font-bold uppercase tracking-[0.12em] text-[#716960]"><a href="{{ route('prestamos.index') }}">Mis préstamos</a><form action="{{ route('logout') }}" method="POST">@csrf<button>Salir</button></form></div></div>
        <p class="mt-16 text-[10px] font-bold uppercase tracking-[0.3em] text-[#bd9360]">Panel de personal</p>
        <h1 class="mt-3 font-display text-5xl text-[#302b26]">Clientes e historiales</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#716960]">Busca por nombre o correo para identificar a cada cliente y consultar solamente sus préstamos y multas.</p>
        @if (session('success'))<p class="mt-5 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</p>@endif
        @if ($errors->any())<p class="mt-5 rounded-lg bg-rose-50 p-4 text-sm text-rose-700">{{ $errors->first() }}</p>@endif

        <form method="GET" action="{{ route('prestamos.clientes') }}" class="mt-8 flex max-w-2xl gap-3">
            <input name="buscar" value="{{ $buscar }}" placeholder="Nombre o correo del cliente" class="min-w-0 flex-1 rounded-lg border border-[#ded3c6] bg-white px-4 py-3 text-sm">
            <button class="rounded-full bg-[#24211f] px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-white">Buscar</button>
        </form>

        <details class="mt-6 max-w-2xl rounded-2xl bg-[#fbf8f3] p-5 shadow-sm">
            <summary class="cursor-pointer text-xs font-bold uppercase tracking-[0.14em] text-[#9b7650]">Registrar nuevo cliente</summary>
            <form action="{{ route('prestamos.clientes.crear') }}" method="POST" class="mt-5 grid gap-3 sm:grid-cols-2">
                @csrf
                <input name="name" placeholder="Nombre completo" required class="rounded-lg border border-[#ded3c6] bg-white px-4 py-3 text-sm">
                <input name="email" type="email" placeholder="Correo electrónico" required class="rounded-lg border border-[#ded3c6] bg-white px-4 py-3 text-sm">
                <button class="rounded-full bg-[#24211f] px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-white sm:col-span-2">Guardar cliente y continuar</button>
            </form>
        </details>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_1.25fr]">
            <section class="divide-y divide-[#e2d7ca] border-y border-[#e2d7ca]">
                @forelse ($clientes as $item)
                    <a href="{{ route('prestamos.clientes', ['cliente' => $item->id, 'buscar' => $buscar]) }}" class="block px-2 py-5 transition hover:bg-white">
                        <div class="flex items-start justify-between gap-4"><div><h2 class="font-display text-xl text-[#302b26]">{{ $item->name }}</h2><p class="mt-1 text-xs text-[#716960]">{{ $item->email }} · Cliente #{{ $item->id }}</p></div><span class="rounded-full bg-[#e5ddd3] px-3 py-1 text-[10px] font-bold text-[#716960]">{{ $item->prestamos_activos }} activos</span></div>
                        <p class="mt-3 text-xs text-[#8b8178]">{{ $item->prestamos_count }} préstamos registrados</p>
                    </a>
                @empty
                    <p class="py-12 text-center text-sm text-[#716960]">No se encontraron clientes.</p>
                @endforelse
            </section>

            <section class="rounded-2xl bg-[#fbf8f3] p-6 shadow-sm sm:p-8">
                @if ($cliente)
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#bd9360]">Historial del cliente #{{ $cliente->id }}</p>
                    <h2 class="mt-3 font-display text-3xl text-[#302b26]">{{ $cliente->name }}</h2>
                    <p class="mt-1 text-sm text-[#716960]">{{ $cliente->email }} · Cliente #{{ $cliente->id }}</p>
                    <form action="{{ route('prestamos.clientes.registrar') }}" method="POST" class="mt-6 border-y border-[#e2d7ca] py-5">
                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#bd9360]">Nuevo préstamo presencial</p>
                        <label class="mt-3 block text-sm text-[#716960]">Libro<select name="libro_id" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-3 py-3 text-sm"><option value="">Seleccionar libro</option>@foreach ($librosDisponibles as $libro)<option value="{{ $libro->id }}">{{ $libro->titulo }} · {{ $libro->ejemplares_disponibles }} disponibles</option>@endforeach</select></label>
                        <button class="mt-4 rounded-full bg-[#24211f] px-5 py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-white">Registrar préstamo</button>
                    </form>
                    <div class="mt-6 divide-y divide-[#e2d7ca] border-y border-[#e2d7ca]">
                        @forelse ($cliente->prestamos->sortByDesc('fecha_prestamo') as $prestamo)
                            <article class="py-5"><div class="flex items-start justify-between gap-4"><div><h3 class="font-display text-xl text-[#302b26]">{{ $prestamo->libro->titulo }}</h3><p class="mt-1 text-xs text-[#716960]">{{ $prestamo->fecha_prestamo->format('d/m/Y') }} · vence {{ $prestamo->fecha_vencimiento?->format('d/m/Y') ?? 'sin fecha' }}</p></div><span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $prestamo->estado === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-[#e5ddd3] text-[#716960]' }}">{{ $prestamo->estado }}</span></div>@if ($prestamo->multa)<p class="mt-2 text-xs {{ $prestamo->multa->estado === 'pendiente' ? 'text-rose-700' : 'text-emerald-700' }}">Multa: S/ {{ number_format($prestamo->multa->monto_centavos / 100, 2) }} · {{ $prestamo->multa->estado }}</p>@endif @if ($prestamo->estado === 'activo')<form action="{{ route('prestamos.devolver', $prestamo) }}" method="POST" class="mt-3">@csrf<button class="rounded-full border border-[#bd9360] px-4 py-2 text-[10px] font-bold uppercase tracking-[0.1em] text-[#9b7650]">Registrar devolución</button></form>@endif</article>
                        @empty
                            <p class="py-10 text-sm text-[#716960]">Este cliente aún no tiene préstamos.</p>
                        @endforelse
                    </div>
                @else
                    <div class="flex min-h-64 items-center justify-center text-center"><p class="max-w-sm text-sm leading-6 text-[#716960]">Selecciona un cliente para ver su historial completo, estado de préstamos y multas.</p></div>
                @endif
            </section>
        </div>
        <div class="mt-6">{{ $clientes->links() }}</div>
    </main>
</body>
</html>