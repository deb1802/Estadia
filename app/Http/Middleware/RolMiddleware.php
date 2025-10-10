<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    /**
     * Manejar una solicitud entrante.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('mensaje', 'Debes iniciar sesión.');
        }

        $usuario = Auth::user();

        // Si el rol del usuario no está en la lista permitida
        if (!in_array($usuario->tipoUsuario, $roles)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('mensaje', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
