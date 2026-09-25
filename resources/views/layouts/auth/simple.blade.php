<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['noFluxAppearance' => true])
        <script>
            try { localStorage.setItem('flux.appearance', 'light'); } catch (e) {}
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        </script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap');
            body.login-brand { font-family: 'Montserrat', sans-serif; }
            .card-premium {
                transition: 0.4s ease;
                background: white;
            }
            .card-premium:hover {
                transform: scale(1.01);
                box-shadow: 0 25px 50px -12px rgba(255, 102, 0, 0.25);
            }
            .nav-link:hover { color: #FF6600; }
            .login-bg {
                background:
                    radial-gradient(900px 420px at 85% -5%, rgba(255, 102, 0, 0.35), transparent 60%),
                    radial-gradient(700px 500px at 10% 110%, rgba(255, 255, 255, 0.14), transparent 60%),
                    linear-gradient(135deg, #002a80 0%, #003399 55%, #001f5c 100%);
            }
            .login-grid {
                background-image: radial-gradient(rgba(255, 255, 255, 0.16) 1px, transparent 1px);
                background-size: 22px 22px;
                mask-image: radial-gradient(ellipse 90% 80% at 50% 40%, black 30%, transparent 75%);
            }
        </style>
    </head>
    <body class="login-brand min-h-screen antialiased">
        <div class="login-bg relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden p-6 md:p-10">
            <div class="login-grid pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-[#FF6600]/30 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-24 size-96 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative flex w-full max-w-md flex-col gap-4">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3" wire:navigate>
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-2xl font-black text-[#003399] shadow-xl ring-4 ring-[#FF6600]/80">
                        86
                    </span>
                    <span class="text-xs font-bold tracking-[0.3em] text-orange-400 uppercase">CBTA 86 · Biblioteca</span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
                <p class="text-center text-xs font-semibold text-blue-200/80">
                    Sistema interno · Uso exclusivo del personal del plantel
                </p>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
