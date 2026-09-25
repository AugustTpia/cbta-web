<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biblioteca') · CBTA 86</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap');
        body { font-family: 'Montserrat', sans-serif; }
        .card-premium { transition: 0.4s ease; background: white; }
        .card-premium:hover { transform: scale(1.01); box-shadow: 0 25px 50px -12px rgba(255, 102, 0, 0.25); }
        .nav-link { color: #003399; font-weight: 700; transition: 0.3s ease; }
        .nav-link:hover { color: #FF6600; }
        .nav-link.active { color: #FF6600; }
        .btn-primary { background: #003399; color: #fff; font-weight: 800; transition: 0.3s ease; }
        .btn-primary:hover { background: #FF6600; }
        .field { width: 100%; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.65rem 0.9rem; color: #0f172a; }
        .field:focus { outline: none; border-color: #003399; box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.15); }
        label.lbl { display: block; font-size: 0.8rem; font-weight: 800; color: #003399; margin-bottom: 0.3rem; text-transform: uppercase; letter-spacing: 0.05em; }
    </style>
</head>
<body class="min-h-screen bg-slate-100">
    <nav class="bg-[#003399] p-4 text-white shadow-md">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3">
            <a href="{{ route('biblioteca.dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-lg font-black text-[#003399] ring-2 ring-[#FF6600]">86</span>
                <span class="text-xs font-bold tracking-[0.25em] text-orange-400 uppercase">CBTA 86 · Biblioteca</span>
            </a>
            <div class="flex items-center gap-6 text-sm">
                <a href="{{ route('biblioteca.dashboard') }}" class="nav-link !text-white hover:!text-[#FF6600] {{ request()->routeIs('biblioteca.dashboard') ? '!text-[#FF6600]' : '' }}">Panel</a>
                <a href="{{ route('biblioteca.libros.index') }}" class="nav-link !text-white hover:!text-[#FF6600] {{ request()->routeIs('biblioteca.libros.*') ? '!text-[#FF6600]' : '' }}">Libros</a>
                <a href="{{ route('biblioteca.miembros.index') }}" class="nav-link !text-white hover:!text-[#FF6600] {{ request()->routeIs('biblioteca.miembros.*') ? '!text-[#FF6600]' : '' }}">Miembros</a>
                <a href="{{ route('biblioteca.prestamos.index') }}" class="nav-link !text-white hover:!text-[#FF6600] {{ request()->routeIs('biblioteca.prestamos.*') ? '!text-[#FF6600]' : '' }}">Préstamos</a>
                <a href="{{ route('biblioteca.reportes.index') }}" class="nav-link !text-white hover:!text-[#FF6600] {{ request()->routeIs('biblioteca.reportes.*') ? '!text-[#FF6600]' : '' }}">Reportes</a>
                <a href="{{ route('hub') }}" class="rounded-full bg-white/10 px-4 py-1.5 font-bold transition hover:bg-white/20">Hub</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-bold text-orange-300 transition hover:text-orange-400">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-6 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-bold text-green-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-bold text-red-700">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
