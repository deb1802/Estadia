<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            $user = Auth::guard($guard)->user();

            // 🧭 Redirección por rol si ya está logueado e intenta ir a /login
            return match ($user->tipoUsuario) {
                'administrador' => redirect()->route('admin.dashboard'),
                'medico'        => redirect()->route('medico.dashboard'),
                'paciente'      => redirect()->route('paciente.dashboard'),
                default         => redirect('/'), // fallback
            };
        }
    }

    return $next($request);
}

}
