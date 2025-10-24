<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class NotificacionesController extends Controller
{
    /**
     * GET /paciente/notificaciones/unread-count
     * Devuelve { unread: N } con el total de no leídas del usuario autenticado
     */
    public function unreadCount()
    {
        $userId = Auth::id(); // Debe ser Usuarios.idUsuario en tu app
        $unread = Notificacion::where('fkUsuario', $userId)
            ->where('leida', 0)
            ->count();

        return response()->json(['unread' => $unread]);
    }

    /**
     * GET /paciente/notificaciones
     * Lista paginada (últimas primero). Si pides JSON, devuelve JSON.
     * Luego haremos la vista/JS del modal en el siguiente paso.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $items = Notificacion::where('fkUsuario', $userId)
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        // ✅ Devuelve JSON si el cliente lo pide o si agregamos ?json=1
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->boolean('json')) {
            return response()->json($items);
        }

        // Vista completa (si navegas a /paciente/notificaciones en el navegador)
        return view('paciente.notificaciones.index', compact('items'));
    }



    /**
     * POST /paciente/notificaciones/{id}/leer
     * Marca UNA notificación como leída (solo si pertenece al usuario actual).
     */
    public function markRead(Request $request, $id)
{
    $userId = \Auth::id();

    $n = \App\Models\Notificacion::where('fkUsuario', $userId)
        ->where('idNotificacion', $id)
        ->firstOrFail();

    if (!$n->leida) {
        $n->leida = 1;
        $n->save();
    }

    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['ok' => true]);
    }

    return back()->with('success', 'Notificación marcada como leída.');
}

public function markAllRead(Request $request)
{
    $userId = \Auth::id();

    \App\Models\Notificacion::where('fkUsuario', $userId)
        ->where('leida', 0)
        ->update(['leida' => 1]);

    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['ok' => true]);
    }

    return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
}


    public function fragment()
{
    $userId = \Auth::id();

    $items = \App\Models\Notificacion::where('fkUsuario', $userId)
        ->orderBy('fecha', 'desc')
        ->paginate(10);

    // Devuelve SOLO el HTML de los <li> (sin layout)
    return view('paciente.notificaciones._list', compact('items'));
}

}
