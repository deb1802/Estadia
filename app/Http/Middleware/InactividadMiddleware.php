<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InactividadMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $ultimaActividad = session('ultimaActividad');
            $ahora = now()->timestamp;

            // 1800 segundos = 30 minutos
            $limite = 1800;

            if ($ultimaActividad && ($ahora - $ultimaActividad > $limite)) {
                Auth::logout();
                session()->flush();

                return redirect()
                    ->route('login')
                    ->with('mensaje', 'Tu sesión se cerró por inactividad.');
            }

            session(['ultimaActividad' => $ahora]);
        }

        return $next($request);
    }
}
