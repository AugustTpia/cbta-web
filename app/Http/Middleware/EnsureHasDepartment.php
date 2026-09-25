<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autoriza por departamento. El rol `admin` tiene pase total.
 *
 * Uso: ->middleware('department:biblioteca') o
 * ->middleware('department:biblioteca,servicios_escolares')
 */
class EnsureHasDepartment
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$departments): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->isAdmin() || $user->hasAnyRole($departments)) {
            return $next($request);
        }

        abort(403, 'Sin acceso a este departamento.');
    }
}
