<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cat&aacute;logo | {{ config('app.name', 'Biblioteca ArquiSoft') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <header class="bg-[#151311] text-[#f9f4ec]">
        <div class="mx-auto flex h-[72px] max-w-[1440px] items-center gap-8 px-6 lg:px-16">
            <a href="{{ url('/') }}" class="shrink-0 font-display text-xl tracking-wide text-[#e1bd7c]">Biblioteca ArquiSoft</a>
            <nav class="hidden flex-1 items-center justify-center gap-6 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#d7d0c7] lg:flex">
                <a href="{{ url('/') }}" class="transition hover:text-[#e1bd7c]">Inicio</a>
                <a href="{{ route('catalogo.index') }}" class="border-b-2 border-[#d2a45e] pb-2 text-[#e1bd7c]">Cat&aacute;logo</a>
                <a href="{{ route('categorias.index') }}" class="transition hover:text-[#e1bd7c]">Categor&iacute;as</a>
                <a href="{{ route('seccion', 'como-funciona') }}" class="transition hover:text-[#e1bd7c]">C&oacute;mo funciona</a>
                <a href="{{ route('seccion', 'suscripciones') }}" class="transition hover:text-[#e1bd7c]">Suscripciones</a>
                <a href="{{ route('seccion', 'nosotros') }}" class="transition hover:text-[#e1bd7c]">Nosotros</a>
                <a href="{{ route('seccion', 'contacto') }}" class="transition hover:text-[#e1bd7c]">Contacto</a>
            </nav>
            <form action="{{ url()->current() }}" method="GET" class="ml-auto flex w-full max-w-[235px] items-center rounded-full bg-[#fbf8f3] px-3 py-2 text-[#777069]">
                <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                <input name="titulo" value="{{ $titulo }}" type="search" placeholder="Buscar libros, autores..." class="min-w-0 flex-1 bg-transparent text-xs outline-none placeholder:text-[#928a82]">
                <input type="hidden" name="autor" value="{{ $autor }}">
                <input type="hidden" name="categoria_id" value="{{ $categoriaId }}">
                <input type="hidden" name="disponibilidad" value="{{ $disponibilidad }}">
                @if ($titulo)<a href="{{ url()->current() }}" aria-label="Limpiar b&uacute;squeda" class="ml-2 text-base leading-none text-[#a39b92]">&times;</a>@endif
            </form>
            <div class="hidden items-center gap-4 text-[#d7d0c7] sm:flex"><span aria-label="Favoritos" class="text-lg">&#9825;</span><span aria-label="Carrito" class="relative text-lg">&#9822;<sup class="absolute -right-2 -top-1 text-[9px] text-[#e1bd7c]">0</sup></span></div>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden border-b border-[#ebe1d4] bg-[#fbf8f3]">
            <div class="mx-auto flex min-h-[290px] max-w-[1440px] items-center justify-between gap-8 px-6 py-14 lg:px-16">
                <div>
                    <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Un mundo de historias</p>
                    <h1 class="font-display text-5xl leading-[1.02] text-[#302b26] sm:text-6xl">Cat&aacute;logo de libros</h1>
                    <p class="mt-5 max-w-xl text-sm text-[#716960]">Explora nuestra colecci&oacute;n de libros y audiolibros. Grandes historias siempre a tu alcance.</p>
                </div>
                <p class="hidden max-w-[180px] font-display text-2xl leading-tight text-[#8c847c] lg:block">&ldquo;Un libro siempre es una buena idea.&rdquo;</p>
            </div>
            <div class="absolute bottom-[-23px] left-1/2 flex -translate-x-1/2 items-center rounded-full bg-[#ede9e5] p-1 shadow-[0_10px_28px_rgba(55,43,31,0.12)]">
                <a href="#libros" class="rounded-full bg-[#24211f] px-6 py-3 text-center text-[11px] font-medium text-white shadow-sm">&#128214; Libros de lectura<span class="block text-[8px] text-[#c8c0b8]">Ebooks y libros f&iacute;sicos</span></a>
                <a href="#audiolibros" class="px-6 py-2 text-center text-[11px] font-medium text-[#726b64]">&#127911; Audiolibros<span class="block text-[8px]">Escucha donde quieras</span></a>
                <a href="#favoritos" class="hidden px-6 py-2 text-center text-[11px] font-medium text-[#726b64] sm:block">&#9825; Favoritos<span class="block text-[8px]">Tus libros guardados</span></a>
            </div>
        </section>

        <section id="libros" class="mx-auto max-w-[1440px] px-6 pb-16 pt-20 lg:px-12">
            <div id="categorias" class="mb-8 flex flex-wrap items-end justify-between gap-5">
                <div><h2 class="font-display text-3xl text-[#302b26]">Libros de lectura <span class="text-xl">&rsaquo;</span></h2><p class="mt-2 text-xs text-[#8b8178]">{{ $libros->total() }} historias en nuestra colecci&oacute;n</p></div>
                <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center justify-end gap-2">
                    <input type="hidden" name="titulo" value="{{ $titulo }}">
                    <label for="autor" class="sr-only">Filtrar por autor</label>
                    <input id="autor" name="autor" value="{{ $autor }}" type="search" placeholder="Autor" class="w-32 rounded-full border border-[#ded3c6] bg-transparent px-4 py-2 text-xs text-[#6d645b] outline-none placeholder:text-[#9b9086] focus:border-[#bd9360]">
                    <label for="categoria_id" class="sr-only">Filtrar por categor&iacute;a</label>
                    <select id="categoria_id" name="categoria_id" class="rounded-full border border-[#ded3c6] bg-transparent px-4 py-2 text-xs text-[#6d645b] outline-none focus:border-[#bd9360]"><option value="">Todas las categor&iacute;as</option>@foreach ($categorias as $categoria)<option value="{{ $categoria->id }}" @selected((string) $categoriaId === (string) $categoria->id)>{{ $categoria->nombre }}</option>@endforeach</select>
                    <label for="disponibilidad" class="sr-only">Filtrar por disponibilidad</label>
                    <select id="disponibilidad" name="disponibilidad" class="rounded-full border border-[#ded3c6] bg-transparent px-4 py-2 text-xs text-[#6d645b] outline-none focus:border-[#bd9360]"><option value="">Disponibilidad</option><option value="disponible" @selected($disponibilidad === 'disponible')>Disponible</option><option value="agotado" @selected($disponibilidad === 'agotado')>Agotado</option></select>
                    <button type="submit" class="rounded-full bg-[#24211f] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#bd9360]">Filtrar</button>
                </form>
            </div>

            @if ($libros->isNotEmpty())
                <div class="grid grid-cols-2 gap-x-5 gap-y-12 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8">
                    @foreach ($libros as $libro)
                        @php
                            $disponibles = (int) $libro->ejemplares_disponibles;
                            $totales = (int) $libro->ejemplares_totales;
                            $initials = collect(explode(' ', trim($libro->titulo)))->filter()->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->join('');
                            $coverStyles = ['from-[#1f4d50] via-[#4d7772] to-[#d1a35e]', 'from-[#243c5f] via-[#607495] to-[#d6b478]', 'from-[#593a35] via-[#9d6a51] to-[#dfbd83]', 'from-[#303c35] via-[#687c62] to-[#c6a26b]'];
                            $coverStyle = $coverStyles[$libro->id % count($coverStyles)];
                        @endphp
                        <article class="group min-w-0">
                            <div class="relative mb-4 aspect-[2/3] overflow-hidden rounded-[3px] bg-[#3c5c5b] shadow-[5px_8px_13px_rgba(50,37,27,0.22)] transition duration-300 group-hover:-translate-y-2 group-hover:shadow-[8px_14px_20px_rgba(50,37,27,0.28)]">
                                <div role="img" aria-label="Portada de {{ $libro->titulo }}" class="flex h-full flex-col justify-between bg-gradient-to-br {{ $coverStyle }} p-4 text-white"><span class="text-[8px] uppercase tracking-[0.2em] text-white/75">Biblioteca ArquiSoft</span><span class="font-display text-4xl leading-none">{{ $initials }}</span><span><span class="block font-display text-lg leading-tight">{{ $libro->titulo }}</span><span class="mt-2 block text-[9px] text-white/80">{{ $libro->autor }}</span></span></div>
                                <span class="absolute right-2 top-2 rounded-full bg-[#151311]/80 px-2 py-1 text-[9px] font-semibold text-white">{{ $disponibles > 0 ? 'Disponible' : 'Agotado' }}</span>
                            </div>
                            <span class="mb-2 inline-block text-[9px] font-semibold uppercase tracking-[0.12em] text-[#b09169]">{{ $libro->categoria?->nombre ?? 'Sin categor&iacute;a' }}</span>
                            <h3 class="line-clamp-2 text-sm font-semibold leading-tight text-[#302b26]">{{ $libro->titulo }}</h3><p class="mt-1 line-clamp-1 text-[11px] text-[#90867d]">{{ $libro->autor }}</p><p class="mt-2 text-[10px] text-[#b09169]">{{ $disponibles }} de {{ $totales }} disponibles</p>
                            <a href="{{ route('catalogo.show', $libro) }}" class="mt-3 inline-flex items-center text-[10px] font-bold uppercase tracking-[0.12em] text-[#302b26] hover:text-[#bd9360]">Ver detalle <span class="ml-1 text-sm">&rarr;</span></a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-12">{{ $libros->links() }}</div>
            @else
                <div class="border-y border-[#e6dbce] py-20 text-center"><p class="font-display text-3xl text-[#302b26]">No encontramos libros</p><p class="mt-3 text-sm text-[#8b8178]">Prueba con otro t&iacute;tulo o selecciona una categor&iacute;a diferente.</p></div>
            @endif
        </section>

        <section id="como-funciona" class="border-t border-[#e6dbce] bg-[#f2eadf] px-6 py-16 lg:px-12">
            <div class="mx-auto max-w-[1200px] text-center">
                <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#bd9360]">Paso a paso</p>
                <h2 class="mt-3 font-display text-3xl text-[#302b26]">&iquest;C&oacute;mo funciona?</h2>
                <div class="mt-8 grid gap-4 text-left md:grid-cols-3">
                    <div class="rounded-xl bg-[#fbf8f3] p-5"><span class="font-display text-2xl text-[#bd9360]">01</span><h3 class="mt-2 font-semibold text-[#302b26]">Explora</h3><p class="mt-1 text-sm text-[#716960]">Busca por t&iacute;tulo, autor, categor&iacute;a o disponibilidad.</p></div>
                    <div class="rounded-xl bg-[#fbf8f3] p-5"><span class="font-display text-2xl text-[#bd9360]">02</span><h3 class="mt-2 font-semibold text-[#302b26]">Elige</h3><p class="mt-1 text-sm text-[#716960]">Revisa el detalle del libro y sus ejemplares disponibles.</p></div>
                    <div class="rounded-xl bg-[#fbf8f3] p-5"><span class="font-display text-2xl text-[#bd9360]">03</span><h3 class="mt-2 font-semibold text-[#302b26]">Solicita</h3><p class="mt-1 text-sm text-[#716960]">Contin&uacute;a con el proceso de pr&eacute;stamo en biblioteca.</p></div>
                </div>
            </div>
        </section>

        <section id="suscripciones" class="border-t border-[#e6dbce] bg-[#151311] px-6 py-14 text-center text-[#f9f4ec] lg:px-12">
            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#d2a45e]">Suscripciones</p>
            <h2 class="mt-3 font-display text-3xl">Siempre hay una nueva historia esper&aacute;ndote</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm text-[#c8c0b8]">Recibe novedades de la colecci&oacute;n y recomendaciones de lectura.</p>
        </section>

        <section id="nosotros" class="border-t border-[#e6dbce] bg-[#fbf8f3] px-6 py-14 lg:px-12">
            <div class="mx-auto max-w-[1200px] text-center"><p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#bd9360]">Sobre Biblioteca ArquiSoft</p><h2 class="mt-3 font-display text-3xl text-[#302b26]">Historias que conectan, lectores que descubren.</h2><p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-[#716960]">Una biblioteca digital para consultar libros, verificar disponibilidad y facilitar el pr&eacute;stamo de materiales.</p></div>
        </section>

        <section id="contacto" class="border-t border-[#e6dbce] bg-[#f2eadf] px-6 py-14 lg:px-12">
            <div class="mx-auto grid max-w-[1200px] gap-8 md:grid-cols-2"><div><p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-[#bd9360]">Contacto</p><h2 class="mt-3 font-display text-3xl text-[#302b26]">Estamos para ayudarte.</h2></div><div class="text-sm leading-7 text-[#716960]"><p>Biblioteca ArquiSoft</p><p>Atenci&oacute;n a lectores y comunidad universitaria</p><p>contacto@arquisoft.local</p></div></div>
        </section>
    </main>
</body>
</html>