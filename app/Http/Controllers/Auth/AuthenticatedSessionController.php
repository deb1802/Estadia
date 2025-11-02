<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la vista de inicio de sesión.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Manejar una solicitud de autenticación entrante.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    // ✅ Adaptar a tu formulario (contrasena -> password)
    $request->merge(['password' => $request->contrasena]);

    // Autentica (usa LoginRequest::authenticate)
    $request->authenticate();

    // Regenera la sesión
    $request->session()->regenerate();

    $usuario = Auth::user();

    // 🎯 A dónde queremos mandarlo por su rol:
    $destinoPorRol = match ($usuario->tipoUsuario) {
        'administrador' => route('admin.dashboard'),
        'medico'        => route('medico.dashboard'),
        'paciente'      => route('paciente.dashboard'),
        default         => null,
    };

    if (!$destinoPorRol) {
        Auth::logout();
        return redirect()
            ->route('login')
            ->with('mensaje', 'Tu cuenta no tiene un rol válido. Contacta al administrador.');
    }

    // 🔁 Usa intended para respetar el “querías ir a…”
    return redirect()->intended($destinoPorRol);
}


    /**
     * Cerrar la sesión autenticada (manual o por inactividad).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 🔒 Mostrar mensaje al volver al login (manual o por inactividad)
        return redirect()
            ->route('login')
            ->with('mensaje', 'Tu sesión se ha cerrado correctamente.');
    }
}
