<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar departamento · CBTA 86</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap');
        body { font-family: 'Montserrat', sans-serif; }
        .card-premium { transition: 0.4s ease; background: white; }
        .card-premium:hover { transform: scale(1.03); box-shadow: 0 25px 50px -12px rgba(255, 102, 0, 0.25); }
        .nav-link:hover { color: #FF6600; }
    </style>
</head>
<body class="min-h-screen bg-slate-100">
    <nav class="bg-[#003399] p-6 text-white shadow-md">
        <div class="mx-auto flex max-w-5xl items-center justify-between">
            <div>
                <p class="text-xs font-bold tracking-[0.3em] text-orange-400 uppercase">CBTA 86</p>
                <h1 class="text-2xl font-black">Hola, {{ auth()->user()->name }}</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link rounded-full bg-white/10 px-5 py-2 text-sm font-bold transition">Salir</button>
            </form>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-6 py-12">
        <h2 class="mb-2 text-3xl font-black text-[#003399]">{{ auth()->user()->isAdmin() ? '¿Dónde quieres entrar?' : '¿A qué departamento entras hoy?' }}</h2>
        <p class="mb-8 font-semibold text-slate-500">{{ auth()->user()->isAdmin() ? 'Como administrador puedes entrar a cualquier departamento o al panel de administración.' : 'Tienes acceso a más de un departamento. Elige uno para continuar.' }}</p>

        <div class="grid gap-6 md:grid-cols-2">
            @if (auth()->user()->isAdmin())
                <div class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg opacity-60">
                    <div class="mb-3 text-4xl">🎛️</div>
                    <h3 class="mb-1 text-xl font-black text-[#003399]">Administración</h3>
                    <p class="text-sm font-semibold text-slate-500">Próximamente: gestión de usuarios y departamentos.</p>
                </div>
            @endif
            @if (auth()->user()->hasAnyRole(['biblioteca', 'admin']))
                <a href="{{ route('biblioteca.dashboard') }}" class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg">
                    <div class="mb-3 text-4xl">📚</div>
                    <h3 class="mb-1 text-xl font-black text-[#003399]">Biblioteca</h3>
                    <p class="text-sm font-semibold text-slate-500">Libros, miembros, préstamos y reportes.</p>
                </a>
            @endif
            @if (auth()->user()->hasAnyRole(['servicios_escolares', 'admin']))
                <div class="card-premium rounded-3xl border-t-8 border-orange-500 p-8 shadow-lg opacity-60">
                    <div class="mb-3 text-4xl">🏫</div>
                    <h3 class="mb-1 text-xl font-black text-[#003399]">Servicios Escolares</h3>
                    <p class="text-sm font-semibold text-slate-500">Próximamente.</p>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
