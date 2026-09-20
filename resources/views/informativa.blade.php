<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ str_replace('-', ' ', ucfirst($seccion)) }} | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    @include('partials.navegacion', ['active' => $seccion])
    <main class="mx-auto max-w-[1000px] px-6 py-20 lg:px-12">
        @if ($seccion === 'como-funciona')
            <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Paso a paso</p>
            <h1 class="mt-3 font-display text-5xl text-[#302b26]">&iquest;C&oacute;mo funciona?</h1>
            <div class="mt-12 grid gap-5 md:grid-cols-3">@foreach (['Explora el cat&aacute;logo', 'Elige tu libro', 'Solicita el pr&eacute;stamo'] as $index => $titulo)<article class="rounded-xl bg-[#fbf8f3] p-6 shadow-sm"><span class="font-display text-3xl text-[#bd9360]">0{{ $index + 1 }}</span><h2 class="mt-8 font-display text-2xl">{!! $titulo !!}</h2><p class="mt-3 text-sm leading-6 text-[#716960]">Consulta la colecci&oacute;n, verifica la disponibilidad y contin&uacute;a el proceso en biblioteca.</p></article>@endforeach</div>
        @elseif ($seccion === 'suscripciones')
            <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Mantente cerca de las historias</p><h1 class="mt-3 font-display text-5xl text-[#302b26]">Suscripciones</h1><p class="mt-6 max-w-xl text-base leading-7 text-[#716960]">Recibe novedades de la colecci&oacute;n y recomendaciones de lectura de Biblioteca ArquiSoft.</p>
        @elseif ($seccion === 'nosotros')
            <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Sobre nosotros</p><h1 class="mt-3 font-display text-5xl text-[#302b26]">Historias que conectan.</h1><p class="mt-6 max-w-2xl text-base leading-7 text-[#716960]">Somos una biblioteca digital creada para acercar grandes historias a todos los lectores, de forma sencilla, r&aacute;pida y segura.</p>
        @else
            <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Contacto</p><h1 class="mt-3 font-display text-5xl text-[#302b26]">Estamos para ayudarte.</h1><p class="mt-6 text-base leading-7 text-[#716960]">Biblioteca ArquiSoft<br>contacto@arquisoft.local<br>Atenci&oacute;n a lectores y comunidad universitaria.</p>
        @endif
        <a href="{{ route('catalogo.index') }}" class="mt-12 inline-block rounded-full bg-[#24211f] px-6 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-white">Volver al cat&aacute;logo</a>
    </main>
</body>
</html>