<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Biblioteca ArquiSoft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3eb] font-sans text-[#27231f] antialiased">
    <main class="mx-auto flex min-h-screen max-w-md items-center px-6">
        <form action="{{ route('login.store') }}" method="POST" class="w-full rounded-2xl bg-[#fbf8f3] p-8 shadow-sm">
            @csrf
            <a href="{{ url('/') }}" class="font-display text-xl text-[#bd9360]">Biblioteca ArquiSoft</a>
            <h1 class="mt-8 font-display text-4xl text-[#302b26]">Iniciar sesión</h1>
            <p class="mt-3 text-sm text-[#716960]">Acceso exclusivo para administrador, bibliotecario y cajero.</p>
            @if ($errors->any())<div class="mt-5 rounded-lg bg-rose-50 p-4 text-sm text-rose-700">{{ $errors->first() }}</div>@endif
            <label class="mt-8 block text-sm text-[#716960]">Correo<input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3"></label>
            <label class="mt-4 block text-sm text-[#716960]">Contraseña<input name="password" type="password" required class="mt-2 w-full rounded-lg border border-[#ded3c6] bg-white px-4 py-3"></label>
            <button class="mt-8 w-full rounded-full bg-[#24211f] px-6 py-4 text-xs font-bold uppercase tracking-[0.14em] text-white">Ingresar</button>
        </form>
    </main>
</body>
</html>
