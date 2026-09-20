<header class="bg-[#151311] text-[#f9f4ec]">
    <div class="mx-auto flex h-[72px] max-w-[1440px] items-center gap-8 px-6 lg:px-16">
        <a href="{{ url('/') }}" class="shrink-0 font-display text-xl tracking-wide text-[#e1bd7c]">Biblioteca ArquiSoft</a>
        <nav class="hidden flex-1 items-center justify-center gap-6 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#d7d0c7] lg:flex">
            <a href="{{ url('/') }}" class="{{ $active === 'inicio' ? 'border-b-2 border-[#d2a45e] pb-2 text-[#e1bd7c]' : 'transition hover:text-[#e1bd7c]' }}">Inicio</a>
            <a href="{{ route('catalogo.index') }}" class="{{ $active === 'catalogo' ? 'border-b-2 border-[#d2a45e] pb-2 text-[#e1bd7c]' : 'transition hover:text-[#e1bd7c]' }}">Cat&aacute;logo</a>
            <a href="{{ route('categorias.index') }}" class="{{ $active === 'categorias' ? 'border-b-2 border-[#d2a45e] pb-2 text-[#e1bd7c]' : 'transition hover:text-[#e1bd7c]' }}">Categor&iacute;as</a>
            <a href="{{ route('seccion', 'como-funciona') }}" class="transition hover:text-[#e1bd7c]">C&oacute;mo funciona</a>
            <a href="{{ route('seccion', 'suscripciones') }}" class="transition hover:text-[#e1bd7c]">Suscripciones</a>
            <a href="{{ route('seccion', 'nosotros') }}" class="transition hover:text-[#e1bd7c]">Nosotros</a>
            <a href="{{ route('seccion', 'contacto') }}" class="transition hover:text-[#e1bd7c]">Contacto</a>
        </nav>
        <a href="{{ route('catalogo.index') }}" class="ml-auto rounded-full border border-[#d2a45e] px-4 py-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-[#e1bd7c] transition hover:bg-[#d2a45e] hover:text-[#151311]">Explorar</a>
    </div>
</header>