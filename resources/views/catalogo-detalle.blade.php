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
                    <button type="button" id="abrir-modal-cliente" class="mt-8 inline-flex rounded-full bg-[#24211f] px-7 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-white transition hover:bg-[#bd9360]">Solicitar préstamo &rarr;</button>
                @else
                    <span class="mt-8 inline-flex rounded-full bg-[#d8cec2] px-7 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-[#716960]">Sin disponibilidad</span>
                @endif
            </div>
        </div>
    </main>
    <div id="modal-cliente" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#151311]/60 px-6" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-cliente">
        <div class="w-full max-w-md rounded-2xl bg-[#fbf8f3] p-7 shadow-2xl">
            <div class="flex items-start justify-between gap-5"><div><p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#bd9360]">Nuevo cliente</p><h2 id="titulo-modal-cliente" class="mt-2 font-display text-3xl text-[#302b26]">¿Para quién es el préstamo?</h2></div><button type="button" id="cerrar-modal-cliente" class="text-xl text-[#716960]" aria-label="Cerrar">&times;</button></div>
            <p class="mt-3 text-sm leading-6 text-[#716960]">Ingresa los datos de la persona. En el siguiente paso se confirmará el método de pago.</p>
            <form action="{{ route('pagos.create', $libro) }}" method="GET" class="mt-6 grid gap-4">
                <label class="text-sm text-[#716960]">Nombre completo<input name="nombre" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3"></label>
                <label class="text-sm text-[#716960]">Correo electrónico<input name="email" type="email" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3"></label>
                <button class="mt-2 rounded-full bg-[#24211f] px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-white">Continuar al pago</button>
            </form>
        </div>
    </div>
    <script>
        const modalCliente = document.querySelector('#modal-cliente');
        const abrirModalCliente = document.querySelector('#abrir-modal-cliente');
        const cerrarModalCliente = document.querySelector('#cerrar-modal-cliente');
        const cambiarModal = (visible) => {
            modalCliente.classList.toggle('hidden', !visible);
            modalCliente.classList.toggle('flex', visible);
        };
        abrirModalCliente?.addEventListener('click', () => cambiarModal(true));
        cerrarModalCliente?.addEventListener('click', () => cambiarModal(false));
        modalCliente?.addEventListener('click', (event) => { if (event.target === modalCliente) cambiarModal(false); });
    </script>
</body>
</html>