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
        // Adaptar la autenticación al campo 'contrasena' de tu tabla
        $request->merge(['password' => $request->contrasena]);

        $request->authenticate();
        $request->session()->regenerate();

        $usuario = Auth::user();

        // 🔁 Redirigir según tipo de usuario
        switch ($usuario->tipoUsuario) {
            case 'administrador':
                return redirect()->route('admin.dashboard'); // 👈 Página del administrador
            case 'medico':
                return redirect()->route('medico.dashboard'); // 👈 Página del médico
            case 'paciente':
                return redirect()->route('paciente.dashboard'); // 👈 Página del paciente
            default:
                Auth::logout();
                return redirect()->route('login')
                    ->with('mensaje', 'Tu cuenta no tiene un rol asignado. Contacta al administrador.');
        }
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
