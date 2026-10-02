<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago seguro | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <header class="bg-[#151311] px-6 py-5 text-[#f9f4ec] lg:px-16"><a href="{{ route('catalogo.index') }}" class="font-display text-xl tracking-wide text-[#e1bd7c]">Biblioteca ArquiSoft</a></header>
    <main class="mx-auto max-w-5xl px-6 py-12 lg:px-12">
        <a href="{{ route('catalogo.show', $libro) }}" class="text-xs font-bold uppercase tracking-[0.14em] text-[#9b7650]">&larr; Volver al libro</a>
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
            <form id="formulario-pago" action="{{ route('pagos.store', $libro) }}" method="POST" class="rounded-2xl bg-[#fbf8f3] p-6 shadow-sm sm:p-8">
                @csrf
                <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#bd9360]">Checkout seguro</p>
                <h1 class="mt-3 font-display text-4xl text-[#302b26]">Registrar préstamo</h1>
                <p class="mt-3 text-sm text-[#716960]">Elige un medio de pago. Solo guardamos la referencia de la operaci&oacute;n.</p>
                @if ($errors->any())<div class="mt-5 rounded-lg bg-rose-50 p-4 text-sm text-rose-700">Revisa los datos ingresados.</div>@endif
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    @guest
                        <label class="text-sm text-[#716960] sm:col-span-2">Nombre del cliente<input name="nombre" value="{{ old('nombre', $clienteNombre) }}" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                        <label class="text-sm text-[#716960] sm:col-span-2">Correo del cliente<input name="email" type="email" value="{{ old('email', $clienteEmail) }}" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                    @elseif (in_array(auth()->user()->role, ['bibliotecario', 'administrador'], true) && $clienteNombre && $clienteEmail)
                        <input type="hidden" name="nombre" value="{{ $clienteNombre }}">
                        <input type="hidden" name="email" value="{{ $clienteEmail }}">
                        <p class="rounded-lg bg-[#f1e8dc] p-4 text-sm text-[#716960] sm:col-span-2">Préstamo para: <strong class="text-[#302b26]">{{ $clienteNombre }}</strong> · {{ $clienteEmail }}</p>
                    @endguest
                    <label class="text-sm text-[#716960] sm:col-span-2">Medio de pago<select name="metodo_pago" id="metodo_pago" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"><option value="tarjeta">Tarjeta</option><option value="yape">Yape</option><option value="plin">Plin</option></select></label>
                    <div id="datos-tarjeta" class="contents">
                        <label class="text-sm text-[#716960] sm:col-span-2">N&uacute;mero de tarjeta<input name="numero_tarjeta" inputmode="numeric" maxlength="16" placeholder="1234567890123456" class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                        <label class="text-sm text-[#716960]">Vencimiento<input name="vencimiento" placeholder="MM/YY" maxlength="5" class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                        <label class="text-sm text-[#716960]">CVV<input name="cvv" inputmode="numeric" maxlength="4" class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                    </div>
                    <p id="instrucciones-billetera" class="hidden text-sm leading-6 text-[#716960] sm:col-span-2">Confirma el pago desde tu billetera digital. La referencia de esta compra quedar&aacute; registrada en tu historial.</p>
                </div>
                <button type="button" id="abrir-modal-pago" class="mt-8 w-full rounded-full bg-[#24211f] px-6 py-4 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-[#bd9360]">Continuar al pago · S/ {{ number_format($precioCentavos / 100, 2) }}</button>
            </form>
            <aside class="h-fit rounded-2xl bg-[#151311] p-6 text-[#f9f4ec]">
                <p class="text-[10px] uppercase tracking-[0.3em] text-[#d2a45e]">Resumen</p><h2 class="mt-4 font-display text-3xl">{{ $libro->titulo }}</h2><p class="mt-2 text-sm text-[#c8c0b8]">{{ $libro->autor }}</p><div class="mt-8 border-t border-white/15 pt-5 text-sm"><div class="flex justify-between"><span>Alquiler por 15 d&iacute;as</span><strong>S/ {{ number_format($precioCentavos / 100, 2) }}</strong></div></div><p class="mt-8 text-xs leading-5 text-[#c8c0b8]">&#128274; Pago protegido. Nunca almacenamos tu tarjeta.</p>
            </aside>
        </div>
    </main>
    <div id="modal-pago" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#151311]/60 px-6" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-pago">
        <div class="w-full max-w-md rounded-2xl bg-[#fbf8f3] p-7 shadow-2xl">
            <div class="flex items-start justify-between gap-5"><div><p class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#bd9360]">Pago interbancario</p><h2 id="titulo-modal-pago" class="mt-2 font-display text-3xl text-[#302b26]">Confirma tu pago</h2></div><button type="button" id="cerrar-modal-pago" class="text-xl text-[#716960]" aria-label="Cerrar">&times;</button></div>
            <div class="mt-6 rounded-xl bg-[#151311] p-5 text-[#f9f4ec]"><p class="text-xs text-[#c8c0b8]">Método seleccionado</p><p id="modal-metodo" class="mt-1 font-display text-2xl text-[#e1bd7c]">Tarjeta</p><div class="mt-5 flex items-center gap-4 border-t border-white/15 pt-4"><div class="grid h-16 w-16 grid-cols-5 gap-1 rounded bg-white p-2" aria-label="Imagen interbancaria de demostración">@for ($i = 0; $i < 25; $i++)<span class="rounded-sm {{ in_array($i, [0, 1, 5, 6, 8, 10, 12, 14, 16, 18, 20, 22, 23, 24], true) ? 'bg-[#151311]' : 'bg-[#e1bd7c]' }}"></span>@endfor</div><div><p class="text-xs text-[#c8c0b8]">Referencia interbancaria</p><strong class="text-sm tracking-[0.12em]">ARQ-DEMO-{{ str()->upper(str()->random(6)) }}</strong></div></div></div>
            <p class="mt-5 text-sm leading-6 text-[#716960]">Esta pantalla simula la confirmación de la entidad bancaria. Al confirmar, se registrará el préstamo y el pago en la base de datos.</p>
            <button type="button" id="confirmar-pago" class="mt-6 w-full rounded-full bg-[#24211f] px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-white">Confirmar y registrar pago</button>
        </div>
    </div>
    <script>
        const metodo = document.querySelector('#metodo_pago');
        const tarjeta = document.querySelector('#datos-tarjeta');
        const instrucciones = document.querySelector('#instrucciones-billetera');
        const formularioPago = document.querySelector('#formulario-pago');
        const modalPago = document.querySelector('#modal-pago');
        const modalMetodo = document.querySelector('#modal-metodo');
        const actualizarMetodo = () => {
            const esTarjeta = metodo.value === 'tarjeta';
            tarjeta.classList.toggle('hidden', !esTarjeta);
            instrucciones.classList.toggle('hidden', esTarjeta);
            tarjeta.querySelectorAll('input').forEach((input) => { input.required = esTarjeta; });
        };
        metodo.addEventListener('change', actualizarMetodo);
        actualizarMetodo();
        document.querySelector('#abrir-modal-pago').addEventListener('click', () => {
            if (!formularioPago.reportValidity()) return;
            modalMetodo.textContent = metodo.options[metodo.selectedIndex].text;
            modalPago.classList.remove('hidden');
            modalPago.classList.add('flex');
        });
        document.querySelector('#cerrar-modal-pago').addEventListener('click', () => {
            modalPago.classList.add('hidden');
            modalPago.classList.remove('flex');
        });
        document.querySelector('#confirmar-pago').addEventListener('click', () => formularioPago.submit());
    </script>
</body>
</html>