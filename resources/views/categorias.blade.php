<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categor&iacute;as | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    @include('partials.navegacion', ['active' => 'categorias'])
    <main class="mx-auto max-w-[1200px] px-6 py-16 lg:px-12">
        <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-[#bd9360]">Explora por tema</p>
        <h1 class="mt-3 font-display text-5xl text-[#302b26]">Categor&iacute;as</h1>
        <p class="mt-4 max-w-xl text-sm leading-7 text-[#716960]">Encuentra una colecci&oacute;n que se adapte a tu pr&oacute;xima lectura.</p>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categorias as $categoria)
                <a href="{{ route('catalogo.index', ['categoria_id' => $categoria->id]) }}" class="group rounded-xl border border-[#e4d9cc] bg-[#fbf8f3] p-6 transition hover:-translate-y-1 hover:border-[#bd9360] hover:shadow-lg">
                    <span class="font-display text-3xl text-[#bd9360]">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h2 class="mt-8 font-display text-2xl text-[#302b26]">{{ $categoria->nombre }}</h2>
                    <p class="mt-2 text-xs text-[#8b8178]">{{ $categoria->libros_count }} libros en la colecci&oacute;n <span class="text-[#bd9360]">&rarr;</span></p>
                </a>
            @endforeach
        </div>
    </main>
</body>
</html>