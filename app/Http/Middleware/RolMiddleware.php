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

        // Normaliza: quita espacios, pasa a minúsculas y remueve acentos
        $normalize = function (string $v): string {
            $v = trim($v);
            $v = mb_strtolower($v, 'UTF-8');
            // Remover acentos (ñ se mantiene)
            $v = strtr($v, [
                'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
                'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
            ]);
            return $v;
        };

        $userRole    = $normalize((string) $usuario->tipoUsuario);
        $allowedRoles = array_map($normalize, $roles);

        // Si el rol del usuario no está en la lista permitida
        if (!in_array($userRole, $allowedRoles, true)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('mensaje', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
