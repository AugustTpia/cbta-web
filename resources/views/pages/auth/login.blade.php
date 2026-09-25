<x-layouts::auth :title="__('Entrar')">
    <style>
        .card-premium, .card-premium [data-flux-label] { color: #0f172a; }
        .card-premium [data-flux-description], .card-premium [data-flux-error] { color: #475569; }
        .card-premium [data-flux-control] {
            background-color: #fff;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .card-premium input::placeholder { color: #94a3b8; opacity: 1; }
    </style>
    <div class="card-premium rounded-3xl border-t-8 border-[#FF6600] p-8 text-slate-900 shadow-2xl">
        <div class="flex flex-col gap-6">
            <div class="text-center">
                <p class="mb-1 text-xs font-black tracking-[0.25em] text-[#FF6600] uppercase">Biblioteca escolar</p>
                <h1 class="text-3xl font-black text-[#003399]">Bienvenido</h1>
                <p class="mt-1 text-sm font-semibold text-slate-500">Ingresa con tu correo y contraseña para continuar</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
                @csrf

                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Correo electrónico')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="usuario@cbta86.edu.mx"
                />

                <!-- Password -->
                <div class="relative">
                    <flux:input
                        name="password"
                        :label="__('Contraseña')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Tu contraseña')"
                        viewable
                    />

                    @if (Route::has('password.request'))
                        <flux:link class="nav-link absolute top-0 text-sm font-bold text-[#003399] end-0" :href="route('password.request')" wire:navigate>
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </flux:link>
                    @endif
                </div>

                <!-- Remember Me -->
                <flux:checkbox name="remember" :label="__('Mantener sesión iniciada')" :checked="old('remember')" />

                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full !bg-[#003399] py-3 text-base font-black tracking-wide transition duration-300 hover:!bg-[#FF6600]"
                    data-test="login-button"
                >
                    {{ __('Entrar') }}
                </flux:button>
            </form>

            <div class="rounded-2xl bg-blue-50 px-4 py-3 text-center text-xs font-semibold text-[#003399]">
                ¿Necesitas una cuenta? Solicítala al administrador del sistema.
            </div>

            <div class="text-center text-sm">
                <flux:link class="nav-link font-bold text-[#003399]" :href="route('home')" wire:navigate>
                    ← Volver al inicio
                </flux:link>
            </div>
        </div>
    </div>
</x-layouts::auth>
