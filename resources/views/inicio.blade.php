<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <header class="bg-[#151311] text-[#f9f4ec]">
        <div class="mx-auto flex h-[72px] max-w-[1440px] items-center gap-8 px-6 lg:px-16">
            <a href="{{ url('/') }}" class="shrink-0 font-display text-xl tracking-wide text-[#e1bd7c]">Biblioteca ArquiSoft</a>
            <nav class="hidden flex-1 items-center justify-center gap-6 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#d7d0c7] lg:flex">
                <a href="{{ url('/') }}" class="border-b-2 border-[#d2a45e] pb-2 text-[#e1bd7c]">Inicio</a>
                <a href="{{ route('catalogo.index') }}" class="transition hover:text-[#e1bd7c]">Cat&aacute;logo</a>
                <a href="{{ route('categorias.index') }}" class="transition hover:text-[#e1bd7c]">Categor&iacute;as</a>
                <a href="{{ route('seccion', 'como-funciona') }}" class="transition hover:text-[#e1bd7c]">C&oacute;mo funciona</a>
                <a href="{{ route('seccion', 'suscripciones') }}" class="transition hover:text-[#e1bd7c]">Suscripciones</a>
                <a href="{{ route('seccion', 'nosotros') }}" class="transition hover:text-[#e1bd7c]">Nosotros</a>
                <a href="{{ route('seccion', 'contacto') }}" class="transition hover:text-[#e1bd7c]">Contacto</a>
            </nav>
            <a href="{{ route('catalogo.index') }}" class="ml-auto rounded-full border border-[#d2a45e] px-4 py-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-[#e1bd7c] transition hover:bg-[#d2a45e] hover:text-[#151311]">Explorar</a>
        </div>
    </header>

    <main>
        <section class="relative min-h-[570px] overflow-hidden bg-[#171411] text-white">
            <div class="absolute inset-0 bg-cover bg-center opacity-75" style="background-image: linear-gradient(90deg, rgba(15, 12, 10, .98) 0%, rgba(15, 12, 10, .8) 40%, rgba(15, 12, 10, .12) 100%), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=2200&q=85');"></div>
            <div class="relative mx-auto flex min-h-[570px] max-w-[1440px] items-center px-6 py-20 lg:px-16">
                <div class="max-w-xl">
                    <p class="mb-5 text-[10px] font-semibold uppercase tracking-[0.35em] text-[#d2a45e]">Libros que viajan contigo</p>
                    <h1 class="font-display text-6xl leading-[0.98] sm:text-7xl">Cada historia<br><em class="text-[#d2a45e]">merece ser le&iacute;da.</em></h1>
                    <p class="mt-7 max-w-md text-sm leading-7 text-[#ddd5ca]">Alquila libros, descubre nuevos mundos y disfruta de la lectura sin l&iacute;mites. Tu pr&oacute;xima gran historia te espera.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('catalogo.index') }}" class="rounded-full bg-[#d2a45e] px-7 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-[#211a14] transition hover:bg-[#edcf98]">Explorar libros &rarr;</a>
                        <a href="{{ route('seccion', 'como-funciona') }}" class="rounded-full border border-white/40 px-7 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-white transition hover:border-[#d2a45e] hover:text-[#d2a45e]">C&oacute;mo funciona &rarr;</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-[#e6dbce] bg-[#fbf8f3]">
            <div class="mx-auto grid max-w-[1200px] gap-6 px-6 py-6 sm:grid-cols-2 lg:grid-cols-4 lg:px-12">
                <div class="flex items-center gap-3 border-[#ded3c6] sm:border-r"><span class="text-2xl text-[#9b7650]">&#128218;</span><div><strong class="block text-xs">Gran variedad de libros</strong><span class="text-[10px] text-[#8b8178]">Miles de t&iacute;tulos disponibles</span></div></div>
                <div class="flex items-center gap-3 border-[#ded3c6] lg:border-r"><span class="text-2xl text-[#9b7650]">&#128666;</span><div><strong class="block text-xs">Env&iacute;o a todo el pa&iacute;s</strong><span class="text-[10px] text-[#8b8178]">Recibe donde est&eacute;s</span></div></div>
                <div class="flex items-center gap-3 border-[#ded3c6] sm:border-r"><span class="text-2xl text-[#9b7650]">&#10003;</span><div><strong class="block text-xs">Alquiler seguro</strong><span class="text-[10px] text-[#8b8178]">Proceso simple y confiable</span></div></div>
                <div class="flex items-center gap-3"><span class="text-2xl text-[#9b7650]">&#9742;</span><div><strong class="block text-xs">Soporte 24/7</strong><span class="text-[10px] text-[#8b8178]">Estamos para ayudarte</span></div></div>
            </div>
        </section>

        <section class="mx-auto max-w-[1200px] px-6 py-20 lg:px-12">
            <div class="text-center"><p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Cat&aacute;logo exclusivo</p><h2 class="mt-3 font-display text-4xl text-[#302b26]">Libros destacados</h2><div class="mx-auto mt-5 h-px w-9 bg-[#bd9360]"></div></div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($libros as $libro)
                    @php($initials = collect(explode(' ', trim($libro->titulo)))->filter()->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->join(''))
                    <article class="rounded-sm bg-white p-4 text-center shadow-[0_8px_20px_rgba(55,43,31,0.08)]">
                        <div class="mx-auto flex aspect-[2/3] max-w-[190px] flex-col justify-between bg-gradient-to-br from-[#315756] via-[#527775] to-[#d6a861] p-4 text-left text-white"><span class="text-[8px] uppercase tracking-[0.2em] text-white/75">Biblioteca ArquiSoft</span><span class="font-display text-4xl">{{ $initials }}</span><span><span class="block font-display text-lg leading-tight">{{ $libro->titulo }}</span><span class="mt-2 block text-[9px] text-white/80">{{ $libro->autor }}</span></span></div>
                        <h3 class="mt-4 font-display text-lg text-[#302b26]">{{ $libro->titulo }}</h3><p class="mt-1 text-[10px] text-[#8b8178]">{{ $libro->autor }}</p><a href="{{ route('catalogo.show', $libro) }}" class="mt-4 block rounded-sm bg-[#24211f] py-3 text-[10px] font-bold uppercase tracking-[0.12em] text-white transition hover:bg-[#bd9360]">Ver detalle</a>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</body>
</html>