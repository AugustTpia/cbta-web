<?php

namespace App\Shared\Auth;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * Redirección post-login según departamentos del usuario.
 *
 * - Sin roles → home de Fortify (/dashboard, conserva tests existentes).
 * - Admin (tenga los roles que tenga) → selector /hub.
 * - Un departamento → home de ese departamento (o /dashboard si el
 *   módulo aún no registra su ruta).
 * - Varios departamentos → selector /hub.
 */
class LoginResponse implements LoginResponseContract
{
    /**
     * @var array<string, string> slug de departamento => nombre de ruta home
     */
    private const DEPARTMENT_HOMES = [
        Role::BIBLIOTECA => 'biblioteca.dashboard',
        Role::SERVICIOS_ESCOLARES => 'servicios.dashboard',
    ];

    public function toResponse($request)
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        if ($user->isAdmin()) {
            return redirect()->intended(
                Route::has('hub') ? route('hub', absolute: false) : config('fortify.home', '/dashboard')
            );
        }

        $departments = $user->departmentSlugs();

        if ($departments === []) {
            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        if (count($departments) === 1) {
            $home = self::DEPARTMENT_HOMES[$departments[0]] ?? null;

            if ($home !== null && Route::has($home)) {
                return redirect()->intended(route($home, absolute: false));
            }

            return redirect()->intended(config('fortify.home', '/dashboard'));
        }

        if (Route::has('hub')) {
            return redirect()->intended(route('hub', absolute: false));
        }

        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
