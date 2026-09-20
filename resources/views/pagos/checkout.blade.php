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
            <form action="{{ route('pagos.store', $libro) }}" method="POST" class="rounded-2xl bg-[#fbf8f3] p-6 shadow-sm sm:p-8">
                @csrf
                <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#bd9360]">Checkout seguro</p>
                <h1 class="mt-3 font-display text-4xl text-[#302b26]">Completa tu alquiler</h1>
                <p class="mt-3 text-sm text-[#716960]">Esta demostraci&oacute;n procesa el pago sin guardar datos de tarjeta.</p>
                @if ($errors->any())<div class="mt-5 rounded-lg bg-rose-50 p-4 text-sm text-rose-700">Revisa los datos ingresados.</div>@endif
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <label class="text-sm text-[#716960] sm:col-span-2">Nombre completo<input name="nombre" value="{{ old('nombre') }}" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                    <label class="text-sm text-[#716960] sm:col-span-2">Correo electr&oacute;nico<input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                    <label class="text-sm text-[#716960] sm:col-span-2">N&uacute;mero de tarjeta<input name="numero_tarjeta" inputmode="numeric" maxlength="16" placeholder="1234 5678 9012 3456" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                    <label class="text-sm text-[#716960]">Vencimiento<input name="vencimiento" placeholder="MM/YY" maxlength="5" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                    <label class="text-sm text-[#716960]">CVV<input name="cvv" inputmode="numeric" maxlength="4" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3 outline-none focus:border-[#bd9360]"></label>
                </div>
                <button type="submit" class="mt-8 w-full rounded-full bg-[#24211f] px-6 py-4 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-[#bd9360]">Pagar S/ {{ number_format($precioCentavos / 100, 2) }}</button>
            </form>
            <aside class="h-fit rounded-2xl bg-[#151311] p-6 text-[#f9f4ec]">
                <p class="text-[10px] uppercase tracking-[0.3em] text-[#d2a45e]">Resumen</p><h2 class="mt-4 font-display text-3xl">{{ $libro->titulo }}</h2><p class="mt-2 text-sm text-[#c8c0b8]">{{ $libro->autor }}</p><div class="mt-8 border-t border-white/15 pt-5 text-sm"><div class="flex justify-between"><span>Alquiler por 15 d&iacute;as</span><strong>S/ {{ number_format($precioCentavos / 100, 2) }}</strong></div></div><p class="mt-8 text-xs leading-5 text-[#c8c0b8]">&#128274; Pago protegido. Nunca almacenamos tu tarjeta.</p>
            </aside>
        </div>
    </main>
</body>
</html>